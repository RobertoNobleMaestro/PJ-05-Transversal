<nav class="navbar navbar-expand-lg navbar-custom">
  <div class="container">
    <img src="{{ asset('img/logo.png') }}">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon" style="color:#fff;">&#9776;</span>
    </button>
    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav ml-auto">
        @auth
          <li class="nav-item">
              <a class="nav-link" href="">Cerrar Sesión</a>
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