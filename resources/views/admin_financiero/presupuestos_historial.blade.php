@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Historial de Presupuestos</h1>
            <p class="text-muted">Registro histórico de presupuestos asignados para {{ $sede->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.presupuestos') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver a Presupuestos
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

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Filtrar Presupuestos</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.financiero.presupuestos.historial') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="anio" class="form-label">Año</label>
                    <select name="anio" id="anio" class="form-select">
                        <option value="">Todos los años</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="mes" class="form-label">Mes</label>
                    <select name="mes" id="mes" class="form-select">
                        <option value="">Todos los meses</option>
                        @foreach($meses as $key => $nombreMes)
                            <option value="{{ $key }}" {{ $mesSeleccionado == $key ? 'selected' : '' }}>{{ $nombreMes }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="categoria" class="form-label">Categoría</label>
                    <select name="categoria" id="categoria" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria }}" {{ $categoriaSeleccionada == $categoria ? 'selected' : '' }}>{{ $categoria }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-grid gap-2 w-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <a href="{{ route('admin.financiero.presupuestos.historial') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-broom"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de presupuestos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Presupuestos Registrados</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Categoría</th>
                            <th>Periodo</th>
                            <th>Monto Presupuestado</th>
                            <th>Gasto Real</th>
                            <th>Diferencia</th>
                            <th>Estado</th>
                            <th>Creado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($presupuestos->count() > 0)
                            @foreach($presupuestos as $presupuesto)
                                @php
                                    $diferencia = $presupuesto->monto - ($presupuesto->gasto_real ?? 0);
                                    $porcentaje = $presupuesto->monto > 0 ? (($presupuesto->gasto_real ?? 0) / $presupuesto->monto) * 100 : 0;
                                    
                                    $estado = 'info';
                                    if ($presupuesto->gasto_real !== null) {
                                        $estado = $diferencia >= 0 ? 'success' : ($diferencia > ($presupuesto->monto * -0.1) ? 'warning' : 'danger');
                                    }
                                @endphp
                                <tr>
                                    <td>
                                        <div>
                                            @if($presupuesto->categoria == 'Gastos de Personal - Salarios')
                                                <i class="fas fa-users me-2 text-primary"></i>
                                            @elseif($presupuesto->categoria == 'Gastos de Mantenimiento - Parkings')
                                                <i class="fas fa-parking me-2 text-secondary"></i>
                                            @elseif($presupuesto->categoria == 'Gastos de Mantenimiento - Vehículos')
                                                <i class="fas fa-car-alt me-2 text-warning"></i>
                                            @elseif($presupuesto->categoria == 'Gastos Fiscales - Impuestos')
                                                <i class="fas fa-file-invoice-dollar me-2 text-danger"></i>
                                            @else
                                                <i class="fas fa-ellipsis-h me-2 text-muted"></i>
                                            @endif
                                            {{ $presupuesto->categoria }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $presupuesto->fecha_inicio->format('F Y') }}
                                        <small class="d-block text-muted">{{ $presupuesto->periodo_tipo }}</small>
                                    </td>
                                    <td class="text-end">{{ number_format($presupuesto->monto, 2, ',', '.') }} €</td>
                                    <td class="text-end">
                                        @if($presupuesto->gasto_real !== null)
                                            {{ number_format($presupuesto->gasto_real, 2, ',', '.') }} €
                                        @else
                                            <span class="badge bg-secondary">No registrado</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($presupuesto->gasto_real !== null)
                                            <span class="text-{{ $estado }}">
                                                {{ $diferencia >= 0 ? '+' : '' }}{{ number_format($diferencia, 2, ',', '.') }} €
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($presupuesto->gasto_real !== null)
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="badge bg-{{ $estado }} mb-1">
                                                    @if($estado == 'success')
                                                        <i class="fas fa-check-circle me-1"></i> Cumplido
                                                    @elseif($estado == 'warning')
                                                        <i class="fas fa-exclamation-triangle me-1"></i> Alerta
                                                    @elseif($estado == 'danger')
                                                        <i class="fas fa-times-circle me-1"></i> Excedido
                                                    @else
                                                        <i class="fas fa-info-circle me-1"></i> Sin datos
                                                    @endif
                                                </span>
                                                <div class="progress" style="height: 5px; width: 100px;">
                                                    <div class="progress-bar bg-{{ $estado }}" role="progressbar" 
                                                         style="width: {{ min($porcentaje, 100) }}%" 
                                                         aria-valuenow="{{ $porcentaje }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small>{{ number_format($porcentaje, 1) }}%</small>
                                            </div>
                                        @else
                                            <span class="badge bg-secondary">Pendiente</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        {{ $presupuesto->created_at->format('d/m/Y') }}
                                        <small class="d-block text-muted">
                                            {{ $presupuesto->creadoPor ? $presupuesto->creadoPor->nombre : 'Sistema' }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-info-circle me-2 text-info"></i>
                                    No se encontraron presupuestos con los filtros aplicados.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $presupuestos->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
