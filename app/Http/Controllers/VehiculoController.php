<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Reserva;
use App\Models\VehiculosReservas;
use Illuminate\Http\Request;
use App\Models\Lugar;
use App\Models\Tipo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class VehiculoController extends Controller
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
    
    // Método para mostrar detalles de un vehículo (para clientes)
    public function detalle($id)
    {
        $vehiculo = Vehiculo::with(['tipo', 'lugar', 'caracteristicas', 'valoraciones', 'vehiculosReservas.reserva'])
            ->findOrFail($id);

        $precioUnitario = $vehiculo->vehiculosReservas
            ->where('fecha_final', '>=', now())
            ->first()->precio_unitario ?? $vehiculo->precio_unitario;

        return view('vehiculos.detalle_vehiculo', [
            'vehiculo' => $vehiculo,
            'precio_unitario' => $precioUnitario
        ]);
    }

    // Método para añadir un vehículo al carrito (para clientes)
    public function añadirAlCarrito($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            $usuarioId = auth()->id();

            if (!$usuarioId) {
                return response()->json(['alert' => [
                    'icon' => 'error',
                    'title' => 'Usuario no autenticado',
                    'text' => 'Debes iniciar sesión para añadir vehículos al carrito.'
                ]]);
            }

            // 1. Buscar o crear una reserva activa ("carrito") para este usuario
            $reserva = Reserva::firstOrCreate(
                [
                    'estado' => 'pendiente',
                    'id_usuario' => $usuarioId
                ],
                [
                    'fecha_reserva' => now(),
                    'total_precio' => 0, // Se puede recalcular luego
                    'id_lugar' => $vehiculo->id_lugar,
                ]
            );

            // 2. Obtener el precio unitario del vehículo desde la tabla vehiculos_reservas
            $precioUnitario = VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)
                ->where('id_reservas', $reserva->id_reservas)
                ->first()->precio_unitario ?? 100; // Usamos 100 como valor por defecto si no se encuentra el precio.

            // 3. Insertar el vehículo en vehiculos_reservas si aún no está
            $existe = VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)
                ->where('id_reservas', $reserva->id_reservas)
                ->exists();

            if ($existe) {
                return response()->json(['alert' => [
                    'icon' => 'warning',
                    'title' => '¡Vehículo ya en el carrito!',
                    'text' => 'Este vehículo ya está en tu carrito de compras.'
                ]]);
            }

            // Insertar la relación con el precio unitario obtenido
            VehiculosReservas::create([
                'fecha_ini' => now()->toDateString(),
                'fecha_final' => now()->addDays(3)->toDateString(),
                'precio_unitario' => $precioUnitario,  // Usar el precio unitario de la reserva
                'id_reservas' => $reserva->id_reservas,
                'id_vehiculos' => $vehiculo->id_vehiculos,
            ]);

            return response()->json(['alert' => [
                'icon' => 'success',
                'title' => '¡Vehículo añadido al carrito!',
                'text' => 'El vehículo ha sido añadido a tu carrito con éxito.'
            ]]);

        } catch (\Exception $e) {
            return response()->json(['alert' => [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un problema al intentar añadir el vehículo al carrito.'
            ]]);
        }
    }
    
    // MÉTODOS PARA EL PANEL DE ADMINISTRACIÓN
    
    // Método para obtener vehiculos en formato JSON (para AJAX)
    public function getVehiculos(Request $request)
    {
        try {
            // Verificar autenticación
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Usuario no autenticado'
                ], 401);
            }
            
            // Verificar que sea administrador
            if (auth()->user()->id_roles !== 1) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No tienes permiso para acceder a esta sección'
                ], 403);
            }
            
            // Determinar la página actual y el tamaño de página
            $page = $request->input('page', 1);
            $perPage = $request->input('per_page', 10); // Por defecto 10 items por página
            
            // Versión simplificada de la consulta para depuración
            $query = Vehiculo::select(
                'vehiculos.*', 
                'lugares.nombre as nombre_lugar', 
                'tipos.nombre_tipo'
            )
            ->leftJoin('lugares', 'vehiculos.id_lugar', '=', 'lugares.id_lugar')
            ->leftJoin('tipos', 'vehiculos.id_tipo', '=', 'tipos.id_tipo');
            
            // Aplicar filtros básicos
            if ($request->has('tipo') && !empty($request->tipo)) {
                $query->where('vehiculos.id_tipo', $request->tipo);
            }
            
            if ($request->has('lugar') && !empty($request->lugar)) {
                $query->where('vehiculos.id_lugar', $request->lugar);
            }
            
            if ($request->has('marca') && !empty($request->marca)) {
                $query->where('vehiculos.marca', 'like', '%' . $request->marca . '%');
            }
            
            if ($request->has('anio') && !empty($request->anio)) {
                $query->where('vehiculos.año', $request->anio);
            }
            
            // Paginación
            $paginatedResults = $query->paginate($perPage, ['*'], 'page', $page);
            
            \Illuminate\Support\Facades\Log::info('Consulta de vehículos exitosa. Encontrados: ' . $paginatedResults->total() . ', Mostrando página ' . $page . ' de ' . $paginatedResults->lastPage());
            \Illuminate\Support\Facades\Log::info('Filtros aplicados: ' . json_encode($request->all()));
            
            return response()->json([
                'status' => 'success',
                'vehiculos' => $paginatedResults->items(),
                'pagination' => [
                    'total' => $paginatedResults->total(),
                    'per_page' => $paginatedResults->perPage(),
                    'current_page' => $paginatedResults->currentPage(),
                    'last_page' => $paginatedResults->lastPage(),
                    'from' => $paginatedResults->firstItem(),
                    'to' => $paginatedResults->lastItem(),
                    'has_more_pages' => $paginatedResults->hasMorePages()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error en getVehiculos: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error('Trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al cargar los vehículos: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros
        $tipos = Tipo::all();
        $lugares = Lugar::all();
        
        // Obtener años únicos de vehículos para el filtro
        $anios = Vehiculo::select('año')->distinct()->orderBy('año', 'desc')->pluck('año');
        
        // Valoraciones posibles (1-5) para el filtro
        $valoraciones = [1, 2, 3, 4, 5];
        
        return view('admin.vehiculos', compact('tipos', 'lugares', 'anios', 'valoraciones'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        $lugares = Lugar::all();
        $tipos = Tipo::all();
        
        return view('admin.add_vehiculo', compact('lugares', 'tipos'));
    }

    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kilometraje' => 'required|integer|min:0',
                'seguro_incluido' => 'boolean',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipos,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'disponibilidad' => 'boolean',
                'matricula' => 'nullable|string|max:20',
            ]);

            // El seguro incluido viene como checkbox, así que si no está marcado, será null
            $validatedData['seguro_incluido'] = $request->has('seguro_incluido') ? true : false;
            $validatedData['disponibilidad'] = $request->has('disponibilidad') ? true : false;

            Vehiculo::create($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo añadido correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo añadido correctamente');

        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al crear el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        $vehiculo = Vehiculo::findOrFail($id_vehiculos);
        $lugares = Lugar::all();
        $tipos = Tipo::all();
        
        return view('admin.edit_vehiculo', compact('vehiculo', 'lugares', 'tipos'));
    }

    public function update(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);
            
            $validatedData = $request->validate([
                'marca' => 'required|string|max:255',
                'modelo' => 'required|string|max:255',
                'año' => 'required|integer|min:1900|max:' . (date('Y') + 1),
                'kilometraje' => 'required|integer|min:0',
                'seguro_incluido' => 'boolean',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_tipo' => 'required|exists:tipos,id_tipo',
                'precio_dia' => 'required|numeric|min:0',
                'disponibilidad' => 'boolean',
                'matricula' => 'nullable|string|max:20',
            ]);

            // El seguro incluido viene como checkbox, así que si no está marcado, será null
            $validatedData['seguro_incluido'] = $request->has('seguro_incluido') ? true : false;
            $validatedData['disponibilidad'] = $request->has('disponibilidad') ? true : false;

            $vehiculo->update($validatedData);
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo actualizado correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo actualizado correctamente');
            
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al actualizar el vehículo: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Request $request, $id_vehiculos)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }

        try {
            $vehiculo = Vehiculo::findOrFail($id_vehiculos);
            $vehiculo->delete();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vehículo eliminado correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.vehiculos')->with('success', 'Vehículo eliminado correctamente');
            
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar el vehículo',
                    'errors' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al eliminar el vehículo: ' . $e->getMessage());
        }
    }
}
