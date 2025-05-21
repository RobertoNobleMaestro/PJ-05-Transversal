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
    <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('gestor.vehiculos') }}"
                        class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i>
                        Vehículos</a></li>
                <li><a href="{{ route('gestor.chat.listar') }}"
                class="{{ request()->routeIs('gestor.chat.listar*') ? 'active' : '' }}"><i
                    class="fas fa-comments"></i> Chats</a></li>
                    <li><a href="{{ route('gestor.historial') }}"
                class="{{ request()->routeIs('gestor.historial') ? 'active' : '' }}"><i
                    class="fas fa-history"></i>Historial</a></li>
                                        <li><a href="{{ route('gestor.parking.index') }}"
                class="{{ request()->routeIs('gestor.parking.index') ? 'active' : '' }}"><i
                    class="fas fa-parking"></i>Parking</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                @if(auth()->check())
                    <span>Bienvenido, {{ auth()->user()->nombre }}</span>
                @endif
                    <div class="admin-welcome">
                    <a href="{{ route('home') }}" class="btn-purple"><i class="fa-solid fa-house"></i>
                        Ver Página Principal</a>
                    <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i>
                        </a>

                </div>
            </div>

            <div class="content-section">
                <div class="alert alert-info admin-welcome-message p-4 mb-4">
                    <h3 class="mb-3"><i class="fas fa-tachometer-alt"></i> ¡Bienvenido al panel del gestor!</h3>
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

                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-history fa-3x"></i>
                            </div>
                            <h3>Historial de Reservas</h3>
                            <p>Consulta el historial completo de todas las reservas realizadas en el sistema.</p>
                            <a href="{{ route('gestor.historial') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-comments fa-3x"></i>
                            </div>
                            <h3>Chats con clientes</h3>
                            <p>Aqui podrás ver todas las conversaciones con los clientes.</p>
                            <a href="{{ route('gestor.historial') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="admin-card shadow-sm">
                            <div class="admin-card-icon">
                                <i class="fas fa-parking fa-3x"></i>
                            </div>
                            <h3>Gestión de parkings</h3>
                            <p>Aqui podrás ver y gestionar de los parkings de tu sede.</p>
                            <a href="{{ route('gestor.parking.index') }}" class="btn-admin-card">
                                Acceder <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection