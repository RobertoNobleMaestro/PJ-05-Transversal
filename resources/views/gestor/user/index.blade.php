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

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Gestión de Usuarios</h1>
                <a href="{{ route('gestor.index') }}" class="btn-purple">
                    <i class="fas fa-arrow-left"></i> Volver al Panel
                </a>
            </div>

            <div class="filter-section">
                <div class="filter-group">
                    <input type="text" class="search-input" placeholder="Buscar por nombre..." id="searchUser">
                    <select class="filter-control" id="filterRole">
                        <option value="">Todos los roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id_roles }}">{{ ucwords(str_replace('_', ' ', $role->nombre)) }}</option>
                        @endforeach
                    </select>
                    <select class="filter-control" id="perPageSelect">
                        <option value="5">5 por página</option>
                        <option value="10" selected>10 por página</option>
                        <option value="20">20 por página</option>
                    </select>
                    <button id="clearFilters" class="btn btn-outline-danger"><i class="fa-solid fa-trash"></i></button>
                </div>

                <a href="{{ route('gestor.user.create') }}" class="add-user-btn">Añadir Usuario</a>
            </div>


            <div id="loading-users" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando usuarios...</p>
            </div>
            <div id="users-table-container" style="display: none;" data-url="{{ route('gestor.user.data') }}">
                <table class="crud-table" id="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>DNI</th>
                            <th>Rol</th>
                            <th>Parking</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- datos mediante AJAX -->
                    </tbody>
                </table>
                    <!-- Paginación -->
                    <div class="pagination-container mt-4" id="pagination-controls">
                        <div class="pagination-info">
                            <span id="pagination-summary">Mostrando 0 de 0 usuarios</span>
                        </div>
                        <div class="pagination-buttons">
                            <button id="prev-page" class="btn btn-sm btn-outline-primary" >
                                <i class="fas fa-chevron-left"></i> Anterior
                            </button>
                            <span id="page-indicator" class="mx-2">Página 1 de 1</span>
                            <button id="next-page" class="btn btn-sm btn-outline-primary" >
                                Siguiente <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset('js/gestor-users.js') }}"></script>
@endsection