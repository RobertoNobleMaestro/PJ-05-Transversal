<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Import Role model
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // Método privado para verificar si el usuario es administrador
    private function checkAdmin($request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario tiene rol de administrador (id_roles = 1)
        if (auth()->user()->id_roles !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar
    }
    
    // Método para obtener usuarios en formato JSON (para AJAX)
    public function getUsers(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
    
        $query = User::select('users.*', 'roles.nombre as nombre_rol')
                    ->leftJoin('roles', 'users.id_roles', '=', 'roles.id_roles');
    
        if ($request->filled('nombre')) {
            $query->where('users.nombre', 'like', '%' . $request->nombre . '%');
        }
    
        if ($request->filled('role')) {
            $query->where('users.id_roles', $request->role);
        }
    
        $perPage = $request->get('perPage', 10);
        $users = $query->paginate($perPage);
    
        return response()->json([
            'users' => $users->items(),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'total' => $users->total(),
                'per_page' => $users->perPage(),
            ]
        ]);
    }
    
    
    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener todos los roles para el filtro
        $roles = Role::all();
        
        // Cargar usuarios con su información de rol usando join
        $users = User::select('users.*', 'roles.nombre as nombre_rol')
                    ->leftJoin('roles', 'users.id_roles', '=', 'roles.id_roles')
                    ->get();

        return view('admin.users', compact('users', 'roles'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        $usuario = new User();
        $licencias = array('AM','A1','A2','A','B','B+E','C1','C1+E','C','C+E','D1','D1+E','D','D+E');
        return view('admin.add_user' , compact('usuario', 'licencias'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'dni' => 'required|string|regex:/^\d{8}[A-Z]$/|max:9',
                'telefono' => 'required|string|regex:/^\d{9}$/',
                'fecha_nacimiento' => 'required|date|before_or_equal:'.date('Y-m-d', strtotime('-16 years')),
                'direccion' => 'required|string|min:5|max:255',
                'licencia_conducir' => 'nullable|string|max:5',
                'id_roles' => 'required|integer',
            ]);

            $validatedData['password'] = Hash::make($validatedData['password']);

            User::create($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Usuario añadido correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.users')->with('success', 'Usuario añadido correctamente');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear el usuario',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(Request $request, $id_usuario)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        $user = User::findOrFail($id_usuario);

        $licencias = array('AM','A1','A2','A','B','B+E','C1','C1+E','C','C+E','D1','D1+E','D','D+E');
        return view('admin.edit_user', compact('user', 'licencias'));
    }

    public function update(Request $request, $id_usuario)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            // Obtener datos dependiendo del Content-Type
            $data = $request->all();
            
            $user = User::findOrFail($id_usuario);
            
            $validatedData = validator($data, [
                'nombre' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id_usuario . ',id_usuario',
                'password' => 'nullable|string|min:8',
                'telefono' => 'required|string|regex:/^\d{9}$/',
                'fecha_nacimiento' => 'required|date|before_or_equal:'.date('Y-m-d', strtotime('-16 years')),
                'direccion' => 'required|string|min:5|max:255',
                'licencia_conducir' => 'nullable|string|max:5',
                'id_roles' => 'required|integer',
            ])->validate();

            if (!empty($validatedData['password'])) {
                $validatedData['password'] = Hash::make($validatedData['password']);
            } else {
                unset($validatedData['password']);
            }

            $user->update($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Usuario actualizado correctamente'
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar el usuario: ' . $e->getMessage(),
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Request $request, $id_usuario)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            if ($request->expectsJson()) {
                return $authCheck;
            }
            return $authCheck;
        }
        
        try {
            DB::beginTransaction();

            // 1. Eliminar valoraciones hechas por el usuario (como autor)
            DB::table('valoraciones')->where('id_usuario', $id_usuario)->delete();

            // 2. Eliminar solicitudes_chofer donde el usuario es el solicitante
            DB::table('solicitudes_chofer')->where('id_usuario', $id_usuario)->delete();

            // 3. Eliminar solicitudes donde el usuario es cliente
            $solicitudesIds = \App\Models\Solicitud::where('id_cliente', $id_usuario)->pluck('id');
            if ($solicitudesIds->count() > 0) {
                // 3.1 Eliminar pagos_choferes de esas solicitudes
                DB::table('pagos_choferes')->whereIn('solicitud_id', $solicitudesIds)->delete();
                // 3.2 Eliminar las solicitudes
                \App\Models\Solicitud::whereIn('id', $solicitudesIds)->delete();
            }

            // 4. Eliminar reservas y todas sus dependencias (como ya tienes implementado)
            $reservasIds = \App\Models\Reserva::where('id_usuario', $id_usuario)->pluck('id_reservas');
            if ($reservasIds->count() > 0) {
                DB::table('valoraciones')->whereIn('id_reservas', $reservasIds)->delete();
                DB::table('vehiculos_reservas')->whereIn('id_reservas', $reservasIds)->delete();
                $pagosReservasIds = \App\Models\Pago::whereIn('id_reservas', $reservasIds)->pluck('id_pago');
                if ($pagosReservasIds->count() > 0) {
                    DB::table('metodos_de_pago')->whereIn('id_pago', $pagosReservasIds)->delete();
                }
                \App\Models\Pago::whereIn('id_reservas', $reservasIds)->delete();
                \App\Models\Reserva::where('id_usuario', $id_usuario)->delete();
            }

            // 5. Eliminar métodos de pago y pagos del usuario
            $pagosIds = \App\Models\Pago::where('id_usuario', $id_usuario)->pluck('id_pago');
            if ($pagosIds->count() > 0) {
                DB::table('metodos_de_pago')->whereIn('id_pago', $pagosIds)->delete();
            }
            \App\Models\Pago::where('id_usuario', $id_usuario)->delete();

            // 6. Eliminar grupo_usuario
            DB::table('grupo_usuario')->where('id_usuario', $id_usuario)->delete();

            // 7. Eliminar asalariado
            \App\Models\Asalariado::where('id_usuario', $id_usuario)->delete();

            // 8. Eliminar parkings y dependencias (si es gestor)
            $parkingsIds = \App\Models\Parking::where('id_usuario', $id_usuario)->pluck('id');
            if ($parkingsIds->count() > 0) {
                $vehiculosIds = \App\Models\Vehiculo::whereIn('parking_id', $parkingsIds)->pluck('id_vehiculos');
                if ($vehiculosIds->count() > 0) {
                    DB::table('caracteristicas')->whereIn('id_vehiculos', $vehiculosIds)->delete();
                    DB::table('imagen_vehiculo')->whereIn('id_vehiculo', $vehiculosIds)->delete();
                    DB::table('vehiculos_reservas')->whereIn('id_vehiculos', $vehiculosIds)->delete();
                    DB::table('pedido_piezas')->whereIn('vehiculo_id', $vehiculosIds)->delete();
                    DB::table('mantenimientos')->whereIn('vehiculo_id', $vehiculosIds)->delete();
                    \App\Models\Vehiculo::whereIn('id_vehiculos', $vehiculosIds)->delete();
                }
                \App\Models\Parking::where('id_usuario', $id_usuario)->delete();
            }

            // 9. Eliminar choferes (si el usuario es chofer)
            \App\Models\Chofer::where('id_usuario', $id_usuario)->delete();

            // 10. Eliminar mensajes donde el usuario es remitente o gestor
            DB::table('messages')->where('user_id', $id_usuario)->orWhere('gestor_id', $id_usuario)->delete();

            // 11. Eliminar usuario
            \App\Models\User::findOrFail($id_usuario)->delete();

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Usuario eliminado correctamente'
                ]);
            }

            return redirect()->route('admin.users')->with('success', 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('admin.users')->with('error', 'Error al eliminar el usuario: ' . $e->getMessage());
        }
    }
}
