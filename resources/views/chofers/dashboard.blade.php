@extends('layouts.admin')

@section('title', 'Espacio Privado Chofers')

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
                <li><a href="" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i
                            class="fa-solid fa-car"></i> Solicitudes</a></li>
                <li><a href="{{route('chofers.chat')}}"
                        class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fa-solid fa-comments"></i>
                        Chat</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Espacio Privado de los Chofers de: {{ $sede ?? 'Central' }}</h1>
                <div class="admin-welcome">
                    @if(auth()->check())
                        <span>Bienvenido, {{ auth()->user()->nombre }}</span>
                        <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i>
                            </a>
                    @endif
                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4">
                    <h3 class="mb-3"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al espacio privado de los chofers de: {{ $sede ?? 'Central' }}!</h3>
                    <p class="mb-0">Desde aquí podrás comunicarte con otros compañeros y ver la disponibilidad de clientes cada día. 
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
                            <h3>Solicitudes de Transporte Privado</h3>
                            <p>Consulta las solicitudes de los usuarios para el servicio de transporte privado.</p>
                            <a href="#" class="btn-admin-card">
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
                            <h3>Chat de Chofers</h3>
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