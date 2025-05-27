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
                <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i
                            class="fas fa-users"></i> Usuarios</a></li>
                <li><a href="{{ route('admin.historial') }}"
                        class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i>
                        Historial</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Panel de Administración</h1>
                <div class="admin-welcome">
                    @if(auth()->check())
                        <span>Bienvenido, {{ auth()->user()->nombre }}</span>
                        <a href="{{ route('logout') }}" class="btn btn-outline-danger" title="Cerrar sesión">
                            <i class="fas fa-sign-out-alt"></i>
                        </a>
                        @if(auth()->user()->id_roles == 1)
                            <a href="{{ route('gestor.index') }}" class="btn btn-purple ms-2" title="Ir al panel de gestor">
                                <i class="fas fa-user-cog"></i> Panel Gestor
                            </a>
                        @endif
                    @endif
                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4">
                    <h3 class="mb-3"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al panel de administración!</h3>
                    <p class="mb-0">Desde aquí podrás gestionar todos los aspectos de la plataforma de alquiler de
                        vehículos. Selecciona una de las opciones a continuación para acceder a las diferentes
                        funcionalidades.</p>
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

                    <!-- Tarjeta de Historial -->
                    <div class="col-md-6 col-lg-4">
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

    @section('scripts')
        <script src="{{asset('js/index-admin.js')}}"></script>
    @endsection

@endsection