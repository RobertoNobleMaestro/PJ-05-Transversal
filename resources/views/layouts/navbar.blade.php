<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Navbar</title>
    <style>
            .nav-link {
            position: relative;
        }

        #cart-count {
          position: absolute;
          top: 0px;
          right: 0px;
          font-size: 9px;
          border-radius: 50%;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container">
        <a href="{{ route('home') }}">
          <img src="{{ asset('img/logo.png') }}" alt="Logo Carflow">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon" style="color:#fff;">&#9776;</span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
          <ul class="navbar-nav ml-auto align-items-center">
            @auth
              <!-- Cerrar sesión -->
              <li class="nav-item">
                <a class="nav-link" href="{{ route('logout') }}">
                  <i class="fas fa-sign-out-alt"></i>
                </a>
              </li>
              <!-- Carrito -->
              <li class="nav-item">
                <a class="nav-link" href="{{ route('carrito') }}">
                  <i class="fas fa-shopping-cart me-1"></i>
                  <span id="cart-count" class="badge bg-danger"></span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('chat.index') }}">
                    <i class="fas fa-comments"></i>
                </a>
              </li>
              <!-- Notificaciones de solicitudes de transporte -->
              <li class="nav-item">
                <a class="nav-link" href="#" onclick="event.preventDefault(); mostrarModalNotificacion();">
                  <i class="fas fa-bell"></i>
                  <span id="notification-count" class="badge bg-danger"></span>
                </a>
              </li>
              <!-- Foto de perfil (link al perfil) -->
              <li class="nav-item">
                <a class="nav-link" href="{{ url('/perfil/' . Auth::user()->id_usuario) }}">
                  <img id="navbar-profile-img" src="{{ asset(Auth::user()->foto_perfil ? 'img/' . Auth::user()->foto_perfil : 'img/default.png') }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;" alt="Perfil">
                </a>
              </li>
            @endauth
            @guest
              <li class="nav-item">
                <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">Registrarme</a>
              </li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>

    <!-- Modal de Notificación de Aceptación -->
    <div class="modal fade" id="modalNotificacionAceptacion" tabindex="-1" aria-labelledby="modalNotificacionAceptacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="margin-top: 60px;">
            <div class="modal-content" style="background: linear-gradient(135deg, #8c37c1, #6f42c1); color: white;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalNotificacionAceptacionLabel">
                        <i class="fas fa-bell me-2"></i>Notificaciones
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div id="contenido-notificaciones">
                        <!-- El contenido se cargará dinámicamente -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function actualizarContadores() {
            // Cargar contador del carrito
            fetch('{{ route("carrito.count") }}')
                .then(response => response.json())
                .then(data => {
                    const cartCount = document.getElementById('cart-count');
                    if (data.count > 0) {
                        cartCount.textContent = data.count;
                        cartCount.style.display = 'inline-block';
                    } else {
                        cartCount.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching cart count:', error));

            // Cargar contador de notificaciones
            fetch('{{ route("notificaciones.count") }}')
                .then(response => response.json())
                .then(data => {
                    const notificationCount = document.getElementById('notification-count');
                    if (data.count > 0) {
                        notificationCount.textContent = data.count;
                        notificationCount.style.display = 'inline-block';
                    } else {
                        notificationCount.style.display = 'none';
                    }
                })
                .catch(error => console.error('Error fetching notification count:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Cargar contadores inicialmente
            actualizarContadores();

            // Actualizar contadores cada 30 segundos
            setInterval(actualizarContadores, 30000);

            // Escuchar el evento de solicitud aceptada
            window.Echo.private('solicitud.{{ Auth::id() }}')
                .listen('SolicitudAceptada', (e) => {
                    actualizarContadores();
                });
        });
    </script>

    <!-- Scripts -->
    <script src="{{ asset('js/notificaciones.js') }}"></script>
    <script>
        // Inicializar Echo para las notificaciones en tiempo real
        const Echo = {
            private: function(channel) {
                return {
                    listen: function(event, callback) {
                        // Implementación básica para evitar errores
                        console.log('Escuchando canal:', channel, 'evento:', event);
                    }
                };
            }
        };
        window.Echo = Echo;
    </script>
</body>
</html>