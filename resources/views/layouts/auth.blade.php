<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- link bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- link fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">  
    <!-- estilos del login-->
    <link rel="stylesheet" href="{{asset('css/PaginaPrincipal/auth/auth.css')}}">
    <title>@yield('title')</title>
    <style>
        .navbar {
            background-color: #6f42c1 !important;
            padding: 15px;
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
        }
        .nav-link:hover {
            color: white !important;
        }
        .dropdown-menu {
            background-color: #f8f9fa;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .btn-outline-light {
            border-color: white;
            color: white;
            margin-right: 10px;
        }
        .btn-outline-light:hover {
            background-color: white;
            color: #6f42c1;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand w-100" href="{{ url('/home') }}">Administración Financiera</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/home') }}">Inicio</a>
                    </li>
                    <!-- Aquí pueden ir más elementos del menú -->
                </ul>
                
                <div class="d-flex align-items-center">
                    @if(Auth::check())
                        <div class="dropdown">
                            <a class="btn btn-link dropdown-toggle text-white text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->foto_perfil)
                                    <img src="{{ asset('storage/'.Auth::user()->foto_perfil) }}" alt="Avatar" class="user-avatar">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                {{ Auth::user()->nombre }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                @if(Auth::user()->id_roles == 1)
                                    <li><a class="dropdown-item" href="{{ url('/admin') }}"><i class="fas fa-user-shield me-2"></i>Panel de Admin</a></li>
                                @endif
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Mi Perfil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-light me-2">Iniciar Sesión</a>
                        <a href="#" class="btn btn-outline-light">Registrarse</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>
    @yield('content')
    <!-- script de bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- link sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @yield('scripts')
</body>
</html>