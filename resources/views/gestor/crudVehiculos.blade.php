@extends('layouts.admin')

@section('title', 'CRUD de Vehículos')


<link rel="stylesheet" href="{{ asset('css/gestor-pagination.css') }}">


@section('content')
    


<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div style="position: fixed; width:250px;">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href=""><i class="fas fa-map-marker-alt"></i> Lugares</a></li>
            </ul>            
        </div>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Vehículos</h1>
            <a href="{{ route('gestor.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">                
                <!-- Filtro por marca -->
                <input type="text" class="filter-control" placeholder="Marca..." id="filterMarca">
                
                <!-- Filtro por tipo de vehiculo -->
                <select class="filter-control" id="filterTipo">
                    <option value="">Todos los tipos</option>
                    @foreach($tipo as $tipo)
                        <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por lugar -->
                <select class="filter-control" id="filterLugar">
                    <option value="">Todos los lugares</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por año -->
                <select class="filter-control" id="filterAnio">
                    <option value="">Todos los años</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}">{{ $anio }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por valoracion -->
                <select class="filter-control" id="filterValoracion">
                    <option value="">Todas las valoraciones</option>
                    @foreach($valoraciones as $val)
                        <option value="{{ $val }}">{{ $val }}+ estrellas</option>
                    @endforeach
                </select>
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
            <a href="{{ route('gestor.vehiculos.create') }}" class="add-user-btn">Añadir Vehículo</a>
        </div>
        
        <div id="loading-vehiculos" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando vehículos...</p>
        </div>
        <div id="vehiculos-table-container" style="display: none;" data-url="{{ route('gestor.vehiculos.data') }}">
            <table class="crud-table" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Kilometraje</th>
                        <th>Lugar</th>
                        <th>Tipo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
            <!-- Paginación -->
            <div class="pagination-container mt-4" id="pagination-controls">
                <div class="pagination-info">
                    <span id="pagination-summary">Mostrando 0 de 0 vehículos</span>
                </div>
                <div class="pagination-buttons">
                    <button id="prev-page" class="btn btn-sm btn-outline-primary" disabled>
                        <i class="fas fa-chevron-left"></i> Anterior
                    </button>
                    <span id="page-indicator" class="mx-2">Página 1 de 1</span>
                    <button id="next-page" class="btn btn-sm btn-outline-primary" disabled>
                        Siguiente <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                <div class="per-page-selector d-flex flex-column" style="gap:5px;">
                    <label for="items-per-page">Por página:</label>
                    <select id="items-per-page" class="form-control">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/gestor-vehiculos.js') }}"></script>
@endsection
