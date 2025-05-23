<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chofer;
use App\Models\User;
use App\Models\Solicitud;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SolicitudController extends Controller
{
    public function getChoferesCercanos(Request $request)
    {
        try {
            $latitud = $request->latitud;
            $longitud = $request->longitud;
            $radio = 20; // Radio en km

            $choferes = DB::table('choferes')
                ->join('users', 'choferes.id_usuario', '=', 'users.id_usuario')
                ->where('choferes.estado', 'disponible')
                ->select(
                    'choferes.id',
                    'users.nombre',
                    'choferes.latitud',
                    'choferes.longitud',
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

    public function crearSolicitud(Request $request)
    {
        try {
            $request->validate([
                'id_chofer' => 'required|exists:choferes,id',
                'latitud_origen' => 'required|numeric',
                'longitud_origen' => 'required|numeric',
                'latitud_destino' => 'required|numeric',
                'longitud_destino' => 'required|numeric',
                'precio' => 'required|numeric|min:0',
                'id_cliente' => 'required|exists:users,id_usuario'
            ]);

            $chofer = Chofer::find($request->id_chofer);
            if (!$chofer || $chofer->estado !== 'disponible') {
                return response()->json([
                    'success' => false,
                    'message' => 'El chofer no está disponible'
                ], 400);
            }

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

    public function getSolicitudesChofer()
    {
        try {
            $chofer = Chofer::where('id_usuario', Auth::id())->first();
            
            if (!$chofer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chofer no encontrado'
                ], 404);
            }

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

    public function aceptarSolicitud($id)
    {
        try {
            $solicitud = Solicitud::findOrFail($id);
            $solicitud->estado = 'aceptada';
            $solicitud->save();

            return response()->json([
                'success' => true,
                'message' => 'Solicitud aceptada exitosamente'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al aceptar solicitud: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al aceptar la solicitud'
            ], 500);
        }
    }

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
}
