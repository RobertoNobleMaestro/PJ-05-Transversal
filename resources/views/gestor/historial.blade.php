@extends('layouts.admin')

@section('title', 'Historial de Reservas')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-badges.css') }}">

<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div> 
    
    <div class="admin-sidebar" id="sidebar">
        <div style="position: fixed;width: 220px;">
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
                        <li><a href="{{ route('gestor.user.index') }}"
                    class="{{ request()->routeIs('gestor.user.index') ? 'active' : '' }}"><i
                        class="fas fa-user"></i>Usuarios</a></li>
                </ul>
            </div>            
        </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">
                Historial de Reservas @if(isset($lugarGestor)) de {{ $lugarGestor->nombre }} @endif
            </h1>
            <a href="{{ route('gestor.index') }}" class="btn-purple">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
        
        <!-- Sección de estadísticas -->
        <div class="stats-container mb-4" id="stats-container">
            <!-- Las estadísticas se cargarán aquí mediante AJAX -->
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <div class="value" id="total-reservas">-</div>
                <div class="label">Total Reservas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="value" id="reservas-completadas">-</div>
                <div class="label">Completadas</div>
            </div>
             <div class="stat-card">
                <div class="icon"><i class="fas fa-envelope-open-text"></i></i></div>
                <div class="value" id="reservas-confirmadas">-</div>
                <div class="label">Confirmadas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="value" id="reservas-pendientes">-</div>
                <div class="label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <div class="value" id="reservas-canceladas">-</div>
                <div class="label">Canceladas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-euro-sign"></i></div>
                <div class="value" id="ingreso-total">-</div>
                <div class="label">Ingresos Totales</div>
            </div>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <!-- Filtro por usuario -->
                <input type="text" class="filter-control" placeholder="Usuario..." id="filterUsuario">
                
                <!-- Filtro por lugar -->
                <select hidden class="filter-control" id="filterLugar">
                    <option value="">Todos los lugares</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por estado -->
                <select class="filter-control" id="filterEstado">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
                
                <!-- Filtros de fecha -->
                <div class="d-flex gap-2 align-items-center">
                    <span>Desde:</span>
                    <input type="date" class="filter-control" id="filterFechaDesde">
                    <span>Hasta:</span>
                    <input type="date" class="filter-control" id="filterFechaHasta">
                </div>
                
                <button id="clearFilters" class="btn-purple">Limpiar</button>
            </div>
        </div>
        
        <div id="loading-historial" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando historial de reservas...</p>
        </div>
        <div id="historial-table-container" style="display: none;" data-url="{{ route('gestor.historial.data') }}">
            <table class="crud-table" id="historial-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Vehículos</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@section('scripts')
<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-historial.js') }}"></script>
@endsection
