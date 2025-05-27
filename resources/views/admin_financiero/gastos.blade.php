@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Gastos - {{ $sede->nombre }}</h1>
            <p class="text-muted">Presupuesto {{ $tipoVista == 'mensual' ? 'mensual' : 'trimestral' }} y análisis de gastos</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group mb-2" role="group">
                <a href="{{ route('admin.financiero.gastos', ['tipo' => 'mensual']) }}" class="btn {{ $tipoVista == 'mensual' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-calendar-day"></i> Visión Mensual
                </a>
                <a href="{{ route('admin.financiero.gastos', ['tipo' => 'trimestral']) }}" class="btn {{ $tipoVista == 'trimestral' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-calendar-week"></i> Visión Trimestral
                </a>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('admin.financiero.presupuestos') }}" class="btn btn-success">
                    <i class="fas fa-chart-pie"></i> CREAR PRESUPUESTOS
                </a>
                <a href="{{ route('admin.financiero.parkings') }}" class="btn btn-info text-white">
                    <i class="fas fa-parking"></i> CRUD DE PARKINGS
                </a>
            </div>
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

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-money-bill-wave fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($totalGastos, 2, ',', '.') }} €</h3>
                            <div>Total de Gastos {{ $tipoVista == 'mensual' ? 'Mensuales' : 'Trimestrales' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-users fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($categorias['Gastos de Personal - Salarios'] ?? 0, 2, ',', '.') }} €</h3>
                            <div>Gastos en Personal</div>
                            <small>{{ $totalGastos > 0 ? number_format((($categorias['Gastos de Personal - Salarios'] ?? 0) / $totalGastos) * 100, 1) : '0.0' }}% del total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-car fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($categorias['Gastos de Mantenimiento - Vehículos'] ?? 0, 2, ',', '.') }} €</h3>
                            <div>Mantenimiento Vehículos</div>
                            <small>{{ $totalGastos > 0 ? number_format((($categorias['Gastos de Mantenimiento - Vehículos'] ?? 0) / $totalGastos) * 100, 1) : '0.0' }}% del total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-secondary text-white shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-parking fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($categorias['Gastos de Mantenimiento - Parkings'] ?? 0, 2, ',', '.') }} €</h3>
                            <div>Mantenimiento Parkings</div>
                            <small>{{ $totalGastos > 0 ? number_format((($categorias['Gastos de Mantenimiento - Parkings'] ?? 0) / $totalGastos) * 100, 1) : '0.0' }}% del total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Se ha eliminado la sección de "Variación vs. Periodo Anterior" según lo solicitado -->
    </div>

    <div class="row">
        <!-- Gráfico circular de distribución de gastos -->
        <div class="col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-white">Distribución de Gastos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4 text-left">
                                @foreach(array_keys($categorias) as $index => $categoria)
                                    <div class="mb-2">
                                        <i class="fas fa-circle me-2" style="color: {{ 'hsl(' . ($index * 36) . ', 70%, 50%)' }}"></i>
                                        <strong>{{ $categoria }}:</strong> {{ number_format($categorias[$categoria], 2, ',', '.') }} €
                                        @if($categoria == 'Gastos de Mantenimiento - Parkings')
                                            <small class="text-muted d-block ms-4">({{ number_format($costoMantenimientoParking ?? 200, 0, ',', '.') }} €/mes por cada parking - {{ $countParkings ?? 0 }} parkings)</small>
                                        @elseif($categoria == 'Gastos de Mantenimiento - Vehículos')
                                            <small class="text-muted d-block ms-4">({{ number_format($costoMantenimientoVehiculo ?? 750, 0, ',', '.') }} €/mes por cada vehículo - {{ $countVehiculos ?? 0 }} vehículos)</small>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla detallada de gastos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Detalle de Gastos por Categoría</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover align-middle">
                    <thead class="table-light text-center">
                        <tr>
                            <th class="align-middle" style="width: 25%">Categoría</th>
                            <th class="align-middle" style="width: 20%">Importe {{ $tipoVista == 'mensual' ? 'Mensual' : 'Trimestral' }}</th>
                            <th class="align-middle" style="width: 15%">Porcentaje</th>
                            <th class="align-middle" style="width: 20%">Presupuesto</th>
                            <th class="align-middle" style="width: 20%">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categorias as $categoria => $valor)
                            @php
                                $porcentaje = ($valor / $totalGastos) * 100;
                                $variacion = rand(-15, 20);
                                $presupuesto = $valor * (rand(90, 120) / 100);
                                $diferencia = $valor - $presupuesto;
                                $estado = $diferencia <= 0 ? 'success' : ($diferencia < ($presupuesto * 0.1) ? 'warning' : 'danger');
                            @endphp
                            <tr>
                                <td>
                                    <div>
                                        @if($categoria == 'Gastos de Personal - Salarios')
                                            <i class="fas fa-users me-2 text-primary"></i>
                                        @elseif($categoria == 'Gastos de Mantenimiento - Parkings')
                                            <i class="fas fa-parking me-2 text-secondary"></i>
                                        @elseif($categoria == 'Gastos de Mantenimiento - Vehículos')
                                            <i class="fas fa-car-alt me-2 text-warning"></i>
                                        @elseif($categoria == 'Gastos Fiscales - Impuestos')
                                            <i class="fas fa-file-invoice-dollar me-2 text-danger"></i>
                                        @else
                                            <i class="fas fa-ellipsis-h me-2 text-muted"></i>
                                        @endif
                                        <strong>{{ $categoria }}</strong>
                                    </div>
                                    @if($categoria == 'Gastos de Mantenimiento - Parkings')
                                        <small class="text-muted d-block mt-1">({{ number_format($costoMantenimientoParking ?? 200, 0, ',', '.') }} €/mes por cada parking - {{ $countParkings ?? 0 }} parkings)</small>
                                    @elseif($categoria == 'Gastos de Mantenimiento - Vehículos')
                                        <small class="text-muted d-block mt-1">({{ number_format($costoMantenimientoVehiculo ?? 750, 0, ',', '.') }} €/mes por cada vehículo - {{ $countVehiculos ?? 0 }} vehículos)</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger text-white px-2 py-1 mb-1 d-block"><i class="fas fa-level-down-alt"></i> GASTO</span>
                                    <strong>{{ number_format($valor, 2, ',', '.') }} €</strong>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="mb-2"><strong>{{ number_format($porcentaje, 1) }}%</strong></div>
                                        <div class="progress" style="height: 8px; width: 80%;">
                                            <div class="progress-bar bg-primary" role="progressbar" 
                                                style="width: {{ $porcentaje }}%" 
                                                aria-valuenow="{{ $porcentaje }}" 
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <strong>{{ number_format($presupuesto, 2, ',', '.') }} €</strong>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex flex-column align-items-center">
                                        <span class="badge bg-{{ $estado }} mb-1">
                                            @if($estado == 'success')
                                                <i class="fas fa-check-circle me-1"></i> Cumplido
                                            @elseif($estado == 'warning')
                                                <i class="fas fa-exclamation-triangle me-1"></i> Alerta
                                            @else
                                                <i class="fas fa-times-circle me-1"></i> Excedido
                                            @endif
                                        </span>
                                        <div class="text-{{ $estado }}">
                                            <strong>{{ $diferencia <= 0 ? '' : '+' }}{{ number_format($diferencia, 2, ',', '.') }} €</strong>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light text-center fw-bold">
                            <th>Total</th>
                            <th>
                                <span class="badge bg-danger text-white px-2 py-1 mb-1 d-block"><i class="fas fa-level-down-alt"></i> TOTAL GASTO</span>
                                <strong>{{ number_format($totalGastos, 2, ',', '.') }} €</strong>
                            </th>
                            <th class="text-center">100%</th>
                            @php
                                $totalPresupuesto = $totalGastos * (rand(95, 105) / 100);
                                $diferenciaTotal = $totalGastos - $totalPresupuesto;
                                $estadoTotal = $diferenciaTotal <= 0 ? 'success' : ($diferenciaTotal < ($totalPresupuesto * 0.05) ? 'warning' : 'danger');
                            @endphp
                            <th class="text-center">{{ number_format($totalPresupuesto, 2, ',', '.') }} €</th>
                            <th class="text-center">
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge bg-{{ $estadoTotal }} mb-1">
                                        @if($estadoTotal == 'success')
                                            <i class="fas fa-check-circle me-1"></i> Cumplido
                                        @elseif($estadoTotal == 'warning')
                                            <i class="fas fa-exclamation-triangle me-1"></i> Alerta
                                        @else
                                            <i class="fas fa-times-circle me-1"></i> Excedido
                                        @endif
                                    </span>
                                    <div class="text-{{ $estadoTotal }}">
                                        <strong>{{ $diferenciaTotal <= 0 ? '' : '+' }}{{ number_format($diferenciaTotal, 2, ',', '.') }} €</strong>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Se ha eliminado la sección de "Recomendaciones para Optimización de Gastos" según lo solicitado -->
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Configuración del gráfico circular
        const categorias = {!! json_encode(array_keys($categorias)) !!};
        const valores = {!! json_encode(array_values($categorias)) !!};
        
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: categorias,
                datasets: [{
                    data: valores,
                    backgroundColor: categorias.map((_, index) => `hsl(${index * 36}, 70%, 50%)`),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${context.label}: ${value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2})} € (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Se ha eliminado la configuración del gráfico de evolución
    });
</script>
@endsection
