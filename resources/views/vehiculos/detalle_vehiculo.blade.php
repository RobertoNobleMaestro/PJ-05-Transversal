<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Vehiculos/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@include('layouts.navbar')

<div class="breadcrumb-container">
    <div class="container">
        <small>Inicio > Alquiler vehiculos > {{ $vehiculo->tipo->nombre }} > {{ $vehiculo->marca }} > {{ $vehiculo->modelo }}</small>
    </div>
</div>

<div class="container vehiculo-detail-section">
    <div class="row">
        <div class="col-md-6">
            <div class="imagen-box text-center">
                <img src="{{ asset('img/' . $vehiculo->imagen) }}" class="img-fluid mb-3" alt="">
                <img src="{{ asset('img/mercedes.png') }}" class="img-fluid" alt="">
            </div>
        </div>
        <div class="col-md-6">
            <p class="text-muted">
                Publicado: {{ $vehiculo->created_at->format('d/m/Y H:i') }} | 
                Modificado: {{ $vehiculo->updated_at->format('d/m/Y H:i') }}
            </p>
            <h2>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
            <p>{{ $vehiculo->descripcion }}</p>

            <!-- Características en 4 filas de 2 columnas -->
            <div class="caracteristicas-box">
                <div class="row caracteristicas">
                    <div class="col-md-6"><i class="fas fa-cogs"></i> Transmisión: {{ $vehiculo->caracteristicas->transmision }}</div>
                    <div class="col-md-6"><i class="fas fa-car"></i> Tipo: {{ $vehiculo->tipo->nombre }}</div>

                    <div class="col-md-6"><i class="fas fa-tachometer-alt"></i> Kilometraje: {{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</div>
                    <div class="col-md-6"><i class="fas fa-map-marker-alt"></i> Ubicación: {{ $vehiculo->lugar->nombre }}</div>

                    <div class="col-md-6"><i class="fas fa-snowflake"></i> Aire acondicionado: {{ $vehiculo->caracteristicas->aire_acondicionado ? 'Sí' : 'No' }}</div>
                    <div class="col-md-6"><i class="fas fa-sun"></i> Techo solar: {{ $vehiculo->caracteristicas->techo ? 'Sí' : 'No' }}</div>

                    <div class="col-md-6"><i class="fas fa-suitcase"></i> Maletero: {{ $vehiculo->caracteristicas->capacidad_maletero }} L</div>
                    <div class="col-md-6"><i class="fas fa-shield-alt"></i> Seguro incluido: {{ $vehiculo->seguro_incluido ? 'Sí' : 'No' }}</div>
                </div>
            </div>

            <!-- Carrito con estilo destacado -->
            <div class="highlight-box">
                <button id="btnAñadirCarrito" 
                        class="btn w-100 d-flex align-items-center"
                        data-vehiculo-id="{{ $vehiculo->id_vehiculos }}">
                    <i class="fas fa-shopping-cart fa-bounce mr-3"></i> 
                    <div>
                        <strong>¡Añade este vehículo a tu carrito!</strong><br>
                        Guarda tus búsquedas favoritas en el carrito para compararlas más tarde.
                    </div>
                </button>
            </div>

        </div>
    </div>

    <hr>

    <!-- Valoraciones con Fetch API -->
    <h4 class="mt-5">VALORACIONES</h4>
    <div id="valoraciones-container">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Cargando valoraciones...</span>
            </div>
            <p>Cargando valoraciones...</p>
        </div>
    </div>
</div>

<footer>
    <div class="container text-center">
        <p class="m-0">Carflow &copy; 2025</p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/vehiculos.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        iniciarDetalleVehiculo({{ $vehiculo->id_vehiculos }});
    });
</script>
</body>
</html>
