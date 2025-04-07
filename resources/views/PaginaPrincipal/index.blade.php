<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">
  <title>Carflow - Alquiler de vehículos</title>
</head>
<body>

  <!-- Navbar -->
  @include('PaginaPrincipal.navbar')

  <!-- Breadcrumb / Ruta de navegación -->
  <div class="breadcrumb-container">
    <div class="container">
      <small>Inicio &gt; Alquiler coches</small>
    </div>
  </div>

  <!-- Sección principal (Hero) -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-6">
        <h1>Alquiler de vehículos de todo tipo<br>y con precios asequibles</h1>
        <!-- Formulario de filtros (ejemplo) -->
        <form class="filter-form">
          <div class="form-group">
            <label>Tipo de vehículo:</label><br>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipoVehiculo" id="coches" value="coches" checked>
              <label class="form-check-label" for="coches">Coches</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipoVehiculo" id="motos" value="motos">
              <label class="form-check-label" for="motos">Motos</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="tipoVehiculo" id="furgonetas" value="furgonetas">
              <label class="form-check-label" for="furgonetas">Furgonetas</label>
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="ubicacion">Ubicación:</label>
              <input type="text" class="form-control" id="ubicacion" value="Madrid, España">
            </div>
            <div class="form-group col-md-6">
              <label for="fecha">Fecha:</label>
              <input type="date" class="form-control" id="fecha">
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary">Buscar</button>
        </form>
      </div>
      <div class="col-md-6 text-center">
        <!-- Ejemplo de imágenes a la derecha -->
        <img src="https://via.placeholder.com/350x200/4f2b90/FFFFFF?text=Auto+1" class="img-fluid mb-2" alt="Auto 1">
        <img src="https://via.placeholder.com/350x200/4f2b90/FFFFFF?text=Auto+2" class="img-fluid" alt="Auto 2">
      </div>
    </div>
  </div>

  <!-- Sección de estadísticas -->
  <div class="container stats-section">
    <div class="row text-center">
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <h3>8</h3>
          <p>Usuarios registrados</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <h3>100.000</h3>
          <p>Vehículos registrados</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <h3>4.8</h3>
          <p>Valoración de la web</p>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-box">
          <h3>★ ★ ★ ★ ☆</h3>
          <p>Opiniones</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección de Top 10 vehículos -->
  <div class="container vehicles-section">
    <h2>Top 10 Vehículos más solicitados</h2>
    <div class="row">
      <!-- Card 1 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+1" class="card-img-top" alt="Coche 1">
          <div class="card-body">
            <h5 class="card-title">Coche 1</h5>
            <p class="card-text">80.000 km | 20.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 2 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+2" class="card-img-top" alt="Coche 2">
          <div class="card-body">
            <h5 class="card-title">Coche 2</h5>
            <p class="card-text">40.000 km | 32.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 3 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+3" class="card-img-top" alt="Coche 3">
          <div class="card-body">
            <h5 class="card-title">Coche 3</h5>
            <p class="card-text">10.000 km | 25.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 4 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+4" class="card-img-top" alt="Coche 4">
          <div class="card-body">
            <h5 class="card-title">Coche 4</h5>
            <p class="card-text">80.000 km | 18.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 5 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+5" class="card-img-top" alt="Coche 5">
          <div class="card-body">
            <h5 class="card-title">Coche 5</h5>
            <p class="card-text">90.000 km | 24.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 6 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+6" class="card-img-top" alt="Coche 6">
          <div class="card-body">
            <h5 class="card-title">Coche 6</h5>
            <p class="card-text">40.000 km | 20.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 7 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+7" class="card-img-top" alt="Coche 7">
          <div class="card-body">
            <h5 class="card-title">Coche 7</h5>
            <p class="card-text">70.000 km | 28.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 8 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+8" class="card-img-top" alt="Coche 8">
          <div class="card-body">
            <h5 class="card-title">Coche 8</h5>
            <p class="card-text">50.000 km | 22.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 9 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+9" class="card-img-top" alt="Coche 9">
          <div class="card-body">
            <h5 class="card-title">Coche 9</h5>
            <p class="card-text">30.000 km | 26.000 €</p>
          </div>
        </div>
      </div>
      <!-- Card 10 -->
      <div class="col-sm-6 col-md-3 mb-4">
        <div class="card">
          <img src="https://via.placeholder.com/300x180?text=Coche+10" class="card-img-top" alt="Coche 10">
          <div class="card-body">
            <h5 class="card-title">Coche 10</h5>
            <p class="card-text">20.000 km | 19.000 €</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <div class="container text-center">
      <p class="m-0">Carflow &copy; 2025</p>
    </div>
  </footer>

  <!-- Scripts de Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>