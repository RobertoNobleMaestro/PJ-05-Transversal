@extends('layouts.admin')

@section('title', 'Panel de Administrador')

@section('content')
<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-color: #9F17BD;
        --header-height: 60px;
    }
    
    .admin-container {
        display: flex;
        min-height: 100vh;
        background-color: #f8f9fa;
    }
    
    /* Barra lateral lila */
    .admin-sidebar {
        width: var(--sidebar-width);
        background-color: var(--sidebar-color);
        color: white;
        padding: 1.5rem 1rem;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    /* ... (el resto del CSS permanece igual) ... */

        
        .sidebar-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid rgba(255,255,255,0.2);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
        }
        
        .sidebar-menu li {
            margin-bottom: 1rem;
        }
        
        .sidebar-menu a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .sidebar-menu a:hover {
            background-color: rgba(255,255,255,0.2);
        }
        
        .sidebar-menu i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
        
        /* Contenido principal */
        .admin-main {
            flex: 1;
            padding: 0.5rem;
            margin-left: 0;
        }
        
        /* Header modificado */
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background-color: white; /* Fondo blanco */
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .admin-title {
            font-size: 1.5rem;
            color: #2d3748;
            font-weight: 600;
        }
        
        .admin-welcome {
            display: flex;
            align-items: center;
            gap: 1rem;
            color: #4a5568;
            font-weight: 500;
        }
        
        /* Filtros */
        .filter-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            background-color: white;
            padding: 0.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .filter-control {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
        }
        
        .search-input {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 5px;
            min-width: 250px;
        }
        
        .add-user-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 0.5rem 1.5rem;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .add-user-btn:hover {
            background-color: #333;
        }
        
        /* Tabla */
        .crud-table {
            width: 100%;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
            border-collapse: collapse;
        }
        
        .crud-table thead {
            background-color: #4a5568;
            color: white;
        }
        
        .crud-table th,
        .crud-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .crud-table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.875rem;
        }
        
        .crud-table tbody tr:nth-child(even) {
            background-color: #f7fafc;
        }
        
        .crud-table tbody tr:hover {
            background-color: #ebf4ff;
        }
        
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-edit {
            color: #2b6cb0;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .btn-delete {
            color: #c53030;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            text-decoration: none;
            transition: background 0.2s;
        }
        
        .btn-edit:hover {
            background-color: #bee3f8;
        }
        
        .btn-delete:hover {
            background-color: #fed7d7;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-photo {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        /* Estilos para las tarjetas del panel de administración */
        .admin-card {
            background-color: white;
            border-radius: 12px;
            padding: 2rem 1.5rem;
            height: 100%;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .admin-card-icon {
            color: var(--sidebar-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background-color: rgba(159, 23, 189, 0.1);
            margin: 0 auto 1.5rem;
        }
        
        .admin-card h3 {
            font-size: 1.25rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 600;
            text-align: center;
        }
        
        .admin-card p {
            color: #666;
            margin-bottom: 1.5rem;
            flex-grow: 1;
            text-align: center;
        }
        
        .btn-admin-card {
            background-color: var(--sidebar-color);
            color: white;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-admin-card:hover {
            background-color: #8714a1;
            color: white;
        }
        
        .admin-welcome-message {
            border-left: 5px solid var(--sidebar-color);
        }
        
        /* Botón del menú hamburguesa */
        .menu-toggle {
            display: none;
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background-color: var(--sidebar-color);
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }

        /* Media queries para responsive design */
        @media (max-width: 1199px) {
            .crud-table th,
            .crud-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.9rem;
            }
            
            .search-input {
                min-width: 180px;
            }
        }
        
        @media (max-width: 991px) {
            .admin-sidebar {
                position: fixed;
                left: -250px; /* Ocultar fuera de pantalla */
                top: 0;
                height: 100%;
                transition: left 0.3s ease;
                z-index: 999;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .admin-main {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar-visible {
                left: 0; /* Mostrar menú cuando está visible */
            }
            
            /* Overlay para cerrar menú al hacer clic */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 998;
            }
            
            .sidebar-overlay.active {
                display: block;
            }
            
            /* Ajustes para la tabla */
            .crud-table {
                overflow-x: auto;
                display: block;
                width: 100%;
            }
            
            /* Ajustes de tabla en tablets */
            .crud-table-container {
                width: 100%;
                overflow-x: auto;
            }
            
            .crud-table {
                min-width: 900px; /* Asegurar que la tabla tenga un ancho mínimo */
            }
            
            /* Ancho de las columnas */
            .crud-table th:nth-child(1) { width: 5%; } /* ID */
            .crud-table th:nth-child(2) { width: 15%; } /* Nombre */
            .crud-table th:nth-child(6) { width: 10%; } /* Rol */
            .crud-table th:nth-child(9) { width: 15%; } /* Acciones */
        }
        
        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
                width: 100%;
            }
            
            .search-input {
                width: 100%;
                min-width: unset;
            }
            
            .add-user-btn {
                width: 100%;
                text-align: center;
            }
            
            /* Ajustes para la tabla en móviles */
            .crud-table th, 
            .crud-table td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
                white-space: nowrap;
            }
            
            /* Ocultar más columnas en móviles */
            .crud-table .col-hide-sm {
                display: none;
            }
            
            /* Ajustes para móviles */
            .crud-table {
                min-width: 600px; /* Ancho mínimo más pequeño para móviles */
            }
            
            .action-cell {
                min-width: 120px;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .btn-edit, .btn-delete {
                font-size: 0.8rem;
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            /* Para móviles muy pequeños */
            .crud-table {
                font-size: 0.75rem;
            }
            
            .crud-table th, 
            .crud-table td {
                padding: 0.4rem 0.2rem;
            }
            
            .action-buttons form {
                width: 100%;
            }
            
            .action-buttons button {
                width: 100%;
            }
            
            .btn-edit {
                display: block;
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <!-- Botón de hamburguesa para menú móvil -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay para cerrar menú al hacer clic fuera -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="admin-container">
        <!-- Barra lateral lila -->
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Usuarios</a></li>
                <li><a href="{{ route('admin.vehiculos') }}" class="{{ request()->routeIs('admin.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i> Vehículos</a></li>
                <li><a href="{{ route('admin.lugares') }}" class="{{ request()->routeIs('admin.lugares*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Lugares</a></li>
                <li><a href="{{ route('admin.reservas') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
                <li><a href="{{ route('admin.historial') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Panel de Administración</h1>
                <div class="admin-welcome">
                    @if(auth()->check())
                    <span>Bienvenido, {{ auth()->user()->nombre }}</span>
                    <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
                    @endif
                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4">
                    <h3 class="mb-3"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al panel de administración!</h3>
                    <p class="mb-0">Desde aquí podrás gestionar todos los aspectos de la plataforma de alquiler de vehículos. Selecciona una de las opciones a continuación para acceder a las diferentes funcionalidades.</p>
                </div>
                
                <div class="row g-4">
                    <!-- Tarjeta de Usuarios -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-users fa-3x"></i>
                            </div>
                            <h3>Gestión de Usuarios</h3>
                            <p>Administra los usuarios del sistema, sus roles y permisos.</p>
                            <a href="{{ route('admin.users') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tarjeta de Vehículos -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-car fa-3x"></i>
                            </div>
                            <h3>Gestión de Vehículos</h3>
                            <p>Administra el inventario de vehículos, precios y disponibilidad.</p>
                            <a href="{{ route('admin.vehiculos') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tarjeta de Lugares -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-map-marker-alt fa-3x"></i>
                            </div>
                            <h3>Gestión de Lugares</h3>
                            <p>Administra las ubicaciones de recogida y entrega de vehículos.</p>
                            <a href="{{ route('admin.lugares') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tarjeta de Reservas -->
                    <div class="col-md-6 col-lg-6">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-calendar-alt fa-3x"></i>
                            </div>
                            <h3>Gestión de Reservas</h3>
                            <p>Administra las reservas actuales, crea nuevas reservas o modifica las existentes.</p>
                            <a href="{{ route('admin.reservas') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tarjeta de Historial -->
                    <div class="col-md-6 col-lg-6">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-history fa-3x"></i>
                            </div>
                            <h3>Historial de Reservas</h3>
                            <p>Consulta el historial completo de todas las reservas realizadas en el sistema.</p>
                            <a href="{{ route('admin.historial') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidad responsive -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Menú responsive
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            if (menuToggle && sidebar && overlay) {
                menuToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('sidebar-visible');
                    overlay.classList.toggle('active');
                });
                
                overlay.addEventListener('click', function() {
                    sidebar.classList.remove('sidebar-visible');
                    overlay.classList.remove('active');
                });
            }
            
            // Filtro por rol
            document.querySelector('.filter-control').addEventListener('change', function() {
                const role = this.value.toLowerCase();
                const rows = document.querySelectorAll('.crud-table tbody tr');
                
                rows.forEach(row => {
                    const roleCell = row.querySelector('td:nth-child(6)');
                    if (roleCell) {
                        const rowRole = roleCell.textContent.toLowerCase();
                        if(role === '' || rowRole.includes(role)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
            
            // Buscador
            document.querySelector('.search-input').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = document.querySelectorAll('.crud-table tbody tr');
                
                rows.forEach(row => {
                    const nameCell = row.querySelector('td:nth-child(2)');
                    const emailCell = row.querySelector('td:nth-child(5)');
                    
                    if (nameCell && emailCell) {
                        const name = nameCell.textContent.toLowerCase();
                        const email = emailCell.textContent.toLowerCase();
                        
                        if(name.includes(searchTerm) || email.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
            
            // Mejorar la visibilidad de las celdas al pasar el ratón
            const rows = document.querySelectorAll('.crud-table tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f0f7ff';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
@endsection