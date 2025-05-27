@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Balance de Activos</h1>
            <p class="text-muted">Activos fijos según esquema contable estudiantil</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoActivoModal">
                <i class="fas fa-plus"></i> Nuevo Activo
            </button>
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

    <!-- Filtro por periodo simple -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Período de análisis</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.financiero.balance.activos') }}" method="GET">
                <div class="row">
                    <div class="col-md-9">
                        <select name="periodo" id="periodo" class="form-select">
                            @foreach($periodos as $valor => $nombre)
                                <option value="{{ $valor }}" {{ $periodoSeleccionado == $valor ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">Actualizar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de activos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Resumen de Activos Fijos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Vehículos -->
                <div class="col-md-6 mb-3">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-car fa-2x text-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Vehículos (Activo Fijo)</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($activosPorCategoria['Vehículos'] ?? 0, 2, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Flota valorada - Se deprecia anualmente</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Parkings -->
                <div class="col-md-6 mb-3">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-parking fa-2x text-success"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Parkings (Activo Fijo)</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($activosPorCategoria['Parkings'] ?? 0, 2, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Estacionamientos propios - Se deprecia anualmente</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de activos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalle de Activos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Activo</th>
                            <th>Descripción</th>
                            <th>Valor Inicial</th>
                            <th>Depreciación Anual</th>
                            <th>Valor Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($activos as $activo)
                        <tr>
                            <td>
                                @if($activo->categoria == 'Vehículos')
                                    <i class="fas fa-car text-primary me-2"></i> Vehículo
                                @elseif($activo->categoria == 'Parkings')
                                    <i class="fas fa-parking text-success me-2"></i> Parking
                                @else
                                    <i class="fas fa-box text-secondary me-2"></i> {{ $activo->categoria }}
                                @endif
                            </td>
                            <td>{{ $activo->nombre }}</td>
                            <td>{{ number_format($activo->valor_inicial, 2, ',', '.') }} €</td>
                            <td>
                                @if($activo->categoria == 'Vehículos')
                                    {{ number_format($activo->valor_inicial * 0.20, 2, ',', '.') }} €
                                    <small class="text-muted">(20% anual)</small>
                                @elseif($activo->categoria == 'Parkings')
                                    {{ number_format($activo->valor_inicial * 0.10, 2, ',', '.') }} €
                                    <small class="text-muted">(10% anual)</small>
                                @else
                                    {{ number_format($activo->valor_inicial * 0.15, 2, ',', '.') }} €
                                    <small class="text-muted">(15% anual)</small>
                                @endif
                            </td>
                            <td>{{ number_format($activo->valor_actual, 2, ',', '.') }} €</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4">Total Activos</th>
                            <th>{{ number_format($totalActivos, 2, ',', '.') }} €</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notas sobre depreciación -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Notas sobre Depreciación</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-car text-primary me-2"></i>
                    <strong>Vehículos:</strong> Depreciación lineal del 20% anual (vida útil de 5 años)
                </li>
                <li class="list-group-item">
                    <i class="fas fa-parking text-success me-2"></i>
                    <strong>Parkings:</strong> Depreciación lineal del 10% anual (vida útil de 10 años)
                </li>
                <li class="list-group-item bg-light">
                    <i class="fas fa-info-circle text-info me-2"></i>
                    <strong>Ejemplo:</strong> Un vehículo con valor inicial de 20.000€ genera un gasto por depreciación de 4.000€ anuales.
                </li>
            </ul>
        </div>
    </div>

    <!-- Modal para añadir nuevo activo -->
    <div class="modal fade" id="nuevoActivoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nuevo Activo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.financiero.activo.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Tipo de Activo</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="Vehículos">Vehículos</option>
                                <option value="Parkings">Parkings</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor_inicial" class="form-label">Valor Inicial (€)</label>
                            <input type="number" step="0.01" class="form-control" id="valor_inicial" name="valor_inicial" required>
                            <div class="form-text">Valor de adquisición del activo.</div>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_adquisicion" class="form-label">Fecha de Adquisición</label>
                            <input type="date" class="form-control" id="fecha_adquisicion" name="fecha_adquisicion" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
