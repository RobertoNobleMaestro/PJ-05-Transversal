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
                <p class="success-message">Tu reserva ha sido confirmada. Puedes gestionar tus reservas desde tu perfil.</p>
                <div class="reference-box">
                    <p class="mb-2"><strong>Referencia de la reserva:</strong> #{{ $reserva->id_reservas }}</p>
                    <p class="mb-0"><strong>Fecha de reserva:</strong> {{ date('d/m/Y', strtotime($reserva->fecha_reserva)) }}</p>
                </div>
                
                <!-- Mostrar vehículos reservados con sus imágenes -->
                <div class="reserved-vehicles mt-4">
                    <h5 class="mb-3">Vehículos reservados:</h5>
                    <div class="row justify-content-center">
                        @foreach ($reserva->vehiculosReservas as $vr)
                            <div class="col-md-4 mb-3">
                                <div class="vehicle-card p-2 text-center">
                                    @php
                                        // Obtener las imágenes directamente de la base de datos para este vehículo
                                        $imagenes = \App\Models\ImagenVehiculo::where('id_vehiculo', $vr->vehiculo->id_vehiculos)->get();
                                    @endphp
                                    
                                    @if($imagenes && count($imagenes) > 0)
                                        <img src="{{ asset('storage/' . $imagenes[0]->ruta_imagen) }}" alt="{{ $vr->vehiculo->marca }}" style="width: 100%; height: 80px; object-fit: cover; border-radius: 5px; margin-bottom: 8px;">
                                    @else
                                        <div style="width: 100%; height: 80px; background: #e0e0e0; border-radius: 5px; display: flex; align-items: center; justify-content: center; margin-bottom: 8px;">
                                            <i class="fas fa-car" style="font-size: 24px; color: #aaa;"></i>
                                        </div>
                                    @endif
                                    <p class="mb-0 font-weight-bold">{{ $vr->vehiculo->marca }} {{ $vr->vehiculo->modelo }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="fas fa-home mr-2"></i>Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
