@extends('layouts.admin')

@section('title', 'CRUD de Usuarios')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

<div class="admin-container">
    <!-- Overlay para menu00fa mu00f3vil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('admin.reservas.index') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
            <li><a href="{{ route('admin.historial') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Usuarios</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <input type="text" class="search-input" placeholder="Buscar por nombre..." id="searchUser">
                <select class="filter-control" id="filterRole">
                    <option value="">Todos los roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id_roles }}">{{ ucfirst($role->nombre) }}</option>
                    @endforeach
                </select>
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
            <a href="{{ route('admin.users.create') }}" class="add-user-btn">Añadir Usuario</a>
        </div>
        
        <div id="loading-users" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando usuarios...</p>
        </div>
        <div id="users-table-container" style="display: none;" data-url="{{ route('admin.users.data') }}">
            <table class="crud-table" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- datos mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-users.js') }}"></script>
@endsection
