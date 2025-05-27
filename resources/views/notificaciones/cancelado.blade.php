<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Cancelado | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Carrito/cancelado.css') }}">
</head>
<body>
    @include('layouts.navbar')

    <div class="container pago-container py-5">
        <div class="cancel-card mx-auto" style="max-width: 600px;">
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-times-circle cancel-icon" style="font-size: 90px;"></i>
                </div>
                <h2 class="mb-4">Pago Cancelado</h2>
                <p class="cancel-message">El proceso de pago ha sido cancelado. Tu solicitud sigue disponible si deseas completar el pago más tarde.</p>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn-cancelar">
                        <i class="fas fa-home mr-2"></i>Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 