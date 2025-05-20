@extends('layouts.admin_financiero')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Dashboard Financiero Avanzado - {{ $sede->nombre }}</h1>
            <p class="text-muted">Análisis financiero completo de la sede</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('financial.vehiculos') }}" class="btn btn-primary me-2">
                <i class="fas fa-car"></i> Rentabilidad por Vehículo
            </a>
            <a href="{{ route('financial.proyecciones') }}" class="btn btn-info">
                <i class="fas fa-chart-line"></i> Proyecciones
            </a>
        </div>
    </div>

    <!-- Resumen General KPIs -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Gastos de Personal</h5>
                    <p class="display-4">{{ number_format($totalGastosPersonal, 2, ',', '.') }} €</p>
                    <p class="mb-0">Anual: {{ number_format($proyeccionAnual, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Ingresos</h5>
                    <p class="display-4">{{ number_format($totalIngresos, 2, ',', '.') }} €</p>
                    <p class="mb-0">Últimos 6 meses</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">Beneficio Operativo</h5>
                    <p class="display-4">{{ number_format($beneficio, 2, ',', '.') }} €</p>
                    <p class="mb-0">Margen: {{ number_format($margen, 1) }}%</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body text-center">
                    <h5 class="card-title">ROI</h5>
                    <p class="display-4">{{ number_format($roi, 1) }}%</p>
                    <p class="mb-0">Retorno sobre inversión</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribución de Gastos y Analíticas -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gastos de Personal por Rol</h5>
                    <span class="badge bg-primary">Mensual</span>
                </div>
                <div class="card-body">
                    <canvas id="gastosPersonalChart" height="300"></canvas>
                </div>
                <div class="card-footer bg-light">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Salario Promedio</th>
                                    <th class="text-end">Total Mensual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estadisticasPersonal as $stat)
                                    <tr>
                                        <td>{{ $stat->nombre_rol }}</td>
                                        <td class="text-center">{{ $stat->cantidad }}</td>
                                        <td class="text-center">
                                            {{ number_format($stat->total_salarios / $stat->cantidad, 2, ',', '.') }} €
                                        </td>
                                        <td class="text-end">{{ number_format($stat->total_salarios, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Evolución de Ingresos</h5>
                    <span class="badge bg-primary">Últimos 6 meses</span>
                </div>
                <div class="card-body">
                    <canvas id="ingresosChart" height="300"></canvas>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ingresos Totales</h6>
                            <h4>{{ number_format($totalIngresos, 2, ',', '.') }} €</h4>
                        </div>
                        <div class="col-md-6 text-end">
                            <h6>Proyección Anual</h6>
                            <h4>{{ number_format($proyeccionIngresos, 2, ',', '.') }} €</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Análisis Detallado -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Días más Rentables</h5>
                </div>
                <div class="card-body">
                    <canvas id="diasRentablesChart" height="250"></canvas>
                </div>
                <div class="card-footer bg-light p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Día</th>
                                    <th class="text-center">Reservas</th>
                                    <th class="text-end">Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($diasMasRentables as $dia)
                                    <tr>
                                        <td>{{ $dia->nombre_dia }}</td>
                                        <td class="text-center">{{ $dia->cantidad }}</td>
                                        <td class="text-end">{{ number_format($dia->ingresos, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Próximos Pagos de Nómina</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">Próximo día de pago</h6>
                                <h4>{{ $proximoPago ? $proximoPago->format('d/m/Y') : 'No programado' }}</h4>
                                <small>En {{ $diasHastaProximoPago }} días</small>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>Día del mes</th>
                                    <th class="text-center">Empleados</th>
                                    <th class="text-end">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagosAsalariados as $pago)
                                    <tr>
                                        <td>Día {{ $pago->dia_cobro }}</td>
                                        <td class="text-center">{{ $pago->cantidad }}</td>
                                        <td class="text-end">{{ number_format($pago->total_salarios, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Proyección Financiera</h5>
                </div>
                <div class="card-body">
                    <h5 class="card-title">Previsión a 12 meses</h5>
                    <div class="mb-3 p-3 border rounded">
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-muted">Ingresos</h6>
                                <h5 class="text-success">{{ number_format($proyeccionIngresos, 2, ',', '.') }} €</h5>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Gastos</h6>
                                <h5 class="text-danger">{{ number_format($proyeccionAnual, 2, ',', '.') }} €</h5>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <h6 class="text-muted">Beneficio</h6>
                                <h5 class="{{ $proyeccionBeneficio >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($proyeccionBeneficio, 2, ',', '.') }} €
                                </h5>
                            </div>
                            <div class="col-6">
                                <h6 class="text-muted">Margen</h6>
                                <h5 class="{{ $proyeccionBeneficio >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format(($proyeccionBeneficio / $proyeccionIngresos) * 100, 1) }}%
                                </h5>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('financial.proyecciones') }}" class="btn btn-outline-primary d-block">
                        <i class="fas fa-chart-line me-2"></i>Ver proyección detallada
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para el gráfico de gastos de personal
        const gastosPorRol = {
            labels: {!! json_encode($estadisticasPersonal->pluck('nombre_rol')->toArray()) !!},
            datasets: [
                {
                    label: 'Gasto Mensual',
                    data: {!! json_encode($estadisticasPersonal->pluck('total_salarios')->toArray()) !!},
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1'
                    ],
                    hoverOffset: 4
                }
            ]
        };
        
        new Chart(document.getElementById('gastosPersonalChart'), {
            type: 'pie',
            data: gastosPorRol,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': ' + new Intl.NumberFormat('es-ES', { 
                                    style: 'currency', 
                                    currency: 'EUR' 
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });

        // Datos para el gráfico de ingresos
        const meses = {!! json_encode($ingresosReservas->pluck('mes_nombre')->toArray()) !!};
        const ingresos = {!! json_encode($ingresosReservas->pluck('ingresos')->toArray()) !!};
        const cantidadReservas = {!! json_encode($ingresosReservas->pluck('cantidad')->toArray()) !!};
        
        new Chart(document.getElementById('ingresosChart'), {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgb(54, 162, 235)',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Número de Reservas',
                        data: cantidadReservas,
                        type: 'line',
                        borderColor: 'rgb(255, 99, 132)',
                        pointBackgroundColor: 'rgb(255, 99, 132)',
                        borderWidth: 2,
                        fill: false,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        type: 'linear',
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Ingresos (€)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Número de Reservas'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });

        // Datos para el gráfico de días más rentables
        const diasRentablesData = {
            labels: {!! json_encode($diasMasRentables->pluck('nombre_dia')->toArray()) !!},
            datasets: [
                {
                    label: 'Ingresos',
                    data: {!! json_encode($diasMasRentables->pluck('ingresos')->toArray()) !!},
                    backgroundColor: [
                        '#1cc88a', '#4e73df', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#5a5c69'
                    ]
                }
            ]
        };
        
        new Chart(document.getElementById('diasRentablesChart'), {
            type: 'doughnut',
            data: diasRentablesData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.raw || 0;
                                return label + ': ' + new Intl.NumberFormat('es-ES', { 
                                    style: 'currency', 
                                    currency: 'EUR' 
                                }).format(value);
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
