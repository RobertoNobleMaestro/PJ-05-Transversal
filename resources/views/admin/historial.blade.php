@extends('layouts.admin')

@section('title', 'Historial de Reservas')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-badges.css') }}">

<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('admin.vehiculos') }}" class="{{ request()->routeIs('admin.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i> Vehículos</a></li>
            <li><a href="{{ route('admin.lugares') }}" class="{{ request()->routeIs('admin.lugares*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Lugares</a></li>
            <li><a href="{{ route('admin.reservas.index') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
            <li><a href="{{ route('admin.historial') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Historial de Reservas</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
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
                <select class="filter-control" id="filterLugar">
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
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
        </div>
        
        <div id="loading-historial" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando historial de reservas...</p>
        </div>
        <div id="historial-table-container" style="display: none;" data-url="{{ route('admin.historial.data') }}">
            <table class="crud-table" id="historial-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Vehículos</th>
                        <th>Detalles</th>
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
