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
      <small>Inicio &gt; Alquiler coches</small>
    </div>
  </div>

  <!-- Sección principal (Hero) -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-5">
        <h1>Alquiler de vehículos de todo tipo<br>y con precios asequibles</h1>
        <p>Encuentra el vehículo perfecto para tus necesidades, con una amplia variedad de opciones y precios competitivos.</p>
        <ul class="list-unstyled mt-4">
          <li><i class="fas fa-check-circle text-success mr-2"></i>Reserva cuando quieras</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Todo tipo de vehiculos</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Seguro incluido</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Sin comisiones</li>
        </ul>
        <a href="#alquiler" class="btn btn-light font-weight-bold">Encuentra ya tu vehiculo a reservar!</a>
      </div>
      <div class="col-md-6 text-center">
        <!-- Ejemplo de imágenes a la derecha -->
        <img src="{{ asset('img/coches.png') }}" class="img-fluid" alt="Auto 1">
      </div>
    </div>
  </div> 
  
  <!-- Sección de estadísticas -->
  <div class="container-fluid stats-section">
    <div class="row no-gutters text-center">
      <div class="col-12 col-sm-3">
        <div class="stat-box">
          <i class="fas fa-users" style="color: #9F17BD"></i>
          <div class="stat-content">
            <h3>{{ $usuariosClientes }}</h3>
            <p>Usuarios registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-3">
        <div class="stat-box">
          <i class="fas fa-car" style="color: #9F17BD"></i>
          <div class="stat-content">
            <h3>{{ number_format($vehiculos, 0, ',', '.') }}</h3>
            <p>Vehículos registrados</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-3">
        <div class="stat-box">
          <i class="fa-solid fa-star" style="color: #ffc800;"></i>
          <div class="stat-content">
            <h3>{{ $valoracionVehiculos }}</h3>
            <p>Valoración de los vehiculos</p>
          </div>
        </div>
      </div>
      <div class="col-12 col-sm-3">
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

  <div id="alquiler" class="container vehicles-section">
    <h2>Alquila vehículos</h2>
    <div class="row">
      <!-- Filtros laterales -->
      <div class="col-md-3">
        <div id="filtros-form" class="bg-white p-3 rounded shadow-sm sticky-top" style="top: 90px;">
          <h4 class="mb-3">Filtros</h4>

          <!-- Filtro por tipo de vehículo -->
          <div class="form-group">
            <label><strong>Tipo de vehículo:</strong></label>
            <div id="tipoVehiculoFiltro" class="form-check">
              <!-- Checkboxes insertados aquí -->
            </div>
          </div>          
          
          <div class="form-group col-m">
            <label><strong>Ciudad:</strong></label>
            <div id="lugarFiltro" class="form-check">
              <!-- Se insertarán dinámicamente -->
            </div>
          </div>          
  
          <div class="form-group">
            <label for="marcaFiltro"><strong>Marca:</strong></label>
            <input type="text" id="marcaFiltro" class="form-control" placeholder="Ej. Toyota">
          </div>
  
          <div class="form-group">
            <label><strong>Año:</strong></label>
            <div id="anioFiltroContainer" class="form-check">
              <!-- Se insertarán dinámicamente -->
            </div>
          </div>          

          <div class="form-group">
            <label for="precioMin"><strong>Precio mín (€):</strong></label>
            <input type="number" id="precioMin" class="form-control" placeholder="Mín">
          </div>
  
          <div class="form-group">
            <label for="precioMax"><strong>Precio máx (€):</strong></label>
            <input type="number" id="precioMax" class="form-control" placeholder="Máx">
          </div>

          <div class="form-group">
            <label><strong>Valoración:</strong></label>
            <div id="valoracionFiltro" class="form-check">
              <label><input class="form-check-input" type="checkbox" name="valoracion" value="5"> 5 ⭐</label><br>
              <label><input class="form-check-input" type="checkbox" name="valoracion" value="4"> 4 ⭐</label><br>
              <label><input class="form-check-input" type="checkbox" name="valoracion" value="3"> 3 ⭐</label><br>
              <label><input class="form-check-input" type="checkbox" name="valoracion" value="2"> 2 ⭐</label><br>
              <label><input class="form-check-input" type="checkbox" name="valoracion" value="1"> 1 ⭐</label><br>
            </div>
          </div>          
  
          <div class="form-group">
            <label for="perPageInput"><strong>Vehículos/página:</strong></label>
            <input id="perPageInput" type="number" class="form-control" value="16" min="1">
          </div>

          <div class="form-group text-center mt-3">
            <button id="resetFiltrosBtn" class="btn btn-outline-danger btn-block">
              <i class="fas fa-undo"></i> Limpiar filtros
            </button>
          </div>          
        </div>
      </div>
  
      <!-- Vehículos -->
      <div class="col-md-9">
        <div class="row" id="vehiculos-container">
          <!-- tarjetas dinámicas -->
        </div>
        <!-- Paginación -->
        <div class="d-flex justify-content-center">
          <div class="btn-group" id="pagination-controls"></div>
        </div>
        <div class="text-center text-muted small mt-2" id="pagination-info"></div>
      </div>
    </div>
  </div>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>