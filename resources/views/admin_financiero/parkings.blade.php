@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <!-- Encabezado de página -->
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Gestión de Parkings</h1>
            <p class="text-muted">Administración de metros cuadrados y precios de parkings</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-primary">Listado de Parkings</h5>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <h5>Información Importante</h5>
                <p>Los valores de metros cuadrados y precio por metro cuadrado son utilizados en diversos cálculos financieros a lo largo del sistema, incluyendo:</p>
                <ul>
                    <li>Cálculo del valor total de los activos inmobiliarios</li>
                    <li>Estimación de costos de mantenimiento basados en el tamaño del parking</li>
                    <li>Proyecciones financieras y presupuestos anuales</li>
                    <li>Informes de valoración para auditorías e inversores</li>
                </ul>
                <p>Asegúrese de mantener estos valores actualizados para garantizar la precisión de los informes financieros.</p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Ubicación</th>
                            <th>Metros Cuadrados</th>
                            <th>Precio por Metro (€)</th>
                            <th>Valor Total (€)</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($parkings as $parking)
                        <tr>
                            <td>{{ $parking->id }}</td>
                            <td>{{ $parking->nombre }}</td>
                            <td>
                                {{ $parking->lugar ? $parking->lugar->nombre : 'No asignado' }}<br>
                                <small class="text-muted">{{ $parking->lugar ? $parking->lugar->direccion : '' }}</small>
                            </td>
                            <td>{{ number_format($parking->metros_cuadrados, 0, ',', '.') }} m²</td>
                            <td>{{ number_format($parking->precio_por_metro, 2, ',', '.') }} €</td>
                            <td>{{ number_format($parking->valor_total, 2, ',', '.') }} €</td>
                            <td>
                                <a href="{{ route('admin.financiero.parkings.edit', $parking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            order: [[0, 'asc']]
        });
    });
</script>
@endsection
