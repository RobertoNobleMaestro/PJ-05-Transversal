<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Carflow - Administración Financiera</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <style>
        :root {
            --primary-color: #9F17BD;
            --secondary-color: #7952b3;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
        }
        
        body {
            min-height: 100vh;
            background-color: #f5f5f5;
        }
        
        /* Barra de navegación */
        .navbar {
            background-color: var(--primary-color);
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .navbar-brand:hover {
            color: rgba(255,255,255,0.9);
        }
        
        .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 0.5rem 1rem;
            transition: all 0.3s;
        }
        
        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        
        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.5);
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
        }
        
        /* Contenido principal */
        .main-content {
            padding: 2rem 0;
        }
        
        /* Tarjetas */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 500;
            border-bottom: 0;
        }
        
        /* Botones personalizados */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Tablas */
        .table thead th {
            background-color: rgba(159, 23, 189, 0.1);
            border-bottom: 2px solid var(--primary-color);
            color: var(--dark-color);
            font-weight: 600;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(159, 23, 189, 0.05);
        }
        
        /* Formularios */
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(159, 23, 189, 0.25);
        }
        
        /* Filtros */
        .filter-section {
            background-color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
        }
        
        /* Stats cards */
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
            height: 100%;
        }
        
        .stat-card-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .stat-card-success {
            background-color: #28a745;
            color: white;
        }
        
        .stat-card-info {
            background-color: #17a2b8;
            color: white;
        }
        
        .stat-value {
            font-size: 2rem;
            font-weight: bold;
        }
        
        /* Footer */
        footer {
            background-color: var(--light-color);
            padding: 1rem 0;
            margin-top: 3rem;
            border-top: 1px solid #ddd;
        }
        
        /* Extras */
        .action-buttons {
            display: flex;
            gap: 5px;
        }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/asalariados') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Carflow" height="30">
                Administración Financiera
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('asalariados*') ? 'active' : '' }}" href="{{ route('asalariados.index') }}">
                            <i class="fas fa-users me-1"></i> Asalariados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('admin-financiero/resumen*') ? 'active' : '' }}" href="{{ route('admin.financiero.resumen') }}">
                            <i class="fas fa-chart-bar me-1"></i> Resumen
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    @if(Auth::check())
                        <div class="dropdown">
                            <a class="btn btn-link dropdown-toggle text-white text-decoration-none" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(Auth::user()->foto_perfil)
                                    <img src="{{ asset('storage/'.Auth::user()->foto_perfil) }}" alt="Avatar" class="user-avatar me-1">
                                @else
                                    <i class="fas fa-user-circle me-1"></i>
                                @endif
                                {{ Auth::user()->nombre }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="{{ url('/home') }}"><i class="fas fa-home me-2"></i>Página principal</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item text-danger" href="{{ route('logout') }}">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container main-content">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="text-center py-3">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Carflow - Administración Financiera</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @yield('scripts')
</body>
</html>
