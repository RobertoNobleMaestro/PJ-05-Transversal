<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class PagoController extends Controller
{
    public function checkout()
    {
        // Obtener la reserva pendiente del usuario con sus vehículos
        $reserva = Reserva::where('id_usuario', Auth::id())
                         ->where('estado', 'pendiente')
                         ->with(['vehiculos', 'vehiculosReservas', 'vehiculosReservas.vehiculo']) // Cargamos las relaciones necesarias
                         ->first();

        if (!$reserva) {
            return redirect()->route('carrito')->with('error', 'No tienes ninguna reserva pendiente');
        }

        // Calcular el precio total de la reserva
        $totalPrecio = 0;
        foreach ($reserva->vehiculosReservas as $vr) {
            $totalPrecio += $vr->vehiculo->precio_dia;
        }

        // Actualizar la reserva con el precio total
        $reserva->total_precio = $totalPrecio;
        $reserva->save();

        // Configurar Stripe con la clave secreta
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // Preparar los items para Stripe
            $line_items = [];
            
            foreach ($reserva->vehiculosReservas as $vr) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $vr->vehiculo->marca . ' ' . $vr->vehiculo->modelo,
                            'description' => 'Reserva del ' . date('d/m/Y', strtotime($vr->fecha_ini)) . ' al ' . date('d/m/Y', strtotime($vr->fecha_final)),
                            'images' => [$vr->vehiculo->imagenes()->first() ? asset('img/' . $vr->vehiculo->imagenes()->first()->ruta) : asset('img/default-car.png')],
                        ],
                        'unit_amount' => intval($vr->vehiculo->precio_dia * 100), // Stripe trabaja en centavos
                    ],
                    'quantity' => 1,
                ];
            }

            // Crear la sesión de Stripe Checkout
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('pago.exito', ['id_reserva' => $reserva->id_reservas]) . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('pago.cancelado'),
                'customer_email' => Auth::user()->email,
                'metadata' => [
                    'id_reserva' => $reserva->id_reservas,
                    'id_usuario' => Auth::id(),
                ],
            ]);

            // Guardar el ID de la sesión en la reserva para referencia futura
            $reserva->referencia_pago = $session->id;
            $reserva->save();

            return view('pago.checkout', [
                'reserva' => $reserva,
                'stripe_session_id' => $session->id,
                'stripe_public_key' => env('STRIPE_KEY'),
            ]);
        } catch (ApiErrorException $e) {
            // Manejo de errores de la API de Stripe
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
        
        // Verificar el estado de la sesión de Stripe
        if ($request->has('session_id')) {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            
            try {
                $session = Session::retrieve($request->session_id);
                
                // Verificar que la sesión está pagada
                if ($session->payment_status === 'paid') {
                    // Actualizar el estado de la reserva
                    $reserva->estado = 'pagado';
                    $reserva->save();
                    
                    // Registrar el pago en nuestro sistema
                    Pago::create([
                        'estado_pago' => 'completado',
                        'fecha_pago' => now(),
                        'referencia_externa' => $session->payment_intent,
                        'monto_pagado' => $reserva->total_precio,
                        'total_precio' => $reserva->total_precio,
                        'moneda' => 'EUR',
                        'id_usuario' => Auth::id(),
                        'id_reservas' => $reserva->id_reservas,
                    ]);
                    
                    return view('pago.exito', ['reserva' => $reserva]);
                }
            } catch (ApiErrorException $e) {
                // Manejar errores de Stripe
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
                                'monto_pagado' => $reserva->total_precio,
                                'total_precio' => $reserva->total_precio,
                                'moneda' => 'EUR',
                                'id_usuario' => $reserva->id_usuario,
                                'id_reservas' => $reserva->id_reservas,
                            ]);
                        }
                    }
                    break;
                // Puedes manejar más eventos aquí
            }
            
            return response()->json(['status' => 'success']);
        } catch (\UnexpectedValueException $e) {
            // Error de firma inválida
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Error de firma inválida
            return response()->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            // Otro error
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
