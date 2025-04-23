@extends('layouts.admin')

@section('title', 'Panel de Administrador')

@section('content')
<!-- Se han movido los estilos CSS a un archivo externo -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

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