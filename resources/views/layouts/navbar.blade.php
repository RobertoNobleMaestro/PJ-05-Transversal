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
            <a class="nav-link" href="{{ route('logout') }}">
              <i class="fas fa-sign-out-alt"></i>
            </a>
          </li>
          <!-- Carrito -->
          <li class="nav-item">
            <a class="nav-link" href="">
              <i class="fas fa-shopping-cart me-1"></i>
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
            <a class="nav-link" href="">Registrarme</a>
          </li>
        @endguest
      </ul>
    </div>
  </div>
</nav>