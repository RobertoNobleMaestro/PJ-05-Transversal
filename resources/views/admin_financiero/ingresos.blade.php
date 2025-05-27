@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Ingresos - {{ $sede->nombre }}</h1>
            <p class="text-muted">Estimación {{ $tipoVista == 'mensual' ? 'mensual' : 'trimestral' }} y análisis de ingresos</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group" role="group">
                <a href="{{ route('admin.financiero.ingresos', ['tipo' => 'mensual']) }}" class="btn {{ $tipoVista == 'mensual' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-calendar-day"></i> Visión Mensual
                </a>
                <a href="{{ route('admin.financiero.ingresos', ['tipo' => 'trimestral']) }}" class="btn {{ $tipoVista == 'trimestral' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-calendar-week"></i> Visión Trimestral
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
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-3">
                            <i class="fas fa-money-bill-wave fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($totalIngresos, 2, ',', '.') }} €</h3>
                            <div>Total de Ingresos {{ $tipoVista == 'mensual' ? 'Mensuales' : 'Trimestrales' }}</div>
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
                            <i class="fas fa-car fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($fuentesIngresos['Ingresos por Alquiler de vehículos'] ?? 0, 2, ',', '.') }} €</h3>
                            <div>Alquiler de Vehículos</div>
                            <small>{{ $totalIngresos > 0 ? number_format((($fuentesIngresos['Ingresos por Alquiler de vehículos'] ?? 0) / $totalIngresos) * 100, 1) : '0' }}% del total</small>
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
                            <i class="fas fa-taxi fa-3x"></i>
                        </div>
                        <div class="col-9 text-end">
                            <h3 class="mb-0">{{ number_format($fuentesIngresos['Ingresos por Servicios de Taxi'] ?? 0, 2, ',', '.') }} €</h3>
                            <div>Servicios de Taxi</div>
                            <small>{{ $totalIngresos > 0 ? number_format((($fuentesIngresos['Ingresos por Servicios de Taxi'] ?? 0) / $totalIngresos) * 100, 1) : '0' }}% del total</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Se ha eliminado la sección de "Proyección próximo mes" según lo solicitado -->
    </div>

    <div class="row">
        <!-- Gráfico circular de distribución de ingresos -->
        <div class="col-md-12 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-white">Distribución de Ingresos</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="pieChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mt-4">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Fuente de Ingreso</th>
                                                <th class="text-end">Importe</th>
                                                <th class="text-end">%</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $totalIngresos = array_sum($fuentesIngresos); @endphp
                                            @foreach($fuentesIngresos as $fuente => $valor)
                                                @php 
                                                    $porcentaje = $totalIngresos > 0 ? ($valor / $totalIngresos) * 100 : 0;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-circle me-2" style="color: {{ 'hsl(' . (120 + $loop->index * 30) . ', 70%, 50%)' }}"></i> 
                                                        {{ $fuente }}
                                                    </td>
                                                    <td class="text-end">{{ number_format($valor, 2, ',', '.') }} €</td>
                                                    <td class="text-end">{{ number_format($porcentaje, 1) }}%</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla detallada de ingresos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Detalle de Ingresos por Fuente</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fuente de Ingresos</th>
                            <th>Importe {{ $tipoVista == 'mensual' ? 'Mensual' : 'Trimestral' }}</th>
                            <th>Porcentaje</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fuentesIngresos as $fuente => $valor)
                            @php
                                $porcentaje = ($totalIngresos > 0) ? ($valor / $totalIngresos) * 100 : 0;
                                // Usar variación promedio del controlador si está definida, o un valor fijo para consistencia
                                $variacion = isset($variacionPromedio) ? $variacionPromedio : 14;
                                $proyeccion = $valor * (1 + ($variacion / 100));
                                // Estados más consistentes basados en valores reales
                                $estado = 'success';
                            @endphp
                            <tr>
                                <td>
                                    @if($fuente == 'Ingresos por Alquiler de vehículos')
                                        <i class="fas fa-car me-2 text-primary"></i>
                                    @elseif($fuente == 'Ingresos por Servicios de Taxi')
                                        <i class="fas fa-taxi me-2 text-warning"></i>
                                    @elseif($fuente == 'Reservas premium')
                                        <i class="fas fa-crown me-2 text-warning"></i>
                                    @elseif($fuente == 'Seguros adicionales')
                                        <i class="fas fa-shield-alt me-2 text-success"></i>
                                    @elseif($fuente == 'Servicios de conductor')
                                        <i class="fas fa-user-tie me-2 text-info"></i>
                                    @elseif($fuente == 'Tarifas de cancelación')
                                        <i class="fas fa-calendar-times me-2 text-danger"></i>
                                    @elseif($fuente == 'Programas de fidelización')
                                        <i class="fas fa-award me-2 text-primary"></i>
                                    @else
                                        <i class="fas fa-ellipsis-h me-2 text-muted"></i>
                                    @endif
                                    {{ $fuente }}
                                </td>
                                <td>
                                    <span class="badge bg-success text-white px-2 py-1 me-1"><i class="fas fa-level-up-alt"></i> INGRESO</span>
                                    <strong>{{ number_format($valor, 2, ',', '.') }} €</strong>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="me-2">{{ number_format($porcentaje, 1) }}%</div>
                                        <div class="progress" style="height: 10px; width: 100px;">
                                            <div class="progress-bar bg-success" role="progressbar" 
                                                style="width: {{ $porcentaje }}%" 
                                                aria-valuenow="{{ $porcentaje }}" 
                                                aria-valuemin="0" aria-valuemax="100">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th>Total</th>
                            <th>
                                <span class="badge bg-success text-white px-2 py-1 me-1"><i class="fas fa-level-up-alt"></i> TOTAL INGRESO</span>
                                <strong>{{ number_format($totalIngresos, 2, ',', '.') }} €</strong>
                            </th>
                            <th>100%</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Se han eliminado las secciones de "Proyecciones de Ingresos" y "Comparativa de Rendimiento" según lo solicitado -->
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para los gráficos (mantenemos solo los necesarios)
        const fuentes = {!! json_encode(array_keys($fuentesIngresos)) !!};
        const valores = {!! json_encode(array_values($fuentesIngresos)) !!};
        const periodos = {!! json_encode($periodos) !!};
        const datosEvolucion = {!! json_encode($datosEvolucion) !!};
        
        // Configuración del gráfico circular
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: fuentes,
                datasets: [{
                    data: valores,
                    backgroundColor: fuentes.map((_, index) => `hsl(${120 + index * 30}, 70%, 50%)`),
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
        
        // Se ha eliminado la configuración del gráfico de evolución de ingresos
        
        // Se han eliminado los gráficos de proyecciones y comparativa
    });
</script>
@endsection
