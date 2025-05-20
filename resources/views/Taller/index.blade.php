@extends('layouts.admin')

@section('title', 'Lista de Vehículos')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/taller-index.css') }}">
@endpush


<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('taller.index') }}" class="{{ request()->routeIs('taller.index*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Gestión del Taller</a></li>
            <li><a href="{{ route('Taller.historial') }}" class="{{ request()->routeIs('Taller.historial*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Historial Mantenimiento</a></li>

        </ul>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Vehículos</h1>
                <a href="{{ route('gestor.index') }}" class="btn-outline-purple">
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


        <div class="row mb-4 g-2 align-items-end">
            <div class="col-md-3">
                <label for="filtro-sede" class="filtros-label">Sede</label>
                <select id="filtro-sede" class="form-select select-purple" style="border">
                    <option value="">Todas</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="filtro-anio" class="filtros-label">Año</label>
                <select id="filtro-anio" class="form-select select-purple">
                    <option value="">Todos</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}">{{ $anio }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro-tipo" class="filtros-label">Tipo</label>
                <select id="filtro-tipo" class="form-select select-purple">
                    <option value="">Todos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="filtro-parking" class="filtros-label">Parking</label>
                <select id="filtro-parking" class="form-select select-purple">
                    <option value="">Todos</option>
                    @foreach($parkings as $parking)
                        <option value="{{ $parking->id }}">{{ $parking->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1 d-flex flex-column justify-content-end">
                <label class="filtros-label">&nbsp;</label>
                <button id="btn-limpiar-filtros" class="btn btn-outline-purple h-100 w-100 d-flex align-items-center justify-content-center" title="Limpiar filtros" style="min-height:38px;">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>

        <!-- Tabla de vehículos -->
        <div id="vehiculos-table-container">
            @include('Taller.partials.tabla_vehiculos', ['vehiculos' => $vehiculos])
        </div>
    </div>
</div>

<!-- Modal para agendar mantenimiento -->
<div class="modal fade" id="modalAgendarMantenimiento" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content custom-modal-content">
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

                    <div class="mb-3">
                        <label for="motivo-reserva" class="form-label">Motivo de la reserva</label>
                        <select class="form-select" id="motivo-reserva" name="motivo_reserva" required>
                            <option value="">Seleccione el motivo</option>
                            <option value="mantenimiento">Mantenimiento</option>
                            <option value="averia">Avería</option>
                        </select>
                        <div class="invalid-feedback">Por favor seleccione el motivo de la reserva.</div>
                    </div>
                    <div class="mb-3" id="motivo-averia-container" style="display:none;">
                        <label for="motivo-averia" class="form-label">Motivo de la avería (breve)</label>
                        <input type="text" class="form-control" id="motivo-averia" name="motivo_averia" maxlength="100" placeholder="Describa brevemente la avería">
                    </div>
                    
                    <div class="alert alert-warning" id="alerta-disponibilidad" style="display: none">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Recuerde que solo se pueden agendar 2 vehículos por hora en cada taller.
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-purple flex-grow-1">Agendar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    


@section('scripts')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    // Mostrar/ocultar campo motivo avería
    $('#motivo-reserva').on('change', function() {
        if ($(this).val() === 'averia') {
            $('#motivo-averia-container').show();
            $('#motivo-averia').prop('required', true);
        } else {
            $('#motivo-averia-container').hide();
            $('#motivo-averia').prop('required', false);
            $('#motivo-averia').val('');
        }
    });
    function getFiltrosData() {
        return {
            sede: $('#filtro-sede').val(),
            año: $('#filtro-anio').val(),
            tipo: $('#filtro-tipo').val(),
            parking: $('#filtro-parking').val(),
            _token: '{{ csrf_token() }}'
        };
    }
    function filtrarVehiculos(pageUrl) {
        var data = getFiltrosData();
        var url = pageUrl || '{{ route('taller.filtrar') }}';
        // Permitir paginar aunque no haya filtros activos
        $.ajax({
            url: url,
            method: 'POST',
            data: data,
            success: function(response) {
                $('#vehiculos-table-container').html(response.html);
                // Reinicializar DataTable solo para responsive y orden
                if ($.fn.DataTable.isDataTable('#vehiculos-table')) {
                    $('#vehiculos-table').DataTable().destroy();
                }
                $('#vehiculos-table').DataTable({
                    "pageLength": 10,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                    },
                    "order": [[0, "asc"]],
                    "responsive": true,
                    "paging": false,
                    "info": false,
                    "searching": false,
                    "ordering": true
                });
            }
        });
    }
    $('#filtro-sede, #filtro-anio, #filtro-tipo, #filtro-parking').on('change', function() {
        filtrarVehiculos();
    });
    $('#btn-limpiar-filtros').on('click', function(e) {
        e.preventDefault();
        $('#filtro-sede').val('');
        $('#filtro-anio').val('');
        $('#filtro-tipo').val('');
        $('#filtro-parking').val('');
        filtrarVehiculos();
    });
    // Delegación para paginación AJAX
    $(document).on('click', '.taller-pagination .page-link', function(e) {
        var $parent = $(this).parent();
        var href = $(this).attr('href');
        // Solo ejecutar si el link NO es disabled ni active y tiene href
        if (!$parent.hasClass('disabled') && !$parent.hasClass('active') && href && href !== '#') {
            e.preventDefault();
            filtrarVehiculos(href);
        }
    });
});
</script>
<!-- Bootstrap JS Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Moment.js para formateo de fechas -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/es.js"></script>
<!-- Custom JS -->
<script src="{{ asset('js/taller.js') }}"></script>
@endsection
