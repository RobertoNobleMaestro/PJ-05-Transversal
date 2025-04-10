<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carflow - Alquiler de vehículos</title>
  <!-- Enlace a Bootstrap (versión 4.x o 5.x) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">
  <script src="{{ asset('js/home.js') }}"></script>
  <title>Carflow - Alquiler de vehículos</title>
</head>
<body>

  <!-- Navbar -->
  @include('layouts.navbar')

  <!-- Breadcrumb / Ruta de navegación -->
  <div class="breadcrumb-container">
    <div class="container">
      <small>Inicio &gt; Alquiler coches &gt; <span id="breadcrumb-tipo">Coche</span></small>
    </div>
  </div>

  <!-- Sección principal (Hero) -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-6">
        <h1>Alquiler de vehículos de todo tipo<br>y con precios asequibles</h1>
        <!-- Formulario de filtros -->
        <form class="filter-form">
          <div class="form-group">
            <label>Tipo de vehículo:</label><br>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
              @foreach ($tipos as $index => $tipo)
                <label class="btn {{ $index === 0 ? 'active' : '' }}">
                  <input 
                    type="radio" 
                    name="tipoVehiculo" 
                    value="{{ $tipo->nombre }}" 
                    autocomplete="off" 
                    {{ $index === 0 ? 'checked' : '' }}>
                  {{ strtoupper($tipo->nombre) }}
                </label>
              @endforeach
            </div>            
          </div>               
          <div class="form-row">
            <div class="form-group col-md-5">
              <label for="ubicacion">Ciudad:</label>
              <input type="text" class="form-control" id="ubicacion" value="Barcelona">
            </div>
            <div class="form-group col-md-5">
              <label for="fecha">Fecha de reserva:</label>
              <input type="date" class="form-control" id="fecha">
            </div>
            <div class="form-group col-md-2 d-flex align-items-end">
              <button type="submit" class="btn btn-primary w-100">Buscar</button>
            </div>
          </div>          
        </form>
      </div>
      <div class="col-md-6 text-center">
        <!-- Ejemplo de imágenes a la derecha -->
        <img src="{{ asset('img/coches.png') }}" class="img-fluid mb-2" alt="Auto 1">
      </div>
    </div>
  </div> 
  
  <!-- Sección de estadísticas -->
  <div class="container-fluid stats-section">
    <div class="row no-gutters text-center">
      <div class="col-12 col-sm-4">
        <div class="stat-box">
          <i class="fas fa-users"></i>
          <div class="stat-content">
            <h3>{{ $usuariosClientes }}</h3>
            <p>Usuarios registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-4">
        <div class="stat-box">
          <i class="fas fa-car"></i>
          <div class="stat-content">
            <h3>{{ number_format($vehiculos, 0, ',', '.') }}</h3>
            <p>Vehículos registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-4">
        <div class="stat-box">
          <i class="fas fa-star"></i>
          <div class="stat-content">
            <h3>{{ $valoracionMedia }}</h3>
            <p>Valoración de la web</p>
          </div>
        </div>
      </div>
    </div>
  </div>    

  <div class="container vehicles-section">
    <h2>Alquila vehiculos</h2>
    <div class="row" id="vehiculos-container">
      <!-- Aquí se insertarán los vehículos dinámicamente -->
    </div>
  </div>  

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>