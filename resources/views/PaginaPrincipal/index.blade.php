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
          <i class="fas fa-users" style="color: #9F17BD"></i>
          <div class="stat-content">
            <h3>{{ $usuariosClientes }}</h3>
            <p>Usuarios registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-4">
        <div class="stat-box">
          <i class="fas fa-car" style="color: #9F17BD"></i>
          <div class="stat-content">
            <h3>{{ number_format($vehiculos, 0, ',', '.') }}</h3>
            <p>Vehículos registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-4">
        <div class="stat-box">
          <i class="fa-solid fa-star" style="color: #ffc800;"></i>
          <div class="stat-content">
            <h3>{{ $valoracionMedia }}</h3>
            <p>Valoración de la web</p>
          </div>
        </div>
      </div>
    </div>
  </div>    

  <div class="container vehicles-section">
    <h2>Alquila vehículos</h2>
  
    <div class="mb-4">
      <div id="filtros-form">
        <div class="form-row align-items-end">
          <!-- Filtro por marca -->
          <div class="form-group col-md">
            <label for="marcaFiltro">Marca:</label>
            <input type="text" id="marcaFiltro" class="form-control" placeholder="Ej. Toyota">
          </div>
    
          <!-- Filtro por año -->
          <div class="form-group col-md">
            <label for="anioFiltro">Año:</label>
            <select id="anioFiltro" class="form-control">
              <option value="">Todos</option>
              <!-- años dinámicos -->
            </select>
          </div>
    
          <!-- Filtro por precio mínimo -->
          <div class="form-group col-md">
            <label for="precioMin">Precio mín (€):</label>
            <input type="number" id="precioMin" class="form-control" placeholder="Mín">
          </div>
    
          <!-- Filtro por precio máximo -->
          <div class="form-group col-md">
            <label for="precioMax">Precio máx (€):</label>
            <input type="number" id="precioMax" class="form-control" placeholder="Máx">
          </div>

          <!-- Filtro por valoración mínima -->
          <div class="form-group col-md">
            <label for="valoracionMin">Valoración mínima:</label>
            <select id="valoracionMin" class="form-control">
              <option value="">Todas</option>
              <option value="5">5 ⭐</option>
              <option value="4">4 ⭐ o más</option>
              <option value="3">3 ⭐ o más</option>
              <option value="2">2 ⭐ o más</option>
              <option value="1">1 ⭐ o más</option>
            </select>
          </div>          
    
          <!-- Selector de cantidad por página -->
          <div class="form-group col-md">
            <label for="perPageInput">Vehículos/página:</label>
            <input id="perPageInput" type="number" class="form-control" value="8" min="1">
          </div>
    
          <!-- Paginación -->
          <div class="form-group col-md text-center">
            <div id="pagination-controls" class="btn-group d-block mb-1"></div>
            <div id="pagination-info" class="small text-muted"></div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Contenedor de tarjetas de vehículos -->
    <div class="row" id="vehiculos-container">
      <!-- Se insertan dinámicamente -->
    </div>
  </div>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>