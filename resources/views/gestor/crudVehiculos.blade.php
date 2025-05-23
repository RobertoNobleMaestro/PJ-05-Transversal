@extends('layouts.admin')

@section('title', 'CRUD de Vehículos')


<link rel="stylesheet" href="{{ asset('css/gestor-pagination.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">


@section('content')
    


<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div style="position: fixed;width: 220px;">
            <div class="sidebar-title">CARFLOW</div>
                <ul class="sidebar-menu">
                    <li><a href="{{ route('gestor.vehiculos') }}"
                            class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i>
                            Vehículos</a></li>
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
                Gestión de Vehículos @if(isset($lugarGestor)) de {{ $lugarGestor->nombre }} @endif
            </h1>
            <a href="{{ route('gestor.index') }}" class="btn-purple">
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
                <select class="filter-control" id="filterLugar" hidden>
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
                <!-- <select class="filter-control" id="filterValoracion">
                    <option value="">Todas las valoraciones</option>
                    @foreach($valoraciones as $val)
                        <option value="{{ $val }}">{{ $val }}+ estrellas</option>
                    @endforeach
                </select> -->
                
                <select class="filter-control" id="filterParking">
                    <option value="">Todos los parkings</option>
                    @foreach($parkings as $parking)
                        <option value="{{ $parking->id }}">{{ $parking->nombre }}</option>
                    @endforeach
                </select>
                
                <button id="clearFilters" class="btn-purple">Limpiar</button>
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
                        <th>Imagen</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Kilometraje</th>
                        <th>Tipo</th>
                        <th>Parking</th>
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

<!-- Modal para mostrar las reservas -->
<div class="modal fade" id="reservasModal" tabindex="-1" aria-labelledby="reservasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reservasModalLabel">Reservas del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Reserva</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="reservasTableBody">
                        <!-- Las reservas se cargarán aquí -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-purple" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar las características -->
<div class="modal fade" id="caracteristicasModal" tabindex="-1" aria-labelledby="caracteristicasModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="caracteristicasModalLabel">Características del Vehículo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body" id="caracteristicasBody">
                <!-- Aquí se mostrarán las características -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-purple" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/gestor-vehiculos.js') }}"></script>
@endsection
