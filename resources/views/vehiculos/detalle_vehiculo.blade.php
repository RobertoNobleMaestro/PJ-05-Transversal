<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>{{ $vehiculo->marca }} - {{ $vehiculo->modelo }} | Carflow</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">
</head>
<body>

  <!-- Navbar -->
  @include('layouts.navbar')

  <!-- Breadcrumb / Ruta de navegación -->
  <div class="breadcrumb-container">
    <div class="container">
      <small>Inicio &gt; Vehículo: {{ $vehiculo->marca }} {{ $vehiculo->modelo }}</small>
    </div>
  </div>

  <!-- Detalle del Vehículo -->
  <div class="container vehiculo-detail-section">
    <div class="row">
      <div class="col-md-6">
        <h1>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</h1>
        <p><strong>Año:</strong> {{ $vehiculo->año }}</p>
        <p><strong>Kilometraje:</strong> {{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</p>
        <p><strong>Tipo:</strong> {{ $vehiculo->tipo->nombre }}</p>
        <p><strong>Ubicación:</strong> {{ $vehiculo->lugar->nombre }}</p>
        <p><strong>Seguro incluido:</strong> {{ $vehiculo->seguro_incluido ? 'Sí' : 'No' }}</p>
        
        <!-- Aquí agregar más detalles del vehículo según sea necesario -->

        <h4>Características</h4>
        <ul>
          <li><strong>Techo solar:</strong> {{ $vehiculo->caracteristicas->techo ? 'Sí' : 'No' }}</li>
          <li><strong>Transmisión:</strong> {{ $vehiculo->caracteristicas->transmision }}</li>
          <li><strong>Aire acondicionado:</strong> {{ $vehiculo->caracteristicas->aire_acondicionado ? 'Sí' : 'No' }}</li>
          <li><strong>Capacidad del maletero:</strong> {{ $vehiculo->caracteristicas->capacidad_maletero }} L</li>
        </ul>
        
        <a href="#" class="btn btn-primary">Reservar ahora</a>
      </div>

      <div class="col-md-6 text-center">
        <!-- Imagen del vehículo -->
        <img src="{{ asset('img/coches.png') }}" class="img-fluid mb-2" alt="{{ $vehiculo->marca }} {{ $vehiculo->modelo }}">
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container text-center">
      <p class="m-0">Carflow &copy; 2025</p>
    </div>
  </footer>

</body>
</html>
