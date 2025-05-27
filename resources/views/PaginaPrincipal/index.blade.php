<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carflow - Alquiler de vehículos</title>

  <!-- Bootstrap & FontAwesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- CSS personalizado -->
  <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">

  <!-- CSRF Token para Ajax -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

  <!-- Navbar -->
  @include('layouts.navbar')

  <!-- Ruta de navegación -->
  <div class="breadcrumb-container">
    <div class="container">
      <small>Inicio &gt; Alquiler coches</small>
    </div>
  </div>

  <!-- Hero Section -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-5">
        <h1>Alquiler de vehículos de todo tipo<br>y con precios asequibles</h1>
        <p>Encuentra el vehículo perfecto para tus necesidades, con una amplia variedad de opciones y precios
          competitivos.</p>
        <ul class="list-unstyled mt-4">
          <li><i class="fas fa-check-circle text-success mr-2"></i>Reserva cuando quieras</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Todo tipo de vehículos</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Seguro incluido</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Sin comisiones</li>
        </ul>
        <div class="btn-group mt-3" role="group" aria-label="Botones de acción">
          <a href="#alquiler" class="btn btn-light font-weight-bold">Encuentra ya tu vehículo a reservar!</a>
          <a href="{{route('chofers.cliente-pide')}}" class="btn btn-light font-weight-bold">Solicita ya tu transporte privado!</a>
        </div>


      </div>
      <div class="col-md-6 text-center">
        <img src="{{ asset('img/coches.png') }}" class="img-fluid" alt="Vehículos">
      </div>
    </div>
  </div>

  <!-- Estadísticas -->
  <div class="container-fluid stats-section">
    <div class="row no-gutters text-center">
      <div class="col-sm-3">
        <div class="stat-box">
          <i class="fas fa-users" style="color: #9F17BD"></i>
          <h3>{{ $usuariosClientes }}</h3>
          <p>Usuarios registrados</p>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="stat-box">
          <i class="fas fa-car" style="color: #9F17BD"></i>
          <h3>{{ number_format($vehiculos, 0, ',', '.') }}</h3>
          <p>Vehículos registrados</p>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="stat-box">
          <i class="fa-solid fa-star" style="color: #ffc800;"></i>
          <h3>{{ $valoracionVehiculos }}</h3>
          <p>Valoración de los vehículos</p>
        </div>
      </div>
      <div class="col-sm-3">
        <div class="stat-box">
          <i class="fa-solid fa-star" style="color: #ffc800;"></i>
          <h3>{{ $valoracionMedia }}</h3>
          <p>Valoración de la web</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Sección Alquiler -->
  <div id="alquiler" class="container vehicles-section">
    <h2>Alquila vehículos</h2>

    <!-- Botón Chat -->
    <button id="chatBtn" class="btn btn-primary" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
      <i class="fas fa-comments"></i>
    </button>

    <!-- Ventana Chat -->
    <div id="chatBox" class="card shadow"
      style="width: 300px; position: fixed; bottom: 80px; right: 20px; display: none; z-index: 9999;">
      <div class="card-header bg-primary text-white p-2">
        Chat IA
        <button type="button" id="closeChat" class="close text-white">&times;</button>
      </div>
      <div class="card-body p-2" style="height: 300px; overflow-y: auto;" id="chatMessages">
        <div class="text-muted small">Hola 👋 ¿En qué puedo ayudarte?</div>
      </div>
      <div class="card-footer p-2">
        <form id="chatForm">
          <div class="input-group">
            <input type="text" id="mensajeInput" name="mensaje" class="form-control"
              placeholder="Escribe tu mensaje...">
            <div class="input-group-append">
              <button class="btn btn-success" type="submit">Enviar</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row">
      <!-- Filtros -->
      <div class="col-md-3">
        <div id="filtros-form" class="bg-white p-3 rounded shadow-sm sticky-top" style="top: 90px;">
          <h4 class="mb-3">Filtros</h4>

          <!-- Tipo -->
          <div class="form-group">
            <label><strong>Tipo de vehículo:</strong></label>
            <div id="tipoVehiculoFiltro" class="form-check"></div>
          </div>

          <!-- Ciudad -->
          <div class="form-group">
            <label><strong>Ciudad:</strong></label>
            <div id="lugarFiltro" class="form-check"></div>
          </div>

          <!-- Marca -->
          <div class="form-group">
            <label><strong>Marca:</strong></label>
            <input type="text" id="marcaFiltro" class="form-control" placeholder="Ej. Toyota">
          </div>

          <!-- Año -->
          <div class="form-group">
            <label><strong>Año:</strong></label>
            <div id="anioFiltroContainer" class="form-check"></div>
          </div>

          <!-- Precio -->
          <div class="form-group">
            <label><strong>Precio mín (€):</strong></label>
            <input type="number" id="precioMin" class="form-control" placeholder="Mín">
          </div>
          <div class="form-group">
            <label><strong>Precio máx (€):</strong></label>
            <input type="number" id="precioMax" class="form-control" placeholder="Máx">
          </div>

          <!-- Valoración -->
          <div class="form-group">
            <label><strong>Valoración:</strong></label>
            <div id="valoracionFiltro" class="form-check">
              <label><input type="checkbox" value="5" class="form-check-input"> 5 ⭐</label><br>
              <label><input type="checkbox" value="4" class="form-check-input"> 4 ⭐</label><br>
              <label><input type="checkbox" value="3" class="form-check-input"> 3 ⭐</label><br>
              <label><input type="checkbox" value="2" class="form-check-input"> 2 ⭐</label><br>
              <label><input type="checkbox" value="1" class="form-check-input"> 1 ⭐</label>
            </div>
          </div>

          <!-- Per Page -->
          <div class="form-group">
            <label><strong>Vehículos/página:</strong></label>
            <input type="number" id="perPageInput" class="form-control" value="16" min="1">
          </div>

          <button id="resetFiltrosBtn" class="btn btn-outline-danger btn-block mt-3">
            <i class="fas fa-undo"></i> Limpiar filtros
          </button>
        </div>
      </div>

      <!-- Vehículos y paginación -->
      <div class="col-md-9">
        <div class="row" id="vehiculos-container">
          <!-- tarjetas dinámicas -->
        </div>
        <div class="d-flex justify-content-center">
          <div class="btn-group" id="pagination-controls"></div>
        </div>
        <div class="text-center text-muted small mt-2" id="pagination-info"></div>
      </div>
    </div>
  </div>

  <!-- Chat Script -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const chatBtn = document.getElementById('chatBtn');
      const chatBox = document.getElementById('chatBox');
      const closeChat = document.getElementById('closeChat');
      const chatForm = document.getElementById('chatForm');
      const mensajeInput = document.getElementById('mensajeInput');
      const chatMessages = document.getElementById('chatMessages');

      chatBtn.onclick = () => chatBox.style.display = 'block';
      closeChat.onclick = () => chatBox.style.display = 'none';

      chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const mensaje = mensajeInput.value.trim();
        if (!mensaje) return;

        const token = document.querySelector('meta[name="csrf-token"]').content;

        const userMsg = document.createElement('div');
        userMsg.innerText = "Tú: " + mensaje;
        userMsg.classList.add("text-primary");
        chatMessages.appendChild(userMsg);
        mensajeInput.value = "";

        fetch("/chat-ia", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token
          },
          body: JSON.stringify({ mensaje })
        })
        .then(res => res.json())
        .then(data => {
          const iaMsg = document.createElement('div');
          iaMsg.innerText = "IA: " + (data.respuesta ?? "Sin respuesta");
          iaMsg.classList.add("text-success");
          chatMessages.appendChild(iaMsg);
        })
        .catch(error => {
          const errorMsg = document.createElement('div');
          errorMsg.innerText = "Error: " + error.message;
          errorMsg.classList.add("text-danger");
          chatMessages.appendChild(errorMsg);
        });
      });
    });
  </script>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/home.js') }}"></script>

</body>

</html>