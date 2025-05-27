@extends('layouts.admin')

@section('title', 'Espacio Privado Chofers')

@section('content')
    <!-- Se han movido los estilos CSS a un archivo externo -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chofers/dashboard-responsive.css') }}">

    <!-- Overlay para cerrar menú al hacer clic fuera -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-container">
        <!-- Barra lateral lila -->
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="{{route('chofers.solicitudes')}}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i
                            class="fa-solid fa-car"></i> Solicitudes</a></li>
                <li><a href="{{route('chofers.chat')}}"
                        class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fa-solid fa-comments"></i>
                        Chat</a></li>
                <!-- Cerrar sesión solo visible en móvil -->
                <li class="sidebar-logout-mobile">
                    <a href="{{ route('logout') }}">
                        <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <!-- Botón hamburguesa como primer hijo del header -->
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="admin-title dashboard-title">Espacio Privado de los Chofers de: {{ $sede ?? 'Central' }}</h1>
                <div class="admin-welcome dashboard-welcome">
                    @if(auth()->check())
                        <span class="dashboard-welcome-user">Bienvenido, {{ auth()->user()->nombre }}</span>
                        <a href="{{ route('logout') }}" class="btn btn-outline-danger dashboard-logout-desktop"><i class="fas fa-sign-out-alt"></i></a>
                    @endif
                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4 dashboard-alert">
                    <h3 class="mb-3 dashboard-alert-title"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al espacio privado de los chofers de: {{ $sede ?? 'Central' }}!</h3>
                    <p class="mb-0 dashboard-alert-text">Desde aquí podrás comunicarte con otros compañeros y ver la disponibilidad de clientes cada día. 
                        Selecciona una de las opciones a continuación para acceder a las diferentes
                        funcionalidades.</p>
                </div>

                <div class="row g-4">
                    <!-- Tarjeta de Solicitudes de Transporte Privado -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fa-solid fa-car fa-3x"></i>
                            </div>
                            <h3 class="dashboard-card-title">Solicitudes de Transporte Privado</h3>
                            <p>Consulta las solicitudes de los usuarios para el servicio de transporte privado.</p>
                            <a href="{{ route('chofers.solicitudes') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Tarjeta de Historial -->
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fa-solid fa-comments fa-3x"></i>
                            </div>
                            <h3 class="dashboard-card-title">Chat de Chofers</h3>
                            <p>Comunicate con otros chofers y gestiona los servicios del día a día.</p>
                            <a href="{{route('chofers.chat')}}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection

@endsection