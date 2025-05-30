<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class HistorialGestorController extends Controller
{
    private function checkGestor($request)
    {
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
    // Método privado para verificar si el usuario es administrador
    public function historial(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }
        $user = auth()->user();
        if ($user->id_roles == 1) {
            return $this->adminHistorial($request);
        } else {
            return $this->gestorHistorial($request);
        }
    }

    // Panel de historial para admin
    private function adminHistorial(Request $request)
    {
        $reservas = Reserva::orderBy('fecha_reserva', 'desc')->with(['usuario', 'lugar', 'vehiculos'])->get();
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada', 'pagado'];
        return view('gestor.historial', compact('reservas', 'lugares', 'estados'));
    }

    // Panel de historial para gestor
    private function gestorHistorial(Request $request)
    {
        $gestor = auth()->user();
        $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();
        if (!$parking) {
            return redirect()->back()->with('error', 'No se ha encontrado un parking asignado a este gestor.');
        }
        $lugarGestor = Lugar::find($parking->id_lugar);
        if (!$lugarGestor) {
            return redirect()->back()->with('error', 'No se ha encontrado un lugar asignado a este gestor.');
        }
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        $reservas = Reserva::where('id_lugar', $lugarGestor->id_lugar)
            ->orderBy('fecha_reserva', 'desc')
            ->with(['usuario', 'lugar', 'vehiculos'])
            ->get();
        $lugares = collect([$lugarGestor]);
        return view('gestor.historial', compact('reservas', 'lugares', 'estados'));
    }

    public function getHistorialData(Request $request)
    {
        try {
            // Iniciar la consulta con relaciones
            $query = Reserva::with(['usuario', 'lugar', 'vehiculos']);

            $query->select('reservas.*')
                ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');

            $gestor = auth()->user();
            $parking = null;

            if ($gestor->id_roles === 3) {
                $parking = \App\Models\Parking::where('id_usuario', $gestor->id_usuario)->first();
                if ($parking) {
                    $query->where('reservas.id_lugar', $parking->id_lugar);
                } else {
                    return response()->json([
                        'error' => 'No se ha encontrado un parking asociado al gestor.',
                        'reservas' => [],
                        'stats' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'last_page' => 1,
                            'total' => 0,
                            'per_page' => 10,
                        ]
                    ], 400);
                }
            }

            // Filtros opcionales
            if ($gestor->id_roles !== 3 && $request->filled('lugar')) {
                $query->where('reservas.id_lugar', $request->lugar);
            }

            if ($request->filled('usuario')) {
                $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
            }

            if ($request->filled('estado')) {
                $query->where('reservas.estado', $request->estado);
            }

            if ($request->filled('fechaDesde')) {
                $query->whereDate('reservas.fecha_reserva', '>=', $request->fechaDesde);
            }

            if ($request->filled('fechaHasta')) {
                $query->whereDate('reservas.fecha_reserva', '<=', $request->fechaHasta);
            }

            // Paginación
            $perPage = (int) $request->input('perPage', 10);
            $page = (int) $request->input('page', 1);
            $paginated = $query->orderBy('reservas.fecha_reserva', 'desc')->paginate($perPage, ['*'], 'page', $page);

            // Procesar resultados
            $reservasProcessed = [];
            foreach ($paginated->items() as $reserva) {
                $reservaData = $reserva->toArray();

                $reservaData['nombre_usuario'] = $reserva->usuario->nombre ?? 'Usuario #' . $reserva->id_usuario;
                $reservaData['id_usuario_visible'] = $reserva->id_usuario;
                $reservaData['rol_usuario'] = 'Gestor'; // Valor por defecto

                $reservaData['nombre_lugar'] = $reserva->lugar->nombre ?? 'Lugar #' . $reserva->id_lugar;

                $reservaData['vehiculos_procesados'] = false;
                $reservaData['vehiculos_info'] = [];

                if ($reserva->vehiculos && count($reserva->vehiculos) > 0) {
                    $reservaData['vehiculos_procesados'] = true;

                    foreach ($reserva->vehiculos as $vehiculo) {
                        $reservaData['vehiculos_info'][] = [
                            'id_vehiculos' => $vehiculo->id_vehiculos,
                            'marca' => $vehiculo->marca ?? 'Sin marca',
                            'modelo' => $vehiculo->modelo ?? 'Sin modelo',
                            'fecha_ini' => $vehiculo->pivot->fecha_ini ?? null,
                            'fecha_final' => $vehiculo->pivot->fecha_final ?? null,
                            'precio_unitario' => $vehiculo->pivot->precio_unitario ?? 0
                        ];
                    }
                }

                $reservasProcessed[] = $reservaData;
            }

            // Estadísticas (clonar para evitar acumulación de where)
            $baseStatsQuery = Reserva::query();
            if ($gestor->id_roles === 3 && $parking) {
                $baseStatsQuery->where('id_lugar', $parking->id_lugar);
            }

            $stats = [
                'total' => $baseStatsQuery->count(),
                'completadas' => (clone $baseStatsQuery)->where('estado', 'completada')->count(),
                'pendientes' => (clone $baseStatsQuery)->where('estado', 'pendiente')->count(),
                'canceladas' => (clone $baseStatsQuery)->where('estado', 'cancelada')->count(),
                'confirmadas' => (clone $baseStatsQuery)->where('estado', 'confirmada')->count(),
                'ingresos' => (clone $baseStatsQuery)->where('estado', 'completada')->sum('total_precio') ?? 0
            ];

            return response()->json([
                'reservas' => $reservasProcessed,
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $paginated->currentPage(),
                    'last_page' => $paginated->lastPage(),
                    'total' => $paginated->total(),
                    'per_page' => $paginated->perPage(),
                ]
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
                    'confirmadas' => 0,
                    'ingresos' => 0
                ],
                'pagination' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'total' => 0,
                    'per_page' => 10,
                ]
            ], 500);
        }
    }
}
