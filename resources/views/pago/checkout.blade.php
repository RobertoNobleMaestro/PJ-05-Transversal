<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pago | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Carrito/checkout.css') }}">
    <script src="https://js.stripe.com/v3/"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('layouts.navbar')
    <div style="padding-left: 30px; padding-top: 30px; padding-bottom: 30px;">
        <a href="{{ asset('home') }}" class="btn-volver">
          <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
      </div>
    <div class="container pago-container">
        <h2 class="mb-4">Finalizar Pago</h2>
        
        <div class="row">
            <div class="col-md-7">
                <div class="total-box">
                    <h5>Resumen de tu reserva</h5>
                    <hr>
                    @if($reserva->vehiculosReservas->count() > 0)
                        @foreach($reserva->vehiculosReservas as $vr)
                            <div class="reservation-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="vehicle-details">
                                        <strong class="vehicle-title">{{ $vr->vehiculo->marca }} {{ $vr->vehiculo->modelo }}</strong>
                                        <div class="text-muted">
                                            <i class="far fa-calendar-alt mr-2"></i>
                                            Del {{ date('d/m/Y', strtotime($vr->fecha_ini)) }} al {{ date('d/m/Y', strtotime($vr->fecha_final)) }}
                                        </div>
                                        <div class="text-muted">
                                        <i class="fas fa-clock mr-2"></i>
                                        @php
                                            // Corregir el cálculo de los días
                                            $dias = max(1, Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final) + 1);
                                            $diasTexto = $dias == 1 ? '1 día' : $dias . ' días';
                                        @endphp
                                        {{ $diasTexto }}
                                    </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="price-details">
                                            <div class="price-per-day">{{ number_format($vr->vehiculo->precio_dia, 2, ',', '.') }}€/día</div>
                                            <div class="total-price">
                                                {{ number_format($vr->vehiculo->precio_dia * $dias, 2, ',', '.') }}€
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="final-total">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>Total a pagar:</strong>
                                    <div class="text-muted small">{{ $reserva->vehiculosReservas->count() }} {{ $reserva->vehiculosReservas->count() == 1 ? 'vehículo' : 'vehículos' }}</div>
                                </div>
                                <div class="total-price">
                                    @php
                                        $total = 0;
                                        foreach($reserva->vehiculosReservas as $vr) {
                                            // Incluir el día final sumando 1 al resultado de diffInDays
                                            $dias = max(1, Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final) + 1);
                                            $total += $vr->vehiculo->precio_dia * $dias;
                                        }
                                    @endphp
                                    {{ number_format($total, 2, ',', '.') }}€
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            No hay vehículos en tu cesta de reserva.
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="payment-box">
                    <h5>Método de pago</h5>
                    
                    <div class="payment-icons">
                        <i class="fab fa-cc-visa payment-icon"></i>
                        <i class="fab fa-cc-mastercard payment-icon"></i>
                        <i class="fab fa-cc-amex payment-icon"></i>
                        <i class="fab fa-cc-apple-pay payment-icon"></i>
                        <i class="fab fa-cc-paypal payment-icon"></i>
                    </div>
                    
                    <div class="secure-payment-text">
                        <i class="fas fa-shield-alt"></i>
                        Serás redirigido a la pasarela de pago segura de Stripe para completar tu compra.
                    </div>
                    
                    <button id="checkout-button" class="btn-stripe">
                        <i class="fas fa-lock"></i>
                        Pagar ahora con Stripe
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stripe = Stripe('{{ $stripe_public_key }}');
            const checkoutButton = document.getElementById('checkout-button');
            
            checkoutButton.addEventListener('click', function() {
                stripe.redirectToCheckout({
                    sessionId: '{{ $stripe_session_id }}'
                }).then(function(result) {
                    if (result.error) {
                        alert(result.error.message);
                    }
                });
            });
        });
    </script>
</body>
</html>
