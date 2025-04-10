<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Exitoso | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
</head>
<body>
    @include('layouts.navbar')

    <div class="container py-5 text-center">
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                </div>
                <h2 class="mb-3">¡Pago completado con éxito!</h2>
                <p class="mb-4">Tu reserva ha sido confirmada. Puedes gestionar tus reservas desde tu perfil.</p>
                <p class="mb-1"><strong>Referencia de la reserva:</strong> #{{ $reserva->id_reservas }}</p>
                <p class="mb-3"><strong>Fecha de reserva:</strong> {{ date('d/m/Y', strtotime($reserva->fecha_reserva)) }}</p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">Volver al inicio</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>