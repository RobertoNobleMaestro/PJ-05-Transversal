<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pago | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
    <script src="https://js.stripe.com/v3/"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .pago-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .total-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .payment-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .btn-stripe {
            background-color: #635bff;
            color: white;
            padding: 12px 16px;
            border-radius: 4px;
            border: none;
            font-weight: 600;
            display: inline-block;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-stripe:hover {
            background-color: #4f46e5;
            color: white;
            text-decoration: none;
        }
        .payment-icons {
            margin-bottom: 20px;
        }
        .payment-icon {
            font-size: 24px;
            margin: 0 5px;
            color: #6c757d;
        }
    </style>
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
                    @foreach($reserva->vehiculosReservas as $vr)
                        <div class="d-flex justify-content-between mb-2">
                            <div>
                                <strong>{{ $vr->vehiculo->marca }} {{ $vr->vehiculo->modelo }}</strong>
                                <div>Del {{ date('d/m/Y', strtotime($vr->fecha_ini)) }} al {{ date('d/m/Y', strtotime($vr->fecha_final)) }}</div>
                            </div>
                            <div class="text-right">
                                <strong>€{{ number_format($vr->precio_unitario, 2, ',', '.') }}</strong>
                            </div>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <div><strong>Total a pagar:</strong></div>
                        <div><strong>€{{ number_format($reserva->total_precio, 2, ',', '.') }}</strong></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="payment-box">
                    <h5 class="mb-3">Método de pago</h5>
                    
                    <div class="payment-icons">
                        <i class="fab fa-cc-visa payment-icon"></i>
                        <i class="fab fa-cc-mastercard payment-icon"></i>
                        <i class="fab fa-cc-amex payment-icon"></i>
                        <i class="fab fa-cc-apple-pay payment-icon"></i>
                        <i class="fab fa-cc-paypal payment-icon"></i>
                    </div>
                    
                    <p>Serás redirigido a la pasarela de pago segura de Stripe para completar tu compra.</p>
                    
                    <button id="checkout-button" class="btn-stripe">
                        Pagar ahora con Stripe <i class="fas fa-lock ml-2"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configurar Stripe
            const stripe = Stripe('{{ $stripe_public_key }}');
            const checkoutButton = document.getElementById('checkout-button');
            
            // Manejar el clic en el botón de pago
            checkoutButton.addEventListener('click', function() {
                // Redirigir a la página de checkout de Stripe
                stripe.redirectToCheckout({
                    sessionId: '{{ $stripe_session_id }}'
                }).then(function(result) {
                    // Si hay un error, mostrar un mensaje
                    if (result.error) {
                        alert(result.error.message);
                    }
                });
            });
        });
    </script>
</body>
</html>