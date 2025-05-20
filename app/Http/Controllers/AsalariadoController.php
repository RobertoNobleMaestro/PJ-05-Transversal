<?php

namespace App\Http\Controllers;

use App\Models\Asalariado;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class AsalariadoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // El control de acceso ahora se gestiona mediante middleware en las rutas (routes/web.php)
    }
    
    /**
     * Método para obtener asalariados en formato JSON (para AJAX)
     */
    public function getAsalariados(Request $request)
    {
        try {
            // Verificar que el usuario sea admin financiero
            if (!Auth::check() || Auth::user()->id_roles !== 5) {
                return response()->json([
                    'error' => 'No tienes permiso para acceder a esta sección',
                    'asalariados' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'total' => 0,
                        'per_page' => 10,
                    ]
                ], 403);
            }
            
            // Obtener el admin financiero actual
            $adminFinanciero = Auth::user();
            $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
            
            if (!$asalariadoAdmin || !$asalariadoAdmin->parking_id) {
                return response()->json([
                    'error' => 'Necesitas tener un parking asignado para gestionar asalariados',
                    'asalariados' => [],
                    'pagination' => [
                        'current_page' => 1,
                        'last_page' => 1,
                        'total' => 0,
                        'per_page' => 10,
                    ]
                ], 403);
            }
            
            // Obtener el parking asociado al admin financiero
            $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
            if (!$parkingAdmin) {
                throw new \Exception('Parking no encontrado');
            }
            
            $sedeId = $parkingAdmin->id_lugar;
            
            // Obtener la sede (lugar)
            $sede = Lugar::find($sedeId);
            if (!$sede) {
                throw new \Exception('Sede no encontrada');
            }
            
            // Obtener todos los parkings de la misma sede
            $parkingsDeSedeIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
            
            // Iniciar la consulta base
            $query = Asalariado::whereIn('parking_id', $parkingsDeSedeIds)
                ->join('users', 'asalariados.id_usuario', '=', 'users.id_usuario')
                ->join('roles', 'users.id_roles', '=', 'roles.id_roles')
                ->join('parking', 'asalariados.parking_id', '=', 'parking.id')
                ->select(
                    'asalariados.id',
                    'asalariados.id_usuario',
                    'asalariados.salario',
                    'asalariados.dia_cobro',
                    'asalariados.parking_id',
                    'users.nombre',
                    'roles.nombre as nombre_rol',
                    'parking.nombre as nombre_parking'
                );
                
            // Aplicar filtros si existen
            if ($request->filled('nombre')) {
                $query->where('users.nombre', 'like', '%' . $request->nombre . '%');
            }
            
            if ($request->filled('rol')) {
                $query->where('roles.nombre', $request->rol);
            }
            
            if ($request->filled('parking')) {
                $query->where('parking.id', $request->parking);
            }
            
            // Paginación
            $perPage = (int) $request->input('perPage', 10);
            $asalariados = $query->paginate($perPage);
            
            // Devolver datos en formato JSON
            return response()->json([
                'asalariados' => $asalariados->items() ?? [],
                'sede' => $sede->nombre ?? 'Desconocida',
                'total_count' => $asalariados->total() ?? 0,
                'pagination' => [
                    'current_page' => $asalariados->currentPage() ?? 1,
                    'last_page' => $asalariados->lastPage() ?? 1,
                    'total' => $asalariados->total() ?? 0,
                    'per_page' => $asalariados->perPage() ?? $perPage,
                ]
            ]);
        } catch (\Exception $e) {
            // En caso de error, devolver una estructura JSON válida
            return response()->json([
                'error' => 'Ha ocurrido un error: ' . $e->getMessage(),
                'asalariados' => [],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'per_page' => 10,
                ]
            ], 500);
        }
    }

    /**
     * Muestra la lista de asalariados de la misma sede que el admin financiero
     */
    public function index()
    {
        // Obtener el admin financiero actual
        $adminFinanciero = Auth::user();
        
        // Verificar si el admin financiero tiene información de asalariado
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        
        if (!$asalariadoAdmin || !$asalariadoAdmin->parking_id) {
            return redirect()->route('home')->with('error', 'Necesitas tener un parking asignado para gestionar asalariados');
        }
        
        // Obtener el parking asociado al admin financiero
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener la sede (lugar)
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings de la misma sede para el filtro
        $parkings = Parking::where('id_lugar', $sedeId)->get();
        
        // Obtener roles para el filtro
        $roles = ['gestor', 'mecanico', 'admin_financiero'];
        
        // Configurar este flag para mostrar detalles de debug si es necesario
        $debug = true;
        
        $data = [
            'sede' => $sede,
            'parkings' => $parkings,
            'roles' => $roles,
            'debug' => $debug
        ];
        
        return view('admin_financiero.asalariados.index', $data);
    }

    /**
     * Muestra el formulario para editar un asalariado
     */
    public function edit($id)
    {
        // Verificar que el asalariado pertenezca a la misma sede que el admin financiero
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener el asalariado
        $asalariado = Asalariado::findOrFail($id);
        $parkingAsalariado = Parking::find($asalariado->parking_id);
        
        // Verificar que pertenezca a la misma sede
        if ($parkingAsalariado->id_lugar != $sedeId) {
            return redirect()->route('asalariados.index')
                ->with('error', 'No puedes editar asalariados de otras sedes');
        }
        
        // Obtener usuario y datos relacionados
        $usuario = User::find($asalariado->id_usuario);
        $sede = Lugar::find($sedeId);
        
        // Obtener parkings de la sede para el selector
        $parkingsDisponibles = Parking::where('id_lugar', $sedeId)->get();
        
        return view('admin_financiero.asalariados.edit', [
            'asalariado' => $asalariado,
            'usuario' => $usuario,
            'sede' => $sede,
            'parkings' => $parkingsDisponibles
        ]);
    }

    /**
     * Actualiza los datos del asalariado
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $request->validate([
            'salario' => 'required|numeric|min:0',
            'dia_cobro' => 'required|integer|min:1|max:31',
            'parking_id' => 'required|exists:parking,id'
        ], [
            'salario.required' => 'El salario es obligatorio',
            'salario.numeric' => 'El salario debe ser un número',
            'salario.min' => 'El salario no puede ser negativo',
            'dia_cobro.required' => 'El día de cobro es obligatorio',
            'dia_cobro.integer' => 'El día de cobro debe ser un número entero',
            'dia_cobro.min' => 'El día de cobro debe ser al menos 1',
            'dia_cobro.max' => 'El día de cobro no puede ser mayor a 31',
            'parking_id.required' => 'El parking es obligatorio',
            'parking_id.exists' => 'El parking seleccionado no existe'
        ]);
        
        // Verificar que el admin financiero tenga permisos sobre este asalariado
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Verificar que el nuevo parking pertenezca a la misma sede
        $nuevoParking = Parking::find($request->parking_id);
        if ($nuevoParking->id_lugar != $sedeId) {
            return redirect()->route('asalariados.edit', $id)
                ->with('error', 'El parking seleccionado no pertenece a tu sede');
        }
        
        // Actualizar asalariado
        $asalariado = Asalariado::findOrFail($id);
        $asalariado->salario = $request->salario;
        $asalariado->dia_cobro = $request->dia_cobro;
        $asalariado->parking_id = $request->parking_id;
        $asalariado->save();
        
        return redirect()->route('asalariados.index')
            ->with('success', 'Información de asalariado actualizada correctamente');
    }
    
    /**
     * Actualiza los datos del asalariado mediante AJAX
     */
    public function updateAjax(Request $request, $id)
    {
        // Validación de datos
        $validator = Validator::make($request->all(), [
            'salario' => 'required|numeric|min:0',
            'dia_cobro' => 'required|integer|min:1|max:31',
            'parking_id' => 'required|exists:parking,id'
        ], [
            'salario.required' => 'El salario es obligatorio',
            'salario.numeric' => 'El salario debe ser un número',
            'salario.min' => 'El salario no puede ser negativo',
            'dia_cobro.required' => 'El día de cobro es obligatorio',
            'dia_cobro.integer' => 'El día de cobro debe ser un número entero',
            'dia_cobro.min' => 'El día de cobro debe ser al menos 1',
            'dia_cobro.max' => 'El día de cobro no puede ser mayor a 31',
            'parking_id.required' => 'El parking es obligatorio',
            'parking_id.exists' => 'El parking seleccionado no existe'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            // Verificar que el admin financiero tenga permisos sobre este asalariado
            $adminFinanciero = Auth::user();
            $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
            $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
            $sedeId = $parkingAdmin->id_lugar;
            
            // Verificar que el nuevo parking pertenezca a la misma sede
            $nuevoParking = Parking::find($request->parking_id);
            if ($nuevoParking->id_lugar != $sedeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'El parking seleccionado no pertenece a tu sede'
                ], 403);
            }
            
            // Actualizar asalariado
            $asalariado = Asalariado::findOrFail($id);
            $asalariado->salario = $request->salario;
            $asalariado->dia_cobro = $request->dia_cobro;
            $asalariado->parking_id = $request->parking_id;
            $asalariado->save();
            
            // Obtener el nombre del parking para la respuesta
            $parking = Parking::find($request->parking_id);
            
            return response()->json([
                'success' => true,
                'message' => 'Información de asalariado actualizada correctamente',
                'data' => [
                    'asalariado' => $asalariado,
                    'parking_nombre' => $parking->nombre
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Muestra detalles específicos del asalariado
     */
    public function show($id)
    {
        // Verificar que el asalariado pertenezca a la misma sede que el admin financiero
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener el asalariado
        $asalariado = Asalariado::findOrFail($id);
        $parkingAsalariado = Parking::find($asalariado->parking_id);
        
        // Verificar que pertenezca a la misma sede
        if ($parkingAsalariado->id_lugar != $sedeId) {
            return redirect()->route('asalariados.index')
                ->with('error', 'No puedes ver detalles de asalariados de otras sedes');
        }
        
        // Obtener usuario y datos relacionados
        $usuario = User::find($asalariado->id_usuario);
        $parking = Parking::find($asalariado->parking_id);
        $sede = Lugar::find($sedeId);
        
        return view('admin_financiero.asalariados.show', [
            'asalariado' => $asalariado,
            'usuario' => $usuario,
            'parking' => $parking,
            'sede' => $sede
        ]);
    }
}
