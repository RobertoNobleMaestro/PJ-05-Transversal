<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
// Importamos nuestro servicio personalizado de Stripe
use App\Services\Stripe\StripeService;

class PagoController extends Controller
{
    public function checkout()
    {
        // Obtener la reserva pendiente del usuario con sus vehículos
        $reserva = Reserva::where('id_usuario', Auth::id())
                         ->where('estado', 'pendiente')
                         ->with(['vehiculos', 'vehiculosReservas', 'vehiculosReservas.vehiculo'])
                         ->first();

        if (!$reserva) {
            return redirect()->route('carrito')->with('error', 'No tienes ninguna reserva pendiente');
        }

        // Calcular el precio total de la reserva
        $total = 0;
        foreach ($reserva->vehiculosReservas as $vr) {
            $dias = \Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final);
            $dias = max(1, $dias); // Mínimo 1 día
            $total += $vr->vehiculo->precio_dia * $dias;
        }

        // Actualizar la reserva con el precio total
        $reserva->total_precio = $total;
        $reserva->save();

        // Usar nuestro servicio personalizado de Stripe
        $stripeService = new StripeService();

        try {
            // Preparar descripción y metadatos para Stripe
            $description = 'Reserva de vehículos - ID: ' . $reserva->id_reservas;
            $metadata = [
                'id_reserva' => $reserva->id_reservas,
                'id_usuario' => Auth::id(),
                'total' => $total
            ];
            
            // URLs para redirecciones
            $successUrl = route('pago.exito', ['id_reserva' => $reserva->id_reservas]) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancelUrl = route('pago.cancelado');
            
            // Llamar a nuestro servicio para crear la sesión de checkout
            $session = $stripeService->createCheckoutSession(
                $total,
                $description,
                $metadata,
                $successUrl,
                $cancelUrl
            );
            
            // Log para depuración
            Log::info('Respuesta de Stripe: ' . json_encode($session));
            
            // Verificar si hubo error
            if (isset($session['error'])) {
                throw new \Exception($session['message'] ?? 'Error al crear la sesión de pago');
            }

            // Guardar el ID de la sesión en la reserva para referencia futura
            $reserva->referencia_pago = $session['id'] ?? '';
            $reserva->save();

            return view('pago.checkout', [
                'reserva' => $reserva,
                'stripe_session_id' => $session['id'] ?? '',
                'stripe_public_key' => env('STRIPE_KEY'),
                'payment_url' => $session['url'] ?? ''
            ]);
        } catch (\Exception $e) {
            // Manejo de errores
            Log::error('Error al crear la sesión de pago: ' . $e->getMessage());
            return redirect()->route('carrito')->with('error', 'Error al crear la sesión de pago: ' . $e->getMessage());
        }
    }
    
    public function exito(Request $request, $id_reserva)
    {
        $reserva = Reserva::findOrFail($id_reserva);

        // Verificar que la reserva pertenece al usuario autenticado
        if ($reserva->id_usuario != Auth::id()) {
            return redirect()->route('home');
        }
        
        // Verificar el estado de la sesión de Stripe con nuestro servicio
        if ($request->has('session_id')) {
            $stripeService = new StripeService();
            
            try {
                // Verificar si el pago fue exitoso
                $isSuccessful = $stripeService->isPaymentSuccessful($request->session_id);
                
                // Verificar que la sesión está pagada
                if ($isSuccessful) {
                    // Actualizar el estado de la reserva
                    $reserva->estado = 'pagado';
                    $reserva->save();
                    
                    // Registrar el pago en nuestro sistema
                    Pago::create([
                        'estado_pago' => 'completado',
                        'fecha_pago' => now(),
                        'referencia_externa' => $request->session_id, // Usamos el ID de la sesión como referencia
                        'monto_pagado' => $reserva->total_precio,
                        'total_precio' => $reserva->total_precio,
                        'moneda' => 'EUR',
                        'id_usuario' => Auth::id(),
                        'id_reservas' => $reserva->id_reservas,
                    ]);
                    
                    return view('pago.exito', ['reserva' => $reserva]);
                }
            } catch (\Exception $e) {
                // Manejar errores
                Log::error('Error al verificar el pago: ' . $e->getMessage());
                return redirect()->route('carrito')->with('error', 'Error al verificar el pago: ' . $e->getMessage());
            }
        }
        
        // Si no hay session_id o la verificación falló
        return redirect()->route('carrito')->with('error', 'No se pudo verificar el pago');
    }

    public function cancelado()
    {
        return view('pago.cancelado');
    }
    
    public function webhook(Request $request)
    {
        // Usar nuestro servicio personalizado de Stripe
        $stripeService = new StripeService();
        
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        
        try {
            // Verificar el evento de Stripe con nuestro servicio
            $event = $stripeService->constructEventFromWebhook(
                $payload, $sig_header
            );
            
            if (!$event) {
                Log::error('No se pudo verificar el evento de webhook de Stripe');
                return response()->json(['error' => 'Webhook error'], 400);
            }
            
            // Manejar el evento
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    
                    // Obtener la reserva y actualizar su estado
                    if (isset($session->metadata->id_reserva)) {
                        $reserva = Reserva::find($session->metadata->id_reserva);
                        
                        if ($reserva) {
                            $reserva->estado = 'pagado';
                            $reserva->save();
                            
                            // Registrar el pago
                            Pago::create([
                                'estado_pago' => 'completado',
                                'fecha_pago' => now(),
                                'referencia_externa' => $session->payment_intent,
                                'monto_pagado' => $session->metadata->total,
                                'total_precio' => $session->metadata->total,
                                'moneda' => 'EUR',
                                'id_usuario' => $reserva->id_usuario,
                                'id_reservas' => $reserva->id_reservas,
                            ]);
                        }
                    }
                    break;
            }
            
            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}