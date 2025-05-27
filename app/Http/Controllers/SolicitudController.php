<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chofer;
use App\Models\User;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Events\SolicitudAceptada;

class SolicitudController extends Controller
{
    /**
     * Obtiene choferes disponibles dentro de un radio determinado
     */
    public function getChoferesCercanos(Request $request)
    {
        try {
            $latitud = $request->latitud;
            $longitud = $request->longitud;
            $radio = 20; // Radio de búsqueda en kilómetros

            // Consulta a la base de datos para encontrar choferes disponibles dentro del radio
            $choferes = DB::table('choferes')
                ->join('users', 'choferes.id_usuario', '=', 'users.id_usuario')
                ->where('choferes.estado', 'disponible')
                ->select(
                    'choferes.id',
                    'users.nombre',
                    'choferes.latitud',
                    'choferes.longitud',
                    // Cálculo aproximado de distancia usando fórmula del plano cartesiano
                    DB::raw("
                        SQRT(
                            POW(($latitud - choferes.latitud) * 111, 2) + 
                            POW(($longitud - choferes.longitud) * 111 * COS(RADIANS($latitud)), 2)
                        ) AS distancia
                    ")
                )
                ->having('distancia', '<=', $radio)
                ->orderBy('distancia')
                ->get();

            return response()->json($choferes);
        } catch (\Exception $e) {
            Log::error('Error al obtener choferes cercanos: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener choferes cercanos'
            ], 500);
        }
    }

    /**
     * Crea una nueva solicitud de viaje
     */
    public function crearSolicitud(Request $request)
    {
        try {
            // Validación de campos requeridos
            $request->validate([
                'id_chofer' => 'required|exists:choferes,id',
                'latitud_origen' => 'required|numeric',
                'longitud_origen' => 'required|numeric',
                'latitud_destino' => 'required|numeric',
                'longitud_destino' => 'required|numeric',
                'precio' => 'required|numeric|min:0',
                'id_cliente' => 'required|exists:users,id_usuario'
            ]);

            // Verifica que el chofer esté disponible
            $chofer = Chofer::find($request->id_chofer);
            if (!$chofer || $chofer->estado !== 'disponible') {
                return response()->json([
                    'success' => false,
                    'message' => 'El chofer no está disponible'
                ], 400);
            }

            // Verifica si el usuario ya tiene una solicitud pendiente
            $solicitudExistente = Solicitud::where('id_cliente', $request->id_cliente)
                ->whereIn('estado', ['pendiente', 'aceptada'])
                ->first();

            if ($solicitudExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya tienes una solicitud activa pendiente o aceptada'
                ], 400);
            }

            // Crea una nueva solicitud
            $solicitud = new Solicitud();
            $solicitud->id_chofer = $request->id_chofer;
            $solicitud->id_cliente = $request->id_cliente;
            $solicitud->latitud_origen = $request->latitud_origen;
            $solicitud->longitud_origen = $request->longitud_origen;
            $solicitud->latitud_destino = $request->latitud_destino;
            $solicitud->longitud_destino = $request->longitud_destino;
            $solicitud->precio = $request->precio;
            $solicitud->estado = 'pendiente';
            $solicitud->save();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud creada exitosamente',
                'solicitud' => $solicitud
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Manejo de errores de validación
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error al crear solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene las solicitudes pendientes para el chofer autenticado
     */
    public function getSolicitudesChofer()
    {
        try {
            // Obtiene al chofer que corresponde al usuario autenticado
            $chofer = Chofer::where('id_usuario', Auth::id())->first();
            
            if (!$chofer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chofer no encontrado'
                ], 404);
            }

            // Recupera solicitudes pendientes asignadas al chofer
            $solicitudes = Solicitud::with(['cliente', 'chofer'])
                ->where('id_chofer', $chofer->id)
                ->where('estado', 'pendiente')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'solicitudes' => $solicitudes
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener solicitudes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las solicitudes'
            ], 500);
        }
    }

    /**
     * Acepta una solicitud y cambia el estado del chofer a ocupado
     */
    public function aceptarSolicitud($id)
    {
        try {
            $solicitud = Solicitud::with(['chofer.usuario'])->findOrFail($id);
            
            $solicitud->estado = 'aceptada';
            $solicitud->save();

            // Cambia el estado del chofer a ocupado
            $chofer = Chofer::find($solicitud->id_chofer);
            $chofer->estado = 'ocupado';
            $chofer->save();

            $solicitud->refresh();
            $solicitud->load(['chofer.usuario']);

            // Verifica que las relaciones están cargadas antes de lanzar evento
            if (!$solicitud->chofer || !$solicitud->chofer->usuario) {
                throw new \Exception('No se pudieron cargar las relaciones necesarias');
            }

            event(new SolicitudAceptada($solicitud));

            return response()->json([
                'success' => true,
                'message' => 'Solicitud aceptada exitosamente',
                'solicitud' => $solicitud,
                'chofer' => $chofer
            ]);
        } catch (\Exception $e) {
            Log::error('Error al aceptar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al aceptar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rechaza una solicitud cambiando su estado
     */
    public function rechazarSolicitud($id)
    {
        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'rechazada';
            $solicitud->save();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud rechazada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al rechazar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al rechazar la solicitud'
            ], 500);
        }
    }

    /**
     * Obtiene la ruta entre origen y destino usando OpenRouteService API
     */
    public function obtenerRuta(Request $request)
    {
        try {
            $origen_lat = $request->query('origen_lat');
            $origen_lng = $request->query('origen_lng');
            $destino_lat = $request->query('destino_lat');
            $destino_lng = $request->query('destino_lng');

            // Validación de coordenadas
            if (!$origen_lat || !$origen_lng || !$destino_lat || !$destino_lng) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan coordenadas necesarias'
                ], 400);
            }

            // API Key de OpenRouteService
            $apiKey = env('OPENROUTE_API_KEY');

            // Parámetros de la solicitud a la API
            $url = 'https://api.openrouteservice.org/v2/directions/driving-car';
            $params = [
                'start' => "$origen_lng,$origen_lat",
                'end' => "$destino_lng,$destino_lat"
            ];

            Log::info('Intentando obtener ruta de OpenRouteService', [
                'url' => $url,
                'params' => $params
            ]);

            // Realiza la solicitud HTTP
            $response = Http::withOptions(['verify' => false])
                ->withHeaders([
                    'Authorization' => $apiKey,
                    'Accept' => 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
                ])
                ->get($url, $params);

            // Manejo de respuesta exitosa
            if ($response->successful()) {
                $data = $response->json();
                Log::info('Respuesta exitosa de OpenRouteService', ['data' => $data]);
                return response()->json([
                    'success' => true,
                    'route' => [
                        'coordinates' => $data['features'][0]['geometry']['coordinates']
                    ]
                ]);
            }

            // Manejo de error HTTP
            Log::error('Error en respuesta de OpenRouteService', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la ruta: ' . $response->body()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error al obtener ruta: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el estado actual de una solicitud específica
     */
    public function getEstadoSolicitud($id)
    {
        try {
            $solicitud = Solicitud::with(['chofer.usuario'])->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'estado' => $solicitud->estado,
                'solicitud' => $solicitud,
                'chofer' => [
                    'id' => $solicitud->chofer->id,
                    'nombre' => $solicitud->chofer->usuario->nombre,
                    'latitud' => $solicitud->chofer->latitud,
                    'longitud' => $solicitud->chofer->longitud
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error al obtener estado de solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el estado de la solicitud'
            ], 500);
        }
    }

    /**
     * Obtiene detalles de una solicitud aceptada aún no leída por el cliente
     */
    public function getDetalles()
    {
        try {
            $solicitud = DB::table('solicitudes')
                ->join('users as cliente', 'solicitudes.id_cliente', '=', 'cliente.id_usuario')
                ->join('users as chofer', 'solicitudes.id_chofer', '=', 'chofer.id_usuario')
                ->where('solicitudes.id_cliente', Auth::id())
                ->where('solicitudes.estado', 'aceptada')
                ->where('solicitudes.notificacion_leida', false)
                ->select(
                    'solicitudes.id',
                    'cliente.nombre as cliente_nombre',
                    'chofer.nombre as chofer_nombre',
                    'solicitudes.precio'
                )
                ->first();

            if (!$solicitud) {
                return response()->json(['error' => 'No se encontró la solicitud'], 404);
            }

            return response()->json($solicitud);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener los detalles de la solicitud'], 500);
        }
    }

    /**
     * Cancela una solicitud aceptada y actualiza el estado del chofer
     */
    public function cancelarSolicitud($id)
    {
        try {
            DB::beginTransaction();

            $solicitud = Solicitud::findOrFail($id);

            // Verifica que la solicitud pertenece al usuario autenticado
            if ($solicitud->id_cliente !== Auth::id()) {
                throw new \Exception('No tienes permiso para cancelar esta solicitud');
            }

            // Solo se pueden cancelar solicitudes aceptadas
            if ($solicitud->estado !== 'aceptada') {
                throw new \Exception('Solo se pueden cancelar solicitudes aceptadas');
            }

            $solicitud->estado = 'cancelada';
            $solicitud->save();

            // Cambiar el estado del chofer a disponible
            $chofer = Chofer::find($solicitud->id_chofer);
            $chofer->estado = 'disponible';
            $chofer->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud cancelada correctamente'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al cancelar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

