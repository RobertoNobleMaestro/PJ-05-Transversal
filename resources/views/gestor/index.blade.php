@extends('layouts.admin')

@section('title', 'Panel de gestor')

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
                <li><a href="{{ route('gestor.vehiculos') }}"
                        class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i>
                        Vehículos</a></li>
                <li><a href="{{ route('admin.lugares') }}"
                        class="{{ request()->routeIs('admin.lugares*') ? 'active' : '' }}"><i
                            class="fas fa-map-marker-alt"></i> Lugares</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                @if(auth()->check())
                    <span>Bienvenido, {{ auth()->user()->nombre }}</span>
                @endif
                <div class="admin-welcome">
                    <a href="{{ route('home') }}" class="btn btn-outline-primary"><i class="fa-solid fa-house"></i>
                        Ver Página Principal</a>
                    <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i>
                        Cerrar Sesión</a>

                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4">
                    <h3 class="mb-3"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al panel de administración!</h3>
                    <p class="mb-0">Desde aquí podrás gestionar algunos aspectos de la plataforma de alquiler de vehículos.
                        Selecciona una de las opciones a continuación para acceder a las diferentes funcionalidades.</p>
                </div>
                <div class="row g-4">


                    <!-- Tarjeta de Vehículos -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-car fa-3x"></i>
                            </div>
                            <h3>Gestión de Vehículos</h3>
                            <p>Administra el inventario de vehículos, precios y disponibilidad.</p>
                            <a href="{{ route('gestor.vehiculos') }}" class="btn-admin-card">
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
                            <a href="" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para funcionalidad responsive -->
    @section('scripts')
        <script src="{{asset('js/crud_gestor.js')}}"></script>
    @endsection

@endsection