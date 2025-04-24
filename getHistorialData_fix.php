<?php
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
        
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            \Log::warning('Falló verificación de administrador');
            return $authCheck;
        }
        
        // Iniciar la consulta con eager loading para cargar relaciones completas
        $query = Reserva::with(['usuario', 'lugar', 'vehiculos'])
                ->select('reservas.*')
                ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
        
        \Log::info('Query base de historial creada con eager loading');
        
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
        $reservas = $query->orderBy('reservas.fecha_reserva', 'desc')->get();
        \Log::info('Consulta principal ejecutada. Resultados: ' . count($reservas));
        
        // Procesar datos adicionales para cada reserva
        foreach ($reservas as $reserva) {
            // Asegurarse de que los datos del usuario estén correctos
            if ($reserva->usuario) {
                $reserva->nombre_usuario = $reserva->usuario->nombre;
                $reserva->id_usuario_visible = $reserva->id_usuario; // Guardar el ID para mostrar
                $reserva->rol_usuario = 'Gestor'; // Asumimos rol por defecto
            } else {
                $reserva->nombre_usuario = 'Usuario #' . $reserva->id_usuario;
                $reserva->id_usuario_visible = $reserva->id_usuario;
            }
            
            // Asegurarse de que los datos del lugar estén correctos
            if ($reserva->lugar) {
                $reserva->nombre_lugar = $reserva->lugar->nombre;
            } else {
                $reserva->nombre_lugar = 'Lugar #' . $reserva->id_lugar;
            }
            
            // Asegurarse de que los vehículos estén correctos
            if (empty($reserva->vehiculos) || count($reserva->vehiculos) === 0) {
                $reserva->vehiculos_procesados = false;
            } else {
                $reserva->vehiculos_procesados = true;
                
                // Formatear los vehículos para mostrar
                $reserva->vehiculos_info = [];
                foreach ($reserva->vehiculos as $vehiculo) {
                    $reserva->vehiculos_info[] = [
                        'id_vehiculos' => $vehiculo->id_vehiculos,
                        'marca' => $vehiculo->marca ?: 'Sin marca',
                        'modelo' => $vehiculo->modelo ?: 'Sin modelo',
                        'fecha_ini' => $vehiculo->pivot->fecha_ini ?? null,
                        'fecha_final' => $vehiculo->pivot->fecha_final ?? null,
                        'precio_unitario' => $vehiculo->pivot->precio_unitario ?? 0
                    ];
                }
            }
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
        
        \Log::info('Retornando respuesta JSON con ' . count($reservas) . ' reservas');
        return response()->json([
            'reservas' => $reservas,
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
