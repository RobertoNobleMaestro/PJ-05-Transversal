@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Balance de Pasivos</h1>
            <p class="text-muted">Obligaciones pendientes de pago según esquema contable estudiantil</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#nuevoPasivoModal">
                <i class="fas fa-plus"></i> Nuevo Pasivo
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
            <form action="{{ route('admin.financiero.balance.pasivos') }}" method="GET">
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

    <!-- Resumen de pasivos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Resumen de Pasivos Corrientes</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Mantenimiento Vehículos -->
                <div class="col-md-6 mb-3">
                    <div class="card border-left-danger h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-2x text-danger"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Mantenimiento Vehículos</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($pasivosPorCategoria['Mantenimiento Vehículos'] ?? 0, 2, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Facturas pendientes por reparaciones</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Parkings -->
                <div class="col-md-6 mb-3">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-paint-roller fa-2x text-warning"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Mantenimiento Parkings</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($pasivosPorCategoria['Mantenimiento Parkings'] ?? 0, 2, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">
                                        @if(session('parkings_detalle'))
                                            Calculado a {{ session('precio_metro_mantenimiento') }}€/m² (Total: {{ number_format(session('metros_cuadrados_totales'), 0, ',', '.') }} m²)
                                        @else
                                            Facturas pendientes de instalaciones
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                

            </div>
        </div>
    </div>

    <!-- Tabla de pasivos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalle de Pasivos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Pasivo</th>
                            <th>Descripción</th>
                            <th>Importe</th>
                            <th>Fecha Vencimiento</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasivos as $pasivo)
                        <tr>
                            <td>
                                @if($pasivo->categoria == 'Mantenimiento Vehículos')
                                    <i class="fas fa-tools text-danger me-2"></i> Mant. Vehículos
                                @elseif($pasivo->categoria == 'Mantenimiento Parkings')
                                    <i class="fas fa-paint-roller text-warning me-2"></i> Mant. Parkings
                                @elseif($pasivo->categoria == 'Ingresos No Devengados')
                                    <i class="fas fa-calendar-alt text-info me-2"></i> Reserva anticipada
                                @else
                                    <i class="fas fa-file-invoice-dollar text-secondary me-2"></i> {{ $pasivo->categoria }}
                                @endif
                            </td>
                            <td>{{ $pasivo->nombre }}</td>
                            <td>{{ number_format($pasivo->valor, 2, ',', '.') }} €</td>
                            <td>{{ \Carbon\Carbon::parse($pasivo->fecha_vencimiento)->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $hoy = \Carbon\Carbon::now();
                                    $vencimiento = \Carbon\Carbon::parse($pasivo->fecha_vencimiento);
                                    $diasRestantes = $hoy->diffInDays($vencimiento, false);
                                @endphp
                                
                                @if($diasRestantes < 0)
                                    <span class="badge bg-danger">Vencido ({{ abs($diasRestantes) }} días)</span>
                                @elseif($diasRestantes <= 15)
                                    <span class="badge bg-warning">Próximo ({{ $diasRestantes }} días)</span>
                                @else
                                    <span class="badge bg-success">En plazo ({{ $diasRestantes }} días)</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2">Total Pasivos</th>
                            <th>{{ number_format($totalPasivos, 2, ',', '.') }} €</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notas sobre pasivos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Notas sobre Pasivos</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <strong>Pasivos Corrientes:</strong> Facturas pendientes que se pagarán en menos de 1 año
                </li>
                <li class="list-group-item">
                    <i class="fas fa-calendar-alt text-info me-2"></i>
                    <strong>Ingresos No Devengados:</strong> Pagos recibidos por servicios aún no prestados
                </li>
                <li class="list-group-item bg-light">
                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                    <strong>Ejemplo:</strong> Una reserva pagada de 1.000€ para el mes siguiente es un pasivo hasta que se entregue el vehículo.
                </li>
            </ul>
        </div>
    </div>

    <!-- Modal para añadir nuevo pasivo -->
    <div class="modal fade" id="nuevoPasivoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Nuevo Pasivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.financiero.pasivo.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Tipo de Pasivo</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="Mantenimiento Vehículos">Mantenimiento Vehículos</option>
                                <option value="Mantenimiento Parkings">Mantenimiento Parkings</option>
                                <option value="Ingresos No Devengados">Ingresos No Devengados (Reservas anticipadas)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Descripción</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Importe (€)</label>
                            <input type="number" step="0.01" class="form-control" id="valor" name="valor" required>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_vencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
