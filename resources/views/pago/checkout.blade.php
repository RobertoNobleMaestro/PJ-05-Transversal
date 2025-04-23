<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pago | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Carrito/checkout.css') }}">
    <!-- No necesitamos el SDK de Stripe ya que usamos redirección directa -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('layouts.navbar')
    
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
                                                $dias = max(1, Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final));
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
                                        $dias = max(1, Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final));
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
                    
                    <a href="{{ $payment_url }}" id="checkout-button" class="btn-stripe" target="_self">
                        <i class="fas fa-lock"></i>
                        Pagar ahora con Stripe
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutButton = document.getElementById('checkout-button');
            const paymentUrl = "{{ $payment_url }}";
            
            // Comprobamos si tenemos una URL de pago válida
            if (!paymentUrl) {
                console.error('No se ha proporcionado una URL de pago válida');
                return;
            }
            
            // Imprimimos la URL de pago en la consola para depuración
            console.log('URL de pago:', paymentUrl);
            
            // Añadimos un manejador de eventos adicional para garantizar la redirección
            checkoutButton.addEventListener('click', function(e) {
                e.preventDefault(); // Detenemos el comportamiento predeterminado del enlace
                
                // Mostramos animación de carga
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirigiendo...';
                this.classList.add('loading');
                
                // Redirigimos a la página de pago de Stripe tras un breve retraso
                setTimeout(function() {
                    window.location.href = paymentUrl;
                }, 500);
            });
        });
    </script>
</body>
</html>
