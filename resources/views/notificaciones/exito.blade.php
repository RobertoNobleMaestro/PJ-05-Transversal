<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Carrito/exito.css') }}">
</head>
<body>
    @include('layouts.navbar')

    <div class="container pago-container py-5">
        <div class="success-card mx-auto" style="max-width: 600px;">
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-check-circle success-icon" style="font-size: 90px;"></i>
                </div>
                <h2 class="mb-4">¡Pago completado con éxito!</h2>
                <p class="success-message">Tu servicio de chofer ha sido confirmado. Puedes gestionar tus servicios desde tu perfil.</p>
                <div class="reference-box">
                    <p class="mb-2"><strong>Referencia de la solicitud:</strong> #{{ $solicitud->id_solicitud }}</p>
                    <p class="mb-2"><strong>Fecha de solicitud:</strong> {{ date('d/m/Y', strtotime($solicitud->fecha_solicitud)) }}</p>
                    <p class="mb-2"><strong>Chofer:</strong> {{ $solicitud->chofer_nombre }}</p>
                    <p class="mb-0"><strong>Total pagado:</strong> {{ number_format($solicitud->precio, 2, ',', '.') }}€</p>
                </div>
                
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home me-2"></i>Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 