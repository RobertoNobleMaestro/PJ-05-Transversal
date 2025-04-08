<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container">
    <img src="{{ asset('img/logo.png') }}" alt="Logo Carflow">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent"
      aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon" style="color:#fff;">&#9776;</span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ml-auto align-items-center">
        @auth
          <!-- Botón perfil -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-user me-1"></i> Perfil
            </a>
          </li>

          <!-- Carrito -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-shopping-cart me-1"></i> Carrito
            </a>
          </li>

          <!-- Cerrar sesión -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
            </a>
            <form id="logout-form" action="" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
        @endauth

        @guest
          <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">Iniciar Sesión</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="">Registrarme</a>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>