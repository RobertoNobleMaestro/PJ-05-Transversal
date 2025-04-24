<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ReservaCrudController extends Controller
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
    
    // Método para obtener reservas en formato JSON (para AJAX)
    public function getReservas(Request $request)
    {
        try {
            // Log de inicio y parámetros para depuración
            \Illuminate\Support\Facades\Log::info('Iniciando getReservas', [
                'request_params' => $request->all(),
                'request_ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $authCheck = $this->checkAdmin($request);
            if ($authCheck && $request->expectsJson()) {
                return $authCheck;
            }
            
            // Iniciar la consulta
            $query = Reserva::select('reservas.*', 'users.nombre as nombre_usuario', 'lugares.nombre as nombre_lugar')
                        ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                        ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
            
            // Log SQL para depuración
            \Illuminate\Support\Facades\Log::info('SQL base: ' . $query->toSql());
            
            // Aplicar filtros si existen
            if ($request->has('usuario') && !empty($request->usuario)) {
                $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
                \Illuminate\Support\Facades\Log::info('Filtro usuario aplicado: ' . $request->usuario);
            }
            
            if ($request->has('lugar') && !empty($request->lugar)) {
                $query->where('reservas.id_lugar', $request->lugar);
                \Illuminate\Support\Facades\Log::info('Filtro lugar aplicado: ' . $request->lugar);
            }
            
            if ($request->has('estado') && !empty($request->estado)) {
                $query->where('reservas.estado', $request->estado);
                \Illuminate\Support\Facades\Log::info('Filtro estado aplicado: ' . $request->estado);
            }
            
            if ($request->has('fecha') && !empty($request->fecha)) {
                $query->whereDate('reservas.fecha_reserva', $request->fecha);
                \Illuminate\Support\Facades\Log::info('Filtro fecha aplicado: ' . $request->fecha);
            }
            
            // Ejecutar la consulta con manejo de excepciones
            try {
                $reservas = $query->get();
                \Illuminate\Support\Facades\Log::info('Reservas obtenidas: ' . $reservas->count());
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al obtener reservas: ' . $e->getMessage(), [
                    'exception' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error al obtener reservas: ' . $e->getMessage()
                ], 500);
            }
            
            // Obtener los vehículos asociados a cada reserva con manejo de excepciones
            try {
                $reservas->each(function ($reserva) {
                    try {
                        $reserva->vehiculos_info = $reserva->vehiculos()->select(
                            'vehiculos.id_vehiculos', 
                            'vehiculos.marca', 
                            'vehiculos.modelo', 
                            'vehiculos_reservas.fecha_ini',
                            'vehiculos_reservas.fecha_final',
                            'vehiculos_reservas.precio_unitario'
                        )->get();
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Error al obtener vehículos para reserva ' . $reserva->id_reserva . ': ' . $e->getMessage());
                        $reserva->vehiculos_info = []; // Usar un array vacío si falla
                    }
                });
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error al procesar vehículos de reservas: ' . $e->getMessage(), [
                    'exception' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'error' => 'Error al procesar vehículos: ' . $e->getMessage(),
                    'reservas' => [] // Devolver un array vacío en caso de error
                ], 500);
            }
            
            // Devolver resultados exitosos
            \Illuminate\Support\Facades\Log::info('getReservas completado con éxito');
            return response()->json([
                'reservas' => $reservas
            ]);
            
        } catch (\Exception $e) {
            // Capturar cualquier excepción no controlada
            \Illuminate\Support\Facades\Log::error('Error general en getReservas: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return response()->json([
                'error' => 'Error interno del servidor: ' . $e->getMessage(),
                'reservas' => [] // Devolver un array vacío en caso de error
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
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        
        return view('admin.reservas', compact('lugares', 'estados'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener usuarios, lugares y vehículos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::where('disponibilidad', true)->get();
        
        return view('admin.add_reserva', compact('usuarios', 'lugares', 'vehiculos'));
    }

    // Método para obtener reservas de un vehículo específico (para el calendario)
    public function reservasPorVehiculo($id_vehiculo)
    {
        // Buscar el vehículo
        $vehiculo = Vehiculo::findOrFail($id_vehiculo);
        
        // Obtener todas las reservas pendientes, confirmadas o completadas para este vehículo
        $reservasVehiculo = $vehiculo->vehiculosReservas()
            ->join('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
            ->whereIn('reservas.estado', ['pendiente', 'confirmada', 'completada'])
            ->get(['vehiculos_reservas.fecha_ini', 'vehiculos_reservas.fecha_final', 'reservas.estado']);
        
        // Formatear los datos para el calendario
        $eventos = [];
        
        foreach ($reservasVehiculo as $reserva) {
            // Definir color según el estado de la reserva
            $color = '#6f42c1'; // Color lila por defecto
            $titulo = 'Reservado';
            
            if ($reserva->estado === 'pendiente') {
                $color = '#ffc107'; // Amarillo para pendientes
                $titulo = 'Pendiente';
            } elseif ($reserva->estado === 'completada') {
                $color = '#28a745'; // Verde para completadas
                $titulo = 'Completada';
            }
            
            $eventos[] = [
                'start' => $reserva->fecha_ini,
                'end' => date('Y-m-d', strtotime($reserva->fecha_final . ' +1 day')), // Sumar un día para que incluya el día final
                'title' => $titulo,
                'color' => $color,
                'display' => 'background'
            ];
        }
        
        return response()->json($eventos);
    }
    
    // Método para crear una nueva reserva desde la página de detalle de vehículo
    public function crearReserva(Request $request)
    {
        try {
            // Verificar si el usuario está autenticado
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Debes iniciar sesión para realizar una reserva'
                ], 401);
            }
            
            // Validar datos
            $request->validate([
                'id_vehiculos' => 'required|exists:vehiculos,id_vehiculos',
                'fecha_ini' => 'required|date|after_or_equal:today',
                'fecha_final' => 'required|date|after_or_equal:fecha_ini',
            ]);
            
            // Buscar el vehículo
            $vehiculo = Vehiculo::findOrFail($request->id_vehiculos);
            
            // Verificar disponibilidad en esas fechas
            $fechaOcupada = $vehiculo->vehiculosReservas()
                ->join('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
                ->whereIn('reservas.estado', ['confirmada', 'completada'])
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '<=', $request->fecha_ini)
                          ->where('vehiculos_reservas.fecha_final', '>=', $request->fecha_ini);
                    })->orWhere(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '<=', $request->fecha_final)
                          ->where('vehiculos_reservas.fecha_final', '>=', $request->fecha_final);
                    })->orWhere(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '>=', $request->fecha_ini)
                          ->where('vehiculos_reservas.fecha_final', '<=', $request->fecha_final);
                    });
                })
                ->exists();
                
            if ($fechaOcupada) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El vehículo no está disponible en las fechas seleccionadas'
                ], 400);
            }
            
            // Calcular el número de días
            $fechaInicio = new \DateTime($request->fecha_ini);
            $fechaFin = new \DateTime($request->fecha_final);
            $diff = $fechaInicio->diff($fechaFin);
            $dias = $diff->days + 1; // Incluir el día de fin
            
            // Calcular precio total
            $precioUnitario = $vehiculo->precio_dia * $dias;
            
            // Iniciar transacción
            DB::beginTransaction();
            
            // Crear reserva (estado pendiente inicialmente)
            $reserva = Reserva::create([
                'fecha_reserva' => now(),
                'total_precio' => $precioUnitario,
                'estado' => 'pendiente',
                'id_lugar' => $vehiculo->id_lugar,
                'id_usuario' => auth()->id() ?? 1,
            ]);
            
            // Asociar vehículo a la reserva
            $reserva->vehiculos()->attach($vehiculo->id_vehiculos, [
                'fecha_ini' => $request->fecha_ini,
                'fecha_final' => $request->fecha_final,
            ]);
            
            // Confirmar transacción
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Reserva creada correctamente',
                'id_reserva' => $reserva->id_reservas
            ]);
            
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            
            \Illuminate\Support\Facades\Log::error('Error al crear reserva: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la reserva: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Crear la reserva
            $reserva = Reserva::create([
                'fecha_reserva' => $validatedData['fecha_reserva'],
                'total_precio' => 0, // Se actualizará después
                'estado' => $validatedData['estado'],
                'id_lugar' => $validatedData['id_lugar'],
                'id_usuario' => $validatedData['id_usuario'],
            ]);
            
            // Asociar vehículos a la reserva
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->update(['total_precio' => $total_precio]);
            
            // Confirmar la transacción
            DB::commit();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva añadida correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva añadida correctamente');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al crear la reserva: ' . $e->getMessage())->withInput();
        }
    }
    
    public function edit(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Buscar la reserva por su ID
        $reserva = Reserva::findOrFail($id_reservas);
        
        // Obtener los vehículos asociados a esta reserva con sus datos de relación
        $reserva_vehiculos = $reserva->vehiculos()->select(
            'vehiculos.id_vehiculos',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos_reservas.fecha_ini',
            'vehiculos_reservas.fecha_final',
            'vehiculos_reservas.precio_unitario'
        )->get();
        
        // Obtener datos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::all(); // Mostrar todos los vehículos para poder editarlos
        
        return view('admin.edit_reserva', compact('reserva', 'reserva_vehiculos', 'usuarios', 'lugares', 'vehiculos'));
    }
    
    public function update(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Encontrar la reserva a actualizar
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Actualizar los datos básicos de la reserva
            $reserva->fecha_reserva = $validatedData['fecha_reserva'];
            $reserva->estado = $validatedData['estado'];
            $reserva->id_lugar = $validatedData['id_lugar'];
            $reserva->id_usuario = $validatedData['id_usuario'];
            
            // Eliminar todas las relaciones existentes con vehículos
            $reserva->vehiculos()->detach();
            
            // Asociar vehículos a la reserva
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->total_precio = $total_precio;
            $reserva->save();
            
            // Confirmar la transacción
            DB::commit();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva actualizada correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva actualizada correctamente');
        } catch (ValidationException $e) {
            // Si hay errores de validación
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Si ocurre cualquier otro error
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al actualizar la reserva: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroy(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }

        try {
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Eliminar la reserva (y sus relaciones con vehículos por cascada)
            $reserva->delete();
            
            // Confirmar la transacción
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva eliminada correctamente'
                ], 200);
            }
            
            return redirect()->route('admin.reservas.index')->with('success', 'Reserva eliminada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.reservas.index')->with('error', 'Error al eliminar la reserva: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra el historial de todas las reservas
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function historial(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        
        // Obtener todas las reservas completadas ordenadas por fecha
        $reservas = Reserva::where('estado', 'completada')
            ->orderBy('fecha_reserva', 'desc')
            ->with(['usuario', 'lugar', 'vehiculos'])
            ->get();
            
        return view('admin.historial', compact('reservas', 'lugares', 'estados'));
    }
    
    /**
     * Obtiene datos del historial de reservas para AJAX
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHistorialData(Request $request)
    {
        try {
            \Log::info('Iniciando getHistorialData con parámetros:', $request->all());
            
            // Verificar autenticación y autorización
            $authCheck = $this->checkAdmin($request);
            if ($authCheck && $request->expectsJson()) {
                \Log::warning('Falló verificación de administrador');
                return $authCheck;
            }
            
            \Log::info('Autenticación verificada correctamente, procediendo con la consulta');
            
            // Iniciar la consulta con eager loading para cargar relaciones completas
            try {
                $query = Reserva::with(['usuario', 'lugar', 'vehiculos']);
                \Log::info('Consulta base creada con eager loading');
            } catch (\Exception $e) {
                \Log::error('Error al crear consulta base: ' . $e->getMessage());
                throw new \Exception('Error en la creación de la consulta: ' . $e->getMessage());
            }
            
            try {
                $query->select('reservas.*')
                      ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                      ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
                \Log::info('Joins aplicados correctamente');
            } catch (\Exception $e) {
                \Log::error('Error al aplicar joins: ' . $e->getMessage());
                throw new \Exception('Error en los joins de la consulta: ' . $e->getMessage());
            }
            
            // Aplicar filtros si existen
            if ($request->has('usuario') && !empty($request->usuario)) {
                $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
                \Log::info('Filtro por usuario aplicado: ' . $request->usuario);
            }
            
            if ($request->has('lugar') && !empty($request->lugar)) {
                $query->where('reservas.id_lugar', $request->lugar);
                \Log::info('Filtro por lugar aplicado: ' . $request->lugar);
            }
            
            if ($request->has('estado') && !empty($request->estado)) {
                $query->where('reservas.estado', $request->estado);
                \Log::info('Filtro por estado aplicado: ' . $request->estado);
            } else {
                // Por defecto para el historial, mostrar solo las completadas si no se especifica otro estado
                $query->where('reservas.estado', 'completada');
                \Log::info('Aplicando filtro por defecto: estado=completada');
            }
            
            // Filtros de rango de fechas
            if ($request->has('fechaDesde') && !empty($request->fechaDesde)) {
                $query->whereDate('reservas.fecha_reserva', '>=', $request->fechaDesde);
                \Log::info('Filtro por fecha desde aplicado: ' . $request->fechaDesde);
            }
            
            if ($request->has('fechaHasta') && !empty($request->fechaHasta)) {
                $query->whereDate('reservas.fecha_reserva', '<=', $request->fechaHasta);
                \Log::info('Filtro por fecha hasta aplicado: ' . $request->fechaHasta);
            }
            
            \Log::info('Ejecutando consulta principal...');
            // Ejecutar la consulta
            try {
                $reservas = $query->orderBy('reservas.fecha_reserva', 'desc')->get();
                \Log::info('Consulta principal ejecutada. Resultados: ' . count($reservas));
            } catch (\Exception $e) {
                \Log::error('Error en la ejecución de la consulta: ' . $e->getMessage());
                throw new \Exception('Error al recuperar datos de reservas: ' . $e->getMessage());
            }
            
            // Procesar datos adicionales para cada reserva
            $reservasProcessed = [];
            try {
                foreach ($reservas as $reserva) {
                    // Crear copia para no modificar el modelo original
                    $reservaData = $reserva->toArray();
                    
                    // Asegurarse de que los datos del usuario estén correctos
                    if (isset($reserva->usuario) && $reserva->usuario) {
                        $reservaData['nombre_usuario'] = $reserva->usuario->nombre;
                        $reservaData['id_usuario_visible'] = $reserva->id_usuario;
                        $reservaData['rol_usuario'] = 'Gestor'; // Valor por defecto
                    } else {
                        $reservaData['nombre_usuario'] = 'Usuario #' . $reserva->id_usuario;
                        $reservaData['id_usuario_visible'] = $reserva->id_usuario;
                    }
                    
                    // Asegurarse de que los datos del lugar estén correctos
                    if (isset($reserva->lugar) && $reserva->lugar) {
                        $reservaData['nombre_lugar'] = $reserva->lugar->nombre;
                    } else {
                        $reservaData['nombre_lugar'] = 'Lugar #' . $reserva->id_lugar;
                    }
                    
                    // Asegurarse de que los vehículos estén correctos
                    $reservaData['vehiculos_procesados'] = false;
                    $reservaData['vehiculos_info'] = [];
                    
                    if (isset($reserva->vehiculos) && !empty($reserva->vehiculos) && count($reserva->vehiculos) > 0) {
                        $reservaData['vehiculos_procesados'] = true;
                        
                        // Formatear los vehículos para mostrar
                        foreach ($reserva->vehiculos as $vehiculo) {
                            $reservaData['vehiculos_info'][] = [
                                'id_vehiculos' => $vehiculo->id_vehiculos,
                                'marca' => $vehiculo->marca ?: 'Sin marca',
                                'modelo' => $vehiculo->modelo ?: 'Sin modelo',
                                'fecha_ini' => isset($vehiculo->pivot) ? $vehiculo->pivot->fecha_ini : null,
                                'fecha_final' => isset($vehiculo->pivot) ? $vehiculo->pivot->fecha_final : null,
                                'precio_unitario' => isset($vehiculo->pivot) ? $vehiculo->pivot->precio_unitario : 0
                            ];
                        }
                    }
                    
                    $reservasProcessed[] = $reservaData;
                }
                \Log::info('Procesamiento de datos de reservas completado');
            } catch (\Exception $e) {
                \Log::error('Error al procesar datos de reservas: ' . $e->getMessage());
                throw new \Exception('Error al procesar datos adicionales: ' . $e->getMessage());
            }
            
            // Calcular estadísticas
            try {
                $stats = [
                    'total' => Reserva::count(),
                    'completadas' => Reserva::where('estado', 'completada')->count(),
                    'pendientes' => Reserva::where('estado', 'pendiente')->count(),
                    'canceladas' => Reserva::where('estado', 'cancelada')->count(),
                    'ingresos' => Reserva::where('estado', 'completada')->sum('total_precio') ?? 0
                ];
                \Log::info('Estadísticas calculadas correctamente');
            } catch (\Exception $e) {
                \Log::error('Error al calcular estadísticas: ' . $e->getMessage());
                $stats = [
                    'total' => 0,
                    'completadas' => 0,
                    'pendientes' => 0,
                    'canceladas' => 0,
                    'ingresos' => 0
                ];
            }
            
            \Log::info('Retornando respuesta JSON con ' . count($reservasProcessed) . ' reservas');
            return response()->json([
                'reservas' => $reservasProcessed,
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error en getHistorialData: ' . $e->getMessage() . '\nTrace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Error al procesar la solicitud',
                'message' => $e->getMessage(),
                'reservas' => [],
                'stats' => [
                    'total' => 0,
                    'completadas' => 0,
                    'pendientes' => 0,
                    'canceladas' => 0,
                    'ingresos' => 0
                ]
            ], 500);
        }
    }
    
    /**
     * Obtiene los detalles de una reserva específica para mostrar en modal
     *
     * @param int $id_reserva
     * @return \Illuminate\Http\JsonResponse
     */
    public function getReservaDetails($id_reserva)
    {
        try {
            \Log::info('Obteniendo detalles de la reserva ID: ' . $id_reserva);
            
            $authCheck = $this->checkAdmin(request());
            if ($authCheck && request()->expectsJson()) {
                \Log::warning('Falló verificación de administrador al consultar detalles de reserva');
                return $authCheck;
            }
            
            // Obtener la reserva con información relacionada
            $reserva = Reserva::with(['usuario', 'lugar'])
                ->findOrFail($id_reserva);
                
            // Obtener los vehículos asociados a la reserva
            $reserva->vehiculos_info = $reserva->vehiculos()->select(
                'vehiculos.id_vehiculos', 
                'vehiculos.marca', 
                'vehiculos.modelo', 
                'vehiculos_reservas.fecha_ini',
                'vehiculos_reservas.fecha_final',
                'vehiculos_reservas.precio_unitario'
            )->get();
            
            // Incluir información del usuario si existe relación
            if ($reserva->usuario) {
                $reserva->nombre_usuario = $reserva->usuario->nombre;
                $reserva->email_usuario = $reserva->usuario->email;
                $reserva->telefono_usuario = $reserva->usuario->telefono;
            }
            
            // Incluir nombre del lugar si existe relación
            if ($reserva->lugar) {
                $reserva->nombre_lugar = $reserva->lugar->nombre;
            }
            
            \Log::info('Detalles de reserva obtenidos correctamente');
            
            return response()->json([
                'status' => 'success',
                'reserva' => $reserva
            ]);
        } catch (\Exception $e) {
            \Log::error('Error en getReservaDetails: ' . $e->getMessage() . '\nTrace: ' . $e->getTraceAsString());
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo encontrar la reserva solicitada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
