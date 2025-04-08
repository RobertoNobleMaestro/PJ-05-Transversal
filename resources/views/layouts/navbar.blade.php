<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container">
    <img src="{{ asset('img/logo.png') }}" alt="Logo Carflow">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon" style="color:#fff;">&#9776;</span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ml-auto align-items-center">
        @auth
          <!-- Cerrar sesión -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-sign-out-alt me-1"></i>
            </a>
            <form id="logout-form" action="" method="POST" style="display: none;">
              @csrf
            </form>
          </li>
          <!-- Carrito -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-shopping-cart me-1"></i>
            </a>
          </li>
          <!-- Foto de perfil (link al perfil) -->
          <li class="nav-item d-flex align-items-center">
            <a class="nav-link d-flex align-items-center" href="">
              <span class="ms-2">Perfil</span>
              <img src="{{ asset('img/' . Auth::user()->foto_perfil) }}" alt="Foto de perfil" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
            </a>
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