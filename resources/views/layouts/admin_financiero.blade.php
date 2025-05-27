<?php
// Error reporting desactivado - problemas de count() ya resueltos
// ini_set('display_errors', 0);
// ini_set('display_startup_errors', 0);
// error_reporting(0);
?>
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #9F17BD;
            --secondary-color: #7952b3;
            --accent-color: #6610f2;
            --light-purple: #e6d9f2;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --sidebar-width: 250px;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        body {
            min-height: 100vh;
            background-color: #f8f9fc;
            font-family: 'Poppins', sans-serif;
            color: #444;
            line-height: 1.6;
        }
        
        /* Barra de navegación */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            padding: 0.8rem 1rem;
            box-shadow: 0 2px 15px rgba(0,0,0,0.2);
            position: sticky;
            top: 0;
            z-index: 1000;
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
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
            border-top: 3px solid transparent;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(159, 23, 189, 0.15);
            border-top: 3px solid var(--primary-color);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            font-weight: 600;
            border-bottom: 0;
            padding: 1rem 1.25rem;
            letter-spacing: 0.5px;
        }
        
        /* Botones personalizados */
        .btn {
            font-weight: 500;
            padding: 0.5rem 1.25rem;
            border-radius: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-transform: none;
            letter-spacing: 0.3px;
        }
        
        .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            background-color: transparent;
            font-weight: 500;
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 10px rgba(159, 23, 189, 0.2);
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: linear-gradient(45deg, var(--info-color), #36b9cc);
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(45deg, var(--success-color), #2dce89);
            border: none;
        }
        
        /* Tablas */
        .table {
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 1.5rem;
            width: 100%;
            max-width: 100%;
            background-color: transparent;
        }
        
        .table thead th {
            background-color: rgba(159, 23, 189, 0.05);
            border-bottom: 2px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 1rem;
            vertical-align: middle;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(159, 23, 189, 0.05);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .table tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
            border-top: 1px solid #e9ecef;
            color: #555;
        }
        
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.01);
        }
        
        /* Formularios */
        .form-control {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(159, 23, 189, 0.15);
            background-color: #fff;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #444;
        }
        
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(159, 23, 189, 0.15);
        }
        
        /* Filtros */
        .filter-section {
            background-color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .filter-label {
            font-weight: 500;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Stats cards */
        .stat-card {
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .stat-card:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 180px;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            clip-path: polygon(100% 0, 0 0, 100% 100%);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.12);
        }
        
        .stat-card-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }
        
        .stat-card-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .stat-card-info {
            background: linear-gradient(135deg, #17a2b8, #0dcaf0);
        }
        
        .stat-card-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
            color: #212529;
        }
        
        .stat-card-danger {
            background: linear-gradient(135deg, #dc3545, #e74a3b);
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
                Administración Financiera
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.asalariados.index') ? 'active' : '' }}" href="{{ route('admin.asalariados.index') }}">
                            <i class="fas fa-users me-1"></i> Asalariados
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.financiero.balance.activos') ? 'active' : '' }}" href="{{ route('admin.financiero.balance.activos') }}">
                            <i class="fas fa-coins me-1"></i> Balance de Activos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.financiero.balance.pasivos') ? 'active' : '' }}" href="{{ route('admin.financiero.balance.pasivos') }}">
                            <i class="fas fa-file-invoice-dollar me-1"></i> Balance de Pasivos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.financiero.gastos') ? 'active' : '' }}" href="{{ route('admin.financiero.gastos') }}">
                            <i class="fas fa-money-bill-wave me-1"></i> Gastos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.financiero.ingresos') ? 'active' : '' }}" href="{{ route('admin.financiero.ingresos') }}">
                            <i class="fas fa-chart-line me-1"></i> Ingresos
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
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @yield('scripts')
</body>
</html>
