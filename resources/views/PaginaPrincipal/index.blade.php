<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Carflow - Alquiler de vehículos</title>

  <!-- Bootstrap & FontAwesome -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- CSS personalizado -->
  <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">
  
  <!-- CSRF Token para Ajax -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

  @include('layouts.navbar')

  <!-- Breadcrumb -->
  <div class="breadcrumb-container">
    <div class="container">
      <small>Inicio &gt; Alquiler coches</small>
    </div>
  </div>

  <!-- Hero -->
  <div class="container hero-section">
    <div class="row">
      <div class="col-md-5">
        <h1>Alquiler de vehículos de todo tipo<br>y con precios asequibles</h1>
        <p>Encuentra el vehículo perfecto para tus necesidades, con una amplia variedad de opciones y precios competitivos.</p>
        <ul class="list-unstyled mt-4">
          <li><i class="fas fa-check-circle text-success mr-2"></i>Reserva cuando quieras</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Todo tipo de vehículos</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Seguro incluido</li>
          <li><i class="fas fa-check-circle text-success mr-2"></i>Sin comisiones</li>
        </ul>
        <a href="#alquiler" class="btn btn-light font-weight-bold">Encuentra ya tu vehículo a reservar!</a>
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
    <button id="chatBtn" class="btn btn-primary rounded-circle shadow" style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; z-index: 9999;">
      <i class="fas fa-comments fa-lg"></i>
    </button>

    <!-- Ventana Chat -->
    <div id="chatBox" class="card shadow-lg" style="width: 350px; position: fixed; bottom: 100px; right: 30px; display: none; z-index: 9999;">
      <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0"><i class="fas fa-robot mr-2"></i>Asistente Virtual</h6>
        <button id="closeChat" class="btn btn-sm btn-light p-0 rounded-circle" style="width: 24px; height: 24px;">
          &times;
        </button>
      </div>
      <div class="card-body p-3" style="height: 400px; overflow-y: auto;" id="chatMessages">
        <div class="alert alert-info p-2 mb-3 small">
          <strong><i class="fas fa-robot"></i> Asistente:</strong> ¡Hola! Soy tu asistente virtual de Carflow. ¿En qué puedo ayudarte hoy con tu alquiler de vehículos?
        </div>
      </div>
      <div class="card-footer p-3">
        <form id="chatForm" class="form-inline">
          <div class="input-group w-100">
            <input type="text" id="mensajeInput" class="form-control" placeholder="Escribe tu mensaje..." autocomplete="off" autofocus>
            <div class="input-group-append">
              <button class="btn btn-success" type="submit">
                <i class="fas fa-paper-plane"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <div class="row">
      <!-- Contenido de la sección de alquiler -->
    </div>
  </div>

  <!-- Script del Chat -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Elementos del chat
      const chatBtn = document.getElementById('chatBtn');
      const chatBox = document.getElementById('chatBox');
      const closeChat = document.getElementById('closeChat');
      const chatForm = document.getElementById('chatForm');
      const mensajeInput = document.getElementById('mensajeInput');
      const chatMessages = document.getElementById('chatMessages');

      // Mostrar/ocultar chat
      chatBtn.addEventListener('click', function() {
        chatBox.style.display = chatBox.style.display === 'none' ? 'block' : 'none';
        if (chatBox.style.display === 'block') {
          mensajeInput.focus();
        }
      });

      closeChat.addEventListener('click', function() {
        chatBox.style.display = 'none';
      });

      // Enviar mensaje
      chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const message = mensajeInput.value.trim();
        if (!message) return;

        // Mostrar mensaje del usuario
        addMessage('Tú', message, 'user');
        mensajeInput.value = '';
        
        try {
          const response = await fetch("{{ route('chat.send') }}", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
              "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
              "Accept": "application/json"
            },
            body: JSON.stringify({ message: message })
          });

          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.error || 'Error en la respuesta del servidor');
          }

          addMessage('Asistente', data.reply, 'bot');
        } catch (error) {
          addMessage('Error', error.message, 'error');
        }
      });

      // Función para añadir mensajes
      function addMessage(sender, text, type) {
        const messageDiv = document.createElement('div');
        const icon = type === 'user' ? 'user' : (type === 'error' ? 'exclamation-triangle' : 'robot');
        
        messageDiv.className = `alert alert-${type === 'user' ? 'primary' : (type === 'error' ? 'danger' : 'info')} p-3 mb-3 small`;
        messageDiv.innerHTML = `
          <strong><i class="fas fa-${icon} mr-1"></i> ${sender}:</strong> ${text}
        `;
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
    });
  </script>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/home.js') }}"></script>

</body>
</html>