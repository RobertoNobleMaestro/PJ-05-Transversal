@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Parkings</h1>
            <p class="text-muted">Administración de metros cuadrados y precios de parkings</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.gastos') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Gastos
            </a>
        </div>
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

    <!-- Tabla de parkings -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Lista de Parkings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped">
                    <thead class="table-light">
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
                        @forelse($parkings as $parking)
                            <tr>
                                <td>{{ $parking->id }}</td>
                                <td>{{ $parking->nombre }}</td>
                                <td>
                                    {{ $parking->lugar->nombre ?? 'Sin ubicación' }}
                                    <small class="d-block text-muted">{{ $parking->lugar->direccion ?? '' }}</small>
                                </td>
                                <td class="text-center">{{ number_format($parking->metros_cuadrados ?? 0, 0, ',', '.') }} m²</td>
                                <td class="text-center">{{ number_format($parking->precio_metro ?? 0, 2, ',', '.') }} €</td>
                                <td class="text-center">
                                    @php
                                        $valorTotal = ($parking->metros_cuadrados ?? 0) * ($parking->precio_metro ?? 0);
                                    @endphp
                                    <strong>{{ number_format($valorTotal, 2, ',', '.') }} €</strong>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.financiero.parkings.edit', $parking->id) }}" class="btn btn-primary btn-sm">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No hay parkings registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Información adicional sobre la importancia de los metros cuadrados y precios -->
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-info-circle text-primary me-2"></i>
                Información Importante
            </h5>
            <p class="card-text">
                Los valores de <strong>metros cuadrados</strong> y <strong>precio por metro cuadrado</strong> son utilizados en diversos cálculos 
                financieros a lo largo del sistema, incluyendo:
            </p>
            <ul>
                <li>Cálculo del valor total de los activos inmobiliarios</li>
                <li>Estimación de costos de mantenimiento basados en el tamaño del parking</li>
                <li>Proyecciones financieras y presupuestos anuales</li>
                <li>Informes de valoración para auditorías e inversores</li>
            </ul>
            <p class="card-text">
                Asegúrese de mantener estos valores actualizados para garantizar la precisión de los informes financieros.
            </p>
        </div>
    </div>
</div>
@endsection
