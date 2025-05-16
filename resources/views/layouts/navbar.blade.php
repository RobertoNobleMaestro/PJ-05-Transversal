<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Navbar</title>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom">
      <div class="container">
        <a href="{{ route('home') }}">
          <img src="{{ asset('img/logo.png') }}" alt="Logo Carflow">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
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
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ url('chatify') }}">
                    <i class="fas fa-comments"></i>
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
</body>
</html>