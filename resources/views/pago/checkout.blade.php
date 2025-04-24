<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Finalizar Pago | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Carrito/checkout.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .payment-form {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: 600;
            font-size: 14px;
            color: #555;
            margin-bottom: 8px;
        }
        .btn-pay {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            padding: 12px 20px;
            border-radius: 5px;
            border: none;
            width: 100%;
            font-size: 16px;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn-pay:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }
        .card-field {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 12px 15px;
            font-size: 16px;
            width: 100%;
            transition: border-color 0.3s;
        }
        .card-field:focus {
            border-color: #007bff;
            outline: none;
        }
        .card-icons {
            display: flex;
            margin-bottom: 15px;
        }
        .card-icon {
            font-size: 28px;
            margin-right: 10px;
            color: #555;
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
                    <h5>Datos de pago</h5>

                    <div class="card-icons">
                        <i class="fab fa-cc-visa card-icon"></i>
                        <i class="fab fa-cc-mastercard card-icon"></i>
                        <i class="fab fa-cc-amex card-icon"></i>
                    </div>
                    
                    <form action="{{ route('pago.procesar') }}" method="POST" class="payment-form">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                        
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <div class="form-group">
                            <label for="nombre_tarjeta">Nombre del titular</label>
                            <input type="text" id="nombre_tarjeta" name="nombre_tarjeta" class="card-field" placeholder="Nombre que aparece en la tarjeta" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="numero_tarjeta">Número de tarjeta</label>
                            <input type="text" id="numero_tarjeta" name="numero_tarjeta" class="card-field" placeholder="1234 5678 9012 3456" maxlength="16" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="fecha_expiracion">Fecha expiración</label>
                                    <input type="text" id="fecha_expiracion" name="fecha_expiracion" class="card-field" placeholder="MM/AA" maxlength="5" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" class="card-field" placeholder="123" maxlength="3" required>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-pay">
                            <i class="fas fa-lock mr-2"></i>Pagar {{ number_format($total, 2, ',', '.') }}€
                        </button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('carrito') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left mr-1"></i>Volver al carrito
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Formatear campo de fecha de expiración (MM/YY)
            const fechaExpiracion = document.getElementById('fecha_expiracion');
            fechaExpiracion.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
            
            // Solo permitir números en el campo de número de tarjeta
            const numeroTarjeta = document.getElementById('numero_tarjeta');
            numeroTarjeta.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
            
            // Solo permitir números en el campo CVV
            const cvv = document.getElementById('cvv');
            cvv.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        });
    </script>
</body>
</html>
