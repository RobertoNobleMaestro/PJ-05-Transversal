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
            background-color: #f0f0f0;
        }

        .breadcrumb-container {
            background-color: #f4f4f4;
            padding: 15px 0;
            font-size: 14px;
            color: #555;
        }

        .vehiculo-detail-section {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 0;
            margin-top: 20px;
            box-shadow: none;
        }

        .highlight-box {
            background-color: #fff8c4;
            color: #000;
            font-weight: 600;
            font-size: 15px;
            padding: 20px;
            margin-top: 30px;
            display: flex;
            align-items: center;
            border-left: 5px solid #ffcc00;
            border-radius: 8px;
            box-shadow: 0 1px 5px rgba(0,0,0,0.1);
            gap: 15px;
        }

        .highlight-box i {
            font-size: 28px;
            color: #e6b800;
        }

        .caracteristicas-box {
            background-color: #f7f7f7;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .caracteristicas .col-md-6 {
            margin-bottom: 15px;
            font-size: 15px;
            color: #333;
        }

        .caracteristicas i {
            color: #000;
            margin-right: 8px;
        }

        .valoracion {
            background-color: #f9f9f9;
            border-left: 5px solid #999;
            padding: 20px;
            margin-top: 20px;
        }

        .valoracion i {
            color: gold;
            font-size: 18px;
        }

        .valoracion strong {
            font-size: 16px;
        }

        h2 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        h4 {
            font-size: 20px;
            font-weight: bold;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .text-muted {
            color: #888 !important;
            font-size: 13px;
        }

        footer {
            margin-top: 50px;
            padding: 20px;
            background-color: #222;
            color: white;
            text-align: center;
        }

        .imagen-box {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            padding: 15px;
        }

        .imagen-box img {
            max-width: 100%;
            height: auto;
            margin-bottom: 15px;
        }

        /* Animación del carrito (opcional) */
        .fa-bounce {
            animation: bounce 1.5s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
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
                <!-- Enlace al carrito -->
                <a href="{{ route('carrito') }}" class="d-flex align-items-center text-decoration-none text-dark">
                    <i class="fas fa-shopping-cart fa-bounce mr-3"></i> 
                    <div>
                        <strong>¡Añade este vehículo a tu carrito!</strong><br>
                        Guarda tus búsquedas favoritas en el carrito para compararlas más tarde.
                    </div>
                </a>
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
                    <img id="navbar-profile-img" src="{{ asset(Auth::user()->foto_perfil ? 'img/' . Auth::user()->foto_perfil : 'img/default.png') }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;" alt="Perfil">
                    <strong>{{ $valoracion->usuario->nombre }}</strong>
                </p>
                <p>
                    @for ($i = 0; $i < $valoracion->valoracion; $i++) 
                        <i class="fas fa-star"></i>
                    @endfor
                    @for ($i = $valoracion->valoracion; $i < 5; $i++)
                        <i class="far fa-star"></i>
                    @endfor
                </p>
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
