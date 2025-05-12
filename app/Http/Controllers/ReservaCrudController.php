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
    // Muestra el historial de las reservas
    public function historial(Request $request)
    {
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


    // Obtiene datos del historial de reservas para AJAX
    public function getHistorialData(Request $request)
    {
        try {
            // Iniciar la consulta con eager loading para cargar relaciones completas
            try {
                $query = Reserva::with(['usuario', 'lugar', 'vehiculos']);
            } catch (\Exception $e) {
                throw new \Exception('Error en la creación de la consulta: ' . $e->getMessage());
            }

            try {
                $query->select('reservas.*')
                    ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                    ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
            } catch (\Exception $e) {
                throw new \Exception('Error en los joins de la consulta: ' . $e->getMessage());
            }

            // Aplicar filtros si existen
            if ($request->has('usuario') && !empty($request->usuario)) {
                $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
            }

            if ($request->has('lugar') && !empty($request->lugar)) {
                $query->where('reservas.id_lugar', $request->lugar);
            }

            if ($request->has('estado') && !empty($request->estado)) {
                $query->where('reservas.estado', $request->estado);
            } else {
                // Por defecto para el historial, mostrar solo las completadas si no se especifica otro estado
                $query->where('reservas.estado', 'completada');
            }

            // Filtros de rango de fechas
            if ($request->has('fechaDesde') && !empty($request->fechaDesde)) {
                $query->whereDate('reservas.fecha_reserva', '>=', $request->fechaDesde);
            }

            if ($request->has('fechaHasta') && !empty($request->fechaHasta)) {
                $query->whereDate('reservas.fecha_reserva', '<=', $request->fechaHasta);
            }

            // Ejecutar la consulta
            try {
                $reservas = $query->orderBy('reservas.fecha_reserva', 'desc')->get();
            } catch (\Exception $e) {
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
            } catch (\Exception $e) {
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
            } catch (\Exception $e) {
                $stats = [
                    'total' => 0,
                    'completadas' => 0,
                    'pendientes' => 0,
                    'canceladas' => 0,
                    'ingresos' => 0
                ];
            }

            return response()->json([
                'reservas' => $reservasProcessed,
                'stats' => $stats
            ]);
        } catch (\Exception $e) {
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

    // Obtiene los detalles de una reserva específica para mostrar en modal
    public function getReservaDetails($id_reserva)
    {
        try {
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

            return response()->json([
                'status' => 'success',
                'reserva' => $reserva
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se pudo encontrar la reserva solicitada',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}
