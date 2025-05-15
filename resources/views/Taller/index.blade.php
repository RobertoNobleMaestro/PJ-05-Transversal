@extends('layouts.admin')

@section('title', 'Lista de Vehículos')

@section('content')

<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('gestor.vehiculos') }}"
                   class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}">
                   <i class="fas fa-car"></i> Vehículos</a></li>
            <li><a href="{{ route('gestor.chat.listar') }}"
                   class="{{ request()->routeIs('gestor.chat.listar*') ? 'active' : '' }}">
                   <i class="fas fa-comments"></i> Chats</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Mantenimiento de Vehículos</h1>
            <a href="{{ route('gestor.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div id="vehiculos-table-container">
            <table class="crud-table" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Kilometraje</th>
                        <th>Año</th>
                        <th>Precio por Día</th>
                        <th>Lugar</th>
                        <th>Tipo</th>
                        <th>Parking</th>
                        <th>Últ. Mantenimiento</th>
                        <th>Próx. Mantenimiento</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vehiculos as $vehiculo)
                        <tr>
                            <td>{{ $vehiculo->id_vehiculos }}</td>
                            <td>{{ $vehiculo->marca }}</td>
                            <td>{{ $vehiculo->modelo }}</td>
                            <td>{{ $vehiculo->kilometraje }}</td>
                            <td>{{ $vehiculo->año }}</td>
                            <td>{{ $vehiculo->precio_dia }}</td>
                            <td>{{ $vehiculo->lugar->nombre }}</td>
                            <td>{{ $vehiculo->tipo->nombre }}</td>
                            <td>{{ $vehiculo->parking->nombre }}</td>
                            <td>{{ $vehiculo->ultima_fecha_mantenimiento }}</td>
                            <td id="prox-mant-{{ $vehiculo->id_vehiculos }}">{{ $vehiculo->proxima_fecha_mantenimiento }}</td>
                            <td>
                                <button class="btn btn-primary btn-sm btn-agendar-mantenimiento" 
                                        data-id="{{ $vehiculo->id_vehiculos }}">
                                    <i class="fas fa-calendar-check"></i> Agendar
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para agendar mantenimiento -->
<div class="modal fade" id="modalAgendarMantenimiento" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Agendar Mantenimiento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgendarMantenimiento">
                    <input type="hidden" id="vehiculo-id" name="vehiculo_id">
                    
                    <div class="mb-3">
                        <label for="taller-id" class="form-label">Taller</label>
                        <select class="form-select" id="taller-id" name="taller_id" required>
                            <option value="">Seleccione un taller</option>
                            @foreach($talleres as $taller)
                                <option value="{{ $taller->id }}">{{ $taller->nombre }} - {{ $taller->direccion }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">Por favor seleccione un taller.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha-mantenimiento" class="form-label">Fecha de Mantenimiento</label>
                        <input type="date" class="form-control" id="fecha-mantenimiento" name="fecha_mantenimiento" required min="{{ date('Y-m-d') }}">
                        <div class="invalid-feedback">La fecha no puede ser anterior a hoy.</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="hora-mantenimiento" class="form-label">Hora de Mantenimiento</label>
                        <select class="form-select" id="hora-mantenimiento" name="hora_mantenimiento" disabled required>
                            <option value="">Seleccione primero un taller y fecha</option>
                        </select>
                        <div class="text-info mt-1" id="disponibilidad-info"></div>
                    </div>
                    
                    <div class="alert alert-warning" id="alerta-disponibilidad" style="display: none">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Recuerde que solo se pueden agendar 2 vehículos por hora en cada taller.
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Custom JS -->
<script src="{{ asset('js/taller.js') }}"></script>
@endsection