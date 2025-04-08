<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9f9f9;
        }
        .breadcrumb-container {
            background-color: #e9ecef;
            padding: 10px 0;
        }
        .vehiculo-detail-section {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .highlight-box {
            background-color: #fffcdb;
            border-left: 6px solid #ffcc00;
            padding: 15px;
            margin-top: 20px;
            font-weight: 500;
        }
        .valoracion i {
            color: gold;
        }
        ul.caracteristicas {
            list-style: none;
            padding: 0;
        }
        ul.caracteristicas li {
            padding: 5px 0;
        }
        ul.caracteristicas li i {
            color: #007bff;
            margin-right: 6px;
        }
        footer {
            margin-top: 50px;
            padding: 20px;
            background-color: #222;
            color: white;
        }
    </style>
</head>
<body>

@include('layouts.navbar')

<div class="breadcrumb-container">
    <div class="container">
        <small>Inicio > Alquiler vehiculos > {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</small>
    </div>
</div>

<div class="container vehiculo-detail-section">
    <div class="row">
        <div class="col-md-6 text-center">
            <img src="{{ asset('img/' . $vehiculo->imagen) }}" class="img-fluid" alt="{{ $vehiculo->marca }}">
        </div>
        <div class="col-md-6">
            <p class="text-muted">
                Publicado: {{ $vehiculo->created_at->format('d/m/Y H:i') }} | 
                Modificado: {{ $vehiculo->updated_at->format('d/m/Y H:i') }}
            </p>
            <h2>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h2>
            <p>{{ $vehiculo->descripcion }}</p>

            <!-- Características -->
            <h4 class="mt-4">Características</h4>
            <ul class="caracteristicas">
                <li><i class="fas fa-cogs"></i> Transmisión: {{ $vehiculo->caracteristicas->transmision }}</li>
                <li><i class="fas fa-car"></i> Tipo: {{ $vehiculo->tipo->nombre }}</li>
                <li><i class="fas fa-tachometer-alt"></i> Kilometraje: {{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</li>
                <li><i class="fas fa-map-marker-alt"></i> Ubicación: {{ $vehiculo->lugar->nombre }}</li>
                <li><i class="fas fa-snowflake"></i> Aire acondicionado: {{ $vehiculo->caracteristicas->aire_acondicionado ? 'Sí' : 'No' }}</li>
                <li><i class="fas fa-sun"></i> Techo solar: {{ $vehiculo->caracteristicas->techo ? 'Sí' : 'No' }}</li>
                <li><i class="fas fa-suitcase"></i> Capacidad del maletero: {{ $vehiculo->caracteristicas->capacidad_maletero }} L</li>
                <li><i class="fas fa-shield-alt"></i> Seguro incluido: {{ $vehiculo->seguro_incluido ? 'Sí' : 'No' }}</li>
            </ul>

            <div class="highlight-box">
                <i class="fas fa-shopping-cart"></i> Guarda tus búsquedas favoritas en el carrito!
            </div>
        </div>
    </div>

    <hr>

    <!-- Valoraciones -->
    <h4 class="mt-5">VALORACIONES</h4>

    @if ($vehiculo->valoraciones->count())
        @foreach ($vehiculo->valoraciones as $valoracion)
            <div class="valoracion mb-4">
                <p>
                    <strong><i class="fas fa-user-circle"></i> {{ $valoracion->usuario->name }}</strong>
                    {{-- <small class="text-muted">{{ $valoracion->created_at->format('d M Y') }}</small> --}}
                </p>
                {{-- <p>
                    @for ($i = 0; $i < $valoracion->calificacion; $i++)
                        <i class="fas fa-star"></i>
                    @endfor
                    @for ($i = $valoracion->calificacion; $i < 5; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                </p> --}}
                <p>{{ $valoracion->comentario }}</p>
            </div>
        @endforeach
    @else
        <p class="text-muted">Este vehículo aún no tiene valoraciones.</p>
    @endif
</div>

<footer>
    <div class="container text-center">
        <p class="m-0">Carflow &copy; 2025</p>
    </div>
</footer>

</body>
</html>
