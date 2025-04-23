<?php

namespace App\Services\Stripe;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class StripeService
{
    private $secretKey;
    private $publicKey;
    private $baseUrl = 'https://api.stripe.com/v1/';

    public function __construct()
    {
        $this->secretKey = env('STRIPE_SECRET');
        $this->publicKey = env('STRIPE_KEY');
    }

    /**
     * Crear una sesión de checkout para el pago
     *
     * @param float $amount Monto a pagar en la moneda base (EUR)
     * @param string $description Descripción del pago
     * @param array $metadata Metadatos adicionales para el pago
     * @param string $successUrl URL de redirección en caso de éxito
     * @param string $cancelUrl URL de redirección en caso de cancelación
     * @return array Respuesta de la API de Stripe
     */
    public function createCheckoutSession($amount, $description, $metadata = [], $successUrl = null, $cancelUrl = null)
    {
        // Asegurarse de que las URLs de redirección existan
        if (!$successUrl) {
            $successUrl = route('pago.exito', ['id_reserva' => $metadata['id_reserva'] ?? 0]);
        }
        
        if (!$cancelUrl) {
            $cancelUrl = route('pago.cancelado');
        }
        
        try {
            // Convertir el monto a centavos (Stripe usa la unidad más pequeña de la moneda)
            $amountInCents = round($amount * 100);
            
            // Realizar solicitud a la API de Stripe para crear una sesión de checkout
            $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . base64_encode($this->secretKey . ':'),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ])
                ->post($this->baseUrl . 'checkout/sessions', [
                    'payment_method_types[]' => 'card',
                    'line_items[0][price_data][currency]' => 'eur',
                    'line_items[0][price_data][product_data][name]' => 'Reserva de Vehículo',
                    'line_items[0][price_data][product_data][description]' => $description,
                    'line_items[0][price_data][unit_amount]' => $amountInCents,
                    'line_items[0][quantity]' => 1,
                    'mode' => 'payment',
                    'success_url' => $successUrl,
                    'cancel_url' => $cancelUrl,
                    'metadata[id_reserva]' => $metadata['id_reserva'] ?? '',
                    'metadata[id_usuario]' => $metadata['id_usuario'] ?? '',
                    'metadata[total]' => $metadata['total'] ?? '',
                ]);
            
            $responseData = $response->json();
            
            // Registrar la respuesta para depuración
            Log::info('Respuesta completa de Stripe: ' . json_encode($responseData));
            
            // Asegurarnos de que la URL esté presente en la respuesta
            if (isset($responseData['id']) && !isset($responseData['url'])) {
                $responseData['url'] = 'https://checkout.stripe.com/pay/' . $responseData['id'];
            }
            
            return $responseData;
        } catch (\Exception $e) {
            Log::error('Error al crear sesión de Stripe: ' . $e->getMessage());
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Verificar la autenticidad de un evento de webhook
     *
     * @param string $payload El cuerpo de la solicitud como cadena JSON
     * @param string $sigHeader El encabezado de firma proporcionado por Stripe
     * @return bool|object Objeto de evento si es válido, false si no
     */
    public function constructEventFromWebhook($payload, $sigHeader)
    {
        try {
            // Esta implementación es simplificada y no verifica criptográficamente la firma
            // En un entorno de producción, se recomienda usar la biblioteca oficial para esto
            $event = json_decode($payload);
            
            // Registrar el evento para depuración
            Log::info('Evento de Stripe recibido: ' . $event->type);
            
            return $event;
        } catch (\Exception $e) {
            Log::error('Error al procesar webhook de Stripe: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener el enlace de pago desde una sesión
     *
     * @param array $session La sesión devuelta por createCheckoutSession
     * @return string|null URL de pago o null si hay error
     */
    public function getPaymentUrl($session)
    {
        return $session['url'] ?? null;
    }

    /**
     * Verificar si un pago fue completado exitosamente
     *
     * @param string $sessionId ID de la sesión de checkout
     * @return bool True si el pago fue exitoso
     */
    public function isPaymentSuccessful($sessionId)
    {
        try {
            $response = Http::withBasicAuth($this->secretKey, '')
                ->get($this->baseUrl . 'checkout/sessions/' . $sessionId);
            
            $session = $response->json();
            
            // Verificar si el pago fue completado
            return isset($session['payment_status']) && $session['payment_status'] === 'paid';
        } catch (\Exception $e) {
            Log::error('Error al verificar estado de pago: ' . $e->getMessage());
            return false;
        }
    }
}
