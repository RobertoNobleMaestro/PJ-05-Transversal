<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Pago Cancelado | Carflow</title>
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
                    <i class="fas fa-times-circle text-danger" style="font-size: 80px;"></i>
                </div>
                <h2 class="mb-3">Pago Cancelado</h2>
                <p class="mb-4">El proceso de pago ha sido cancelado. Tu carrito sigue disponible si deseas completar la compra más tarde.</p>
                <div class="mt-4">
                    <a href="{{ route('carrito') }}" class="btn btn-primary">Volver al carrito</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>