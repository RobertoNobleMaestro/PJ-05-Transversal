@extends('layouts.admin_financiero')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Proyecciones Financieras</h1>
            <p class="text-muted">Sede: {{ $sede->nombre }} - Proyección a 12 meses</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('financial.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="{{ route('financial.vehiculos') }}" class="btn btn-primary">
                <i class="fas fa-car"></i> Rentabilidad por Vehículo
            </a>
        </div>
    </div>

    <!-- Gráficos de Proyección -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Proyección Financiera a 12 Meses</h5>
                    <div>
                        <span class="badge bg-primary me-2">Datos históricos</span>
                        <span class="badge bg-success">Proyección</span>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="proyeccionChart" height="300"></canvas>
                </div>
                <div class="card-footer bg-light">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Ingresos Proyectados</h6>
                            <h4 class="text-success">{{ number_format($totalIngresosProyectados, 2, ',', '.') }} €</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Gastos Proyectados</h6>
                            <h4 class="text-danger">{{ number_format($totalGastosProyectados, 2, ',', '.') }} €</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Beneficio Proyectado</h6>
                            <h4 class="{{ $totalBeneficioProyectado >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($totalBeneficioProyectado, 2, ',', '.') }} €
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Proyección -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Proyección de Resultados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Métrica</th>
                                    <th class="text-end">Valor</th>
                                    <th class="text-end">% Cambio</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Ingresos Anuales</td>
                                    <td class="text-end">{{ number_format($totalIngresosProyectados, 2, ',', '.') }} €</td>
                                    <td class="text-end text-success">+15.2%</td>
                                </tr>
                                <tr>
                                    <td>Gastos Operativos</td>
                                    <td class="text-end">{{ number_format($totalGastosProyectados, 2, ',', '.') }} €</td>
                                    <td class="text-end text-danger">+6.4%</td>
                                </tr>
                                <tr>
                                    <td>Beneficio Neto</td>
                                    <td class="text-end">{{ number_format($totalBeneficioProyectado, 2, ',', '.') }} €</td>
                                    <td class="text-end text-success">+20.5%</td>
                                </tr>
                                <tr>
                                    <td>Margen de Beneficio</td>
                                    <td class="text-end">{{ number_format(($totalBeneficioProyectado / $totalIngresosProyectados) * 100, 1) }}%</td>
                                    <td class="text-end text-success">+4.2%</td>
                                </tr>
                                <tr>
                                    <td>ROI</td>
                                    <td class="text-end">{{ number_format($roiProyectado, 1) }}%</td>
                                    <td class="text-end text-success">+3.8%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Análisis de Tendencias</h5>
                </div>
                <div class="card-body">
                    <canvas id="tendenciasChart" height="250"></canvas>
                </div>
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Tendencia Ingresos:</strong> <span class="text-success">Crecimiento sostenido</span></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0"><strong>Previsión Gastos:</strong> <span class="text-warning">Incremento controlado</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Escenarios y Estrategias -->
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Escenario Optimista</h5>
                </div>
                <div class="card-body">
                    <h6>Crecimiento de ingresos: +20%</h6>
                    <p>Asumiendo un incremento en la ocupación de vehículos del 15% y un aumento de tarifas del 5%.</p>
                    
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Ingresos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalIngresosProyectados * 1.2, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Gastos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalGastosProyectados * 1.1, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Beneficio:</strong></div>
                            <div class="col-6 text-end text-success">{{ number_format(($totalIngresosProyectados * 1.2) - ($totalGastosProyectados * 1.1), 2, ',', '.') }} €</div>
                        </div>
                    </div>
                    
                    <h6>Estrategias recomendadas:</h6>
                    <ul>
                        <li>Ampliar campañas de marketing digital</li>
                        <li>Introducir nuevos modelos premium</li>
                        <li>Expandir servicios adicionales rentables</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Escenario Conservador</h5>
                </div>
                <div class="card-body">
                    <h6>Crecimiento de ingresos: +5%</h6>
                    <p>Asumiendo estabilidad en la ocupación con un ligero incremento en tarifas del 3-5%.</p>
                    
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Ingresos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalIngresosProyectados * 1.05, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Gastos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalGastosProyectados * 1.05, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Beneficio:</strong></div>
                            <div class="col-6 text-end text-success">{{ number_format(($totalIngresosProyectados * 1.05) - ($totalGastosProyectados * 1.05), 2, ',', '.') }} €</div>
                        </div>
                    </div>
                    
                    <h6>Estrategias recomendadas:</h6>
                    <ul>
                        <li>Optimización de costes operativos</li>
                        <li>Mantener promociones actuales</li>
                        <li>Programa de fidelización de clientes</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Escenario Pesimista</h5>
                </div>
                <div class="card-body">
                    <h6>Decrecimiento de ingresos: -10%</h6>
                    <p>Asumiendo una caída en la demanda del 10% debido a factores externos (crisis económica, competencia).</p>
                    
                    <div class="border rounded p-3 mb-3 bg-light">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Ingresos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalIngresosProyectados * 0.9, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Gastos:</strong></div>
                            <div class="col-6 text-end">{{ number_format($totalGastosProyectados, 2, ',', '.') }} €</div>
                        </div>
                        <div class="row">
                            <div class="col-6"><strong>Beneficio:</strong></div>
                            <div class="col-6 text-end {{ (($totalIngresosProyectados * 0.9) - $totalGastosProyectados) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(($totalIngresosProyectados * 0.9) - $totalGastosProyectados, 2, ',', '.') }} €
                            </div>
                        </div>
                    </div>
                    
                    <h6>Estrategias recomendadas:</h6>
                    <ul>
                        <li>Reducción de flota no rentable</li>
                        <li>Reestructuración de personal</li>
                        <li>Estrategias agresivas de descuentos</li>
                        <li>Renegociación con proveedores</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos históricos
        const mesesHistoricos = {!! $mesesHistoricos !!};
        const ingresosHistoricos = {!! $ingresosHistoricos !!};
        const tendenciaHistorica = {!! $tendenciaHistorica !!};
        
        // Datos proyectados
        const mesesFuturos = {!! $mesesFuturos !!};
        const proyeccionIngresos = {!! $proyeccionIngresos !!};
        const proyeccionGastos = {!! $proyeccionGastos !!};
        const proyeccionBeneficio = {!! $proyeccionBeneficio !!};
        
        // Combinar datos históricos y proyectados para el gráfico principal
        const todosLosMeses = [...mesesHistoricos, ...mesesFuturos];
        
        // Preparar datasets con datos históricos y proyecciones
        const datasetsIngresos = {
            label: 'Ingresos',
            data: [...ingresosHistoricos, ...Array(mesesFuturos.length).fill(null)],
            borderColor: 'rgb(54, 162, 235)',
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderWidth: 2,
            type: 'bar'
        };
        
        const datasetsTendencia = {
            label: 'Tendencia',
            data: [...tendenciaHistorica, ...Array(mesesFuturos.length).fill(null)],
            borderColor: 'rgb(153, 102, 255)',
            borderWidth: 2,
            borderDash: [5, 5],
            fill: false,
            type: 'line'
        };
        
        const datasetsProyeccion = {
            label: 'Proyección Ingresos',
            data: [...Array(mesesHistoricos.length).fill(null), ...proyeccionIngresos],
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.5)',
            borderWidth: 2,
            type: 'bar'
        };
        
        const datasetsGastos = {
            label: 'Gastos Proyectados',
            data: [...Array(mesesHistoricos.length).fill(null), ...proyeccionGastos],
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.5)',
            borderWidth: 2,
            type: 'line'
        };
        
        const datasetsBeneficio = {
            label: 'Beneficio Proyectado',
            data: [...Array(mesesHistoricos.length).fill(null), ...proyeccionBeneficio],
            borderColor: 'rgb(255, 159, 64)',
            backgroundColor: 'rgba(255, 159, 64, 0.5)',
            borderWidth: 2,
            type: 'line'
        };
        
        new Chart(document.getElementById('proyeccionChart'), {
            type: 'bar',
            data: {
                labels: todosLosMeses,
                datasets: [datasetsIngresos, datasetsTendencia, datasetsProyeccion, datasetsGastos, datasetsBeneficio]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                if (value !== null) {
                                    return label + ': ' + new Intl.NumberFormat('es-ES', { 
                                        style: 'currency', 
                                        currency: 'EUR' 
                                    }).format(value);
                                } else {
                                    return label + ': sin datos';
                                }
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Euros (€)'
                        }
                    }
                }
            }
        });
        
        // Gráfico de análisis de tendencias (proyección futura)
        new Chart(document.getElementById('tendenciasChart'), {
            type: 'line',
            data: {
                labels: mesesFuturos,
                datasets: [
                    {
                        label: 'Ingresos Proyectados',
                        data: proyeccionIngresos,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Gastos Proyectados',
                        data: proyeccionGastos,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        fill: true
                    },
                    {
                        label: 'Beneficio',
                        data: proyeccionBeneficio,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        borderWidth: 3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                let value = context.raw || 0;
                                return label + ': ' + new Intl.NumberFormat('es-ES', { 
                                    style: 'currency', 
                                    currency: 'EUR' 
                                }).format(value);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Euros (€)'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
