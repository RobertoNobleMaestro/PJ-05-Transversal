<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role; // Import Role model
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;
use App\Models\Asalariado;

class GestorUserController extends Controller
{
    // Método privado para verificar si el usuario es administrador
    private function checkGestor($request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        if (auth()->user()->id_roles !== 3 && auth()->user()->id_roles !== 1) {
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
        $authCheck = $this->checkGestor($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
    
        $gestor = auth()->user();
        $asalariadoGestor = Asalariado::where('id_usuario', $gestor->id_usuario)->first();

        if (!$asalariadoGestor || !$asalariadoGestor->parking_id) {
            return response()->json([
                'users' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'per_page' => 10,
                ],
                'message' => 'No se ha encontrado un parking asignado a este gestor.'
            ]);
        }

        // Obtener el id_lugar del parking del gestor
        $parkingGestor = \App\Models\Parking::find($asalariadoGestor->parking_id);
        if (!$parkingGestor) {
            return response()->json([
                'users' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'per_page' => 10,
                ],
                'message' => 'No se ha encontrado el parking del gestor.'
            ]);
        }
        $id_lugar = $parkingGestor->id_lugar;

        $query = User::select('users.*', 'roles.nombre as nombre_rol', 'parking.id as parking_id', 'parking.nombre as parking_nombre')
                    ->leftJoin('roles', 'users.id_roles', '=', 'roles.id_roles')
                    ->join('asalariados', 'asalariados.id_usuario', '=', 'users.id_usuario')
                    ->join('parking', 'asalariados.parking_id', '=', 'parking.id')
                    ->where('parking.id_lugar', $id_lugar);
    
        if ($request->filled('nombre')) {
            $query->where('users.nombre', 'like', '%' . $request->nombre . '%');
        }
    
        if ($request->filled('role')) {
            $query->where('users.id_roles', $request->role);
        }
    
        if ($request->filled('parking_id')) {
            $query->where('parking.id', $request->parking_id);
        }
    
        if ($request->filled('email')) {
            $query->where('users.email', 'like', '%' . $request->email . '%');
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
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        $gestor = auth()->user();
        $asalariadoGestor = Asalariado::where('id_usuario', $gestor->id_usuario)->first();
        $roles = Role::whereIn('nombre', ['Mecánico', 'admin_financiero', 'chofer'])->get();

        if (!$asalariadoGestor || !$asalariadoGestor->parking_id) {
            return view('gestor.user.index', [
                'users' => collect([]),
                'roles' => $roles,
                'parkings' => collect([]),
                'message' => 'No se ha encontrado un parking asignado a este gestor.'
            ]);
        }

        $parkingGestor = \App\Models\Parking::find($asalariadoGestor->parking_id);
        if (!$parkingGestor) {
            return view('gestor.user.index', [
                'users' => collect([]),
                'roles' => $roles,
                'parkings' => collect([]),
                'message' => 'No se ha encontrado el parking del gestor.'
            ]);
        }
        $id_lugar = $parkingGestor->id_lugar;

        $parkings = \App\Models\Parking::where('id_lugar', $id_lugar)->get();

        $users = User::select('users.*', 'roles.nombre as nombre_rol', 'parking.id as parking_id', 'parking.nombre as parking_nombre')
                    ->leftJoin('roles', 'users.id_roles', '=', 'roles.id_roles')
                    ->join('asalariados', 'asalariados.id_usuario', '=', 'users.id_usuario')
                    ->join('parking', 'asalariados.parking_id', '=', 'parking.id')
                    ->where('parking.id_lugar', $id_lugar)
                    ->get();

        return view('gestor.user.index', compact('users', 'roles', 'parkings'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) return $authCheck;

        $usuario = new User();
        $licencias = ['AM','A1','A2','A','B','B+E','C1','C1+E','C','C+E','D1','D1+E','D','D+E'];
        $roles = Role::whereIn('nombre', ['Mecánico', 'admin_financiero', 'chofer'])->get();

        // Parkings del lugar del gestor
        $gestor = auth()->user();
        $asalariadoGestor = Asalariado::where('id_usuario', $gestor->id_usuario)->first();
        $parkings = [];
        if ($asalariadoGestor && $asalariadoGestor->parking_id) {
            $parkingGestor = \App\Models\Parking::find($asalariadoGestor->parking_id);
            if ($parkingGestor) {
                $parkings = \App\Models\Parking::where('id_lugar', $parkingGestor->id_lugar)->get();
            }
        }

        return view('gestor.user.add_user', compact('usuario', 'licencias', 'roles', 'parkings'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) return $authCheck;
    
        DB::beginTransaction();
        try {
            $validatedUser = $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'dni' => 'required|string|regex:/^\d{8}[A-Z]$/|max:9',
                'telefono' => 'required|string|regex:/^\d{9}$/',
                'fecha_nacimiento' => 'required|date|before_or_equal:' . now()->subYears(16)->format('Y-m-d'),
                'direccion' => 'required|string|min:5|max:255',
                'licencia_conducir' => 'nullable|string|max:5',
                'id_roles' => 'required|integer',
            ]);
            $validatedUser['password'] = Hash::make($validatedUser['password']);
            $user = User::create($validatedUser);
    
            $validatedAsalariado = $request->validate([
                'salario' => 'required|numeric|min:0',
                'dia_cobro' => 'required|integer|min:1|max:31',
                'parking_id' => 'required|exists:parking,id'
            ]);
            $validatedAsalariado['id_usuario'] = $user->id_usuario;
            Asalariado::create($validatedAsalariado);
    
            DB::commit();
    
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Usuario asalariado creado correctamente.'
                ]);
            }
    
            return redirect()->route('gestor.user.index')->with('success', 'Usuario asalariado creado correctamente');
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
    
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $e->errors()
                ], 422);
            }
    
            return back()->withInput()->withErrors($e->errors());
    
        } catch (\Exception $e) {
            DB::rollBack();
    
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear usuario: ' . $e->getMessage()
                ], 500);
            }
    
            return back()->withInput()->withErrors(['error' => 'Error al crear usuario: ' . $e->getMessage()]);
        }
    }
    

    public function edit(Request $request, $id_usuario)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) return $authCheck;

        $user = User::findOrFail($id_usuario);
        $asalariado = Asalariado::where('id_usuario', $user->id_usuario)->firstOrFail();
        $licencias = ['AM','A1','A2','A','B','B+E','C1','C1+E','C','C+E','D1','D1+E','D','D+E'];
        $roles = Role::whereIn('nombre', ['Mecánico', 'admin_financiero', 'chofer'])->get();

        // Parkings del lugar del gestor
        $gestor = auth()->user();
        $asalariadoGestor = Asalariado::where('id_usuario', $gestor->id_usuario)->first();
        $parkings = [];
        if ($asalariadoGestor && $asalariadoGestor->parking_id) {
            $parkingGestor = \App\Models\Parking::find($asalariadoGestor->parking_id);
            if ($parkingGestor) {
                $parkings = \App\Models\Parking::where('id_lugar', $parkingGestor->id_lugar)->get();
            }
        }

        return view('gestor.user.edit_user', compact('user', 'asalariado', 'licencias', 'roles', 'parkings'));
    }

    public function update(Request $request, $id_usuario)
{
    $authCheck = $this->checkGestor($request);
    if ($authCheck) return $authCheck;

    DB::beginTransaction();
    try {
        $user = User::findOrFail($id_usuario);
        $asalariado = Asalariado::where('id_usuario', $id_usuario)->firstOrFail();

        // Validar datos del usuario
        $validatedUser = $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id_usuario . ',id_usuario',
            'password' => 'nullable|string|min:8',
            'telefono' => 'required|string|regex:/^\d{9}$/',
            'fecha_nacimiento' => 'required|date|before_or_equal:' . date('Y-m-d', strtotime('-16 years')),
            'direccion' => 'required|string|min:5|max:255',
            'licencia_conducir' => 'nullable|string|max:5',
            'id_roles' => 'required|integer',
        ]);

        if (!empty($validatedUser['password'])) {
            $validatedUser['password'] = Hash::make($validatedUser['password']);
        } else {
            unset($validatedUser['password']);
        }

        $user->update($validatedUser);

        // Validar datos del asalariado
        $validatedAsalariado = $request->validate([
            'salario' => 'required|numeric|min:0',
            'dia_cobro' => 'required|integer|min:1|max:31',
            'parking_id' => 'required|exists:parking,id',
        ]);

        $asalariado->update($validatedAsalariado);

        DB::commit();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Usuario actualizado correctamente.'
            ]);
        }

        return redirect()->route('gestor.user.index')->with('success', 'Usuario actualizado correctamente');

    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);
        }

        return back()->withInput()->withErrors($e->errors());

    } catch (\Exception $e) {
        DB::rollBack();

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al actualizar usuario: ' . $e->getMessage()
            ], 500);
        }

        return back()->withInput()->withErrors(['error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
    }
}


    public function destroy(Request $request, $id_usuario)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            if ($request->expectsJson()) {
                return $authCheck;
            }
            return $authCheck;
        }
        
        try {
            DB::beginTransaction();
            Pago::where('id_usuario', $id_usuario)->delete();
            User::findOrFail($id_usuario)->delete();
            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Usuario eliminado correctamente'
                ]);
            }

            return redirect()->route('gestor.user.index')->with('success', 'Usuario eliminado correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar el usuario: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->route('gestor.user.index')->with('error', 'Error al eliminar el usuario');
        }
    }
}
