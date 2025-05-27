<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class NotificacionPagoController extends Controller
{
    public function checkout($id_solicitud)
    {
        // Obtener la solicitud pendiente del usuario
        $solicitud = Solicitud::where('id', $id_solicitud)
                         ->where('id_cliente', Auth::id())
                         ->where('estado', 'aceptada')
                         ->first();

        if (!$solicitud) {
            return redirect()->route('home')->with('error', 'No tienes ninguna solicitud pendiente de pago');
        }

        // Configurar Stripe con la clave secreta
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Crear la sesión de Stripe Checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Servicio de Chofer',
                            'description' => sprintf(
                                'Servicio de chofer del %s - Origen: %s, Destino: %s',
                                $solicitud->fecha_solicitud,
                                $solicitud->origen,
                                $solicitud->destino
                            ),
                        ],
                        'unit_amount' => intval($solicitud->precio * 100), // Stripe trabaja en centavos
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('notificacion.pago.exito', ['id_solicitud' => $solicitud->id]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('notificacion.pago.cancelado'),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'id_solicitud' => $solicitud->id,
                    'id_usuario' => Auth::id(),
                    'total' => $solicitud->precio
                ],
                'locale' => 'es',
            ]);

            // Crear el registro en la tabla pagos_choferes
            DB::table('pagos_choferes')->insert([
                'chofer_id' => $solicitud->id_chofer,
                'solicitud_id' => $solicitud->id,
                'importe_total' => $solicitud->precio,
                'importe_empresa' => $solicitud->precio * 0.8, // 80% para la empresa
                'importe_chofer' => $solicitud->precio * 0.2, // 20% para el chofer
                'estado_pago' => 'pendiente',
                'fecha_pago' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return view('notificaciones.checkout', [
                'solicitud' => $solicitud,
                'stripe_session_id' => $session->id,
                'stripe_public_key' => env('STRIPE_KEY'),
            ]);
        } catch (ApiErrorException $e) {
            // Manejo de errores de la API de Stripe
            return redirect()->route('home')->with('error', 'Error al crear la sesión de pago: ' . $e->getMessage());
        }
    }
    
    public function exito(Request $request, $id_solicitud)
    {
        $solicitud = Solicitud::with(['cliente', 'chofer.usuario'])->findOrFail($id_solicitud);

        // Verificar que la solicitud pertenece al usuario autenticado
        if ($solicitud->id_cliente != Auth::id()) {
            return redirect()->route('home');
        }
        
        // Verificar el estado de la sesión de Stripe
        if ($request->has('session_id')) {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            
            try {
                $session = \Stripe\Checkout\Session::retrieve($request->session_id);
                
                // Verificar que la sesión está pagada
                if ($session->payment_status === 'paid') {
                    // Actualizar el estado de la solicitud
                    $solicitud->estado = 'completada';
                    $solicitud->save();
                    
                    // Actualizar el estado del pago en pagos_choferes
                    DB::table('pagos_choferes')
                        ->where('solicitud_id', $solicitud->id)
                        ->update([
                            'estado_pago' => 'pagado',
                            'updated_at' => now()
                        ]);

                    // Generar el PDF del ticket
                    $pdf = PDF::loadView('notificaciones.ticket', ['solicitud' => $solicitud]);
                    
                    // Guardar el mensaje de éxito en la sesión
                    session()->flash('success', '¡Pago realizado correctamente! Tu ticket se está descargando.');
                    
                    // Descargar el PDF
                    return $pdf->download('ticket-servicio-' . $solicitud->id . '.pdf')
                        ->header('Refresh', '0;url=' . route('home'));
                }
            } catch (\Stripe\Exception\ApiErrorException $e) {
                // Manejar errores de Stripe
                return redirect()->route('home')->with('error', 'Error al verificar el pago: ' . $e->getMessage());
            }
        }
        
        // Si no hay session_id o la verificación falló
        return redirect()->route('home')->with('error', 'No se pudo verificar el pago');
    }

    public function cancelado()
    {
        return view('notificaciones.cancelado');
    }
    
    public function webhook(Request $request)
    {
        // Configurar Stripe
        Stripe::setApiKey(env('STRIPE_SECRET'));
        
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        
        try {
            // Verificar el evento de Stripe
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
            
            // Manejar el evento
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    // Obtener la solicitud y actualizar su estado
                    if (isset($session->metadata->id_solicitud)) {
                        $solicitud = Solicitud::find($session->metadata->id_solicitud);
                        
                        if ($solicitud) {
                            $solicitud->estado = 'pagado';
                            $solicitud->save();
                            
                            // Registrar el pago
                            Pago::create([
                                'estado_pago' => 'completado',
                                'fecha_pago' => now(),
                                'referencia_externa' => $session->payment_intent,
                                'monto_pagado' => $session->metadata->total,
                                'total_precio' => $session->metadata->total,
                                'moneda' => 'EUR',
                                'id_usuario' => $solicitud->id_usuario,
                                'id_solicitud' => $solicitud->id_solicitud,
                            ]);
                        }
                    }
                    break;
            }
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
} 