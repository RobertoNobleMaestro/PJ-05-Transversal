@extends('layouts.admin_financiero')

@section('styles')
<style>
    .balance-container {
        padding: 20px;
    }
    
    .balance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .stat-card {
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
    }
    
    .chart-container {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .chart-title {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .data-table {
        width: 100%;
        margin-top: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    
    .data-table th {
        background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
        color: white;
    }
    
    .positive {
        color: var(--success-color);
        font-weight: bold;
    }
    
    .negative {
        color: var(--danger-color);
        font-weight: bold;
    }
</style>
@endsection

@section('content')
<div class="balance-container">
    <div class="balance-header">
        <h1>Gastos e Ingresos - {{ $sede->nombre }}</h1>
        <div class="text-end">
            <span class="badge bg-primary">{{ $periodoTipo == 'anual' ? 'Año ' . $añoSeleccionado : ($periodoTipo == 'trimestral' && $periodoValor ? $trimestres[$periodoValor] . ' ' . $añoSeleccionado : ($periodoTipo == 'mensual' && $periodoValor ? $meses[$periodoValor] . ' ' . $añoSeleccionado : 'Año ' . $añoSeleccionado)) }}</span>
        </div>
    </div>
    
    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form id="filtroForm" action="{{ route('financial.gastos-ingresos') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="periodo" class="form-label">Tipo de período</label>
                    <select id="periodo" name="periodo" class="form-select filter-control">
                        @foreach($periodos as $key => $value)
                            <option value="{{ $key }}" {{ $periodoTipo == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4" id="valorMensualContainer" style="{{ $periodoTipo != 'mensual' ? 'display:none' : '' }}">
                    <label for="valorMensual" class="form-label">Mes</label>
                    <select id="valorMensual" name="valor" class="form-select filter-control">
                        <option value="">Todos los meses</option>
                        @foreach($meses as $key => $mes)
                            <option value="{{ $key }}" {{ $periodoTipo == 'mensual' && $periodoValor == $key ? 'selected' : '' }}>{{ $mes }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4" id="valorTrimestralContainer" style="{{ $periodoTipo != 'trimestral' ? 'display:none' : '' }}">
                    <label for="valorTrimestral" class="form-label">Trimestre</label>
                    <select id="valorTrimestral" name="valor" class="form-select filter-control">
                        <option value="">Todos los trimestres</option>
                        @foreach($trimestres as $key => $trimestre)
                            <option value="{{ $key }}" {{ $periodoTipo == 'trimestral' && $periodoValor == $key ? 'selected' : '' }}>{{ $trimestre }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label for="año" class="form-label">Año</label>
                    <select id="año" name="año" class="form-select filter-control">
                        @foreach($años as $key => $value)
                            <option value="{{ $key }}" {{ $añoSeleccionado == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-12">
                    <a href="{{ route('financial.gastos-ingresos') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Ingresos Totales</h5>
                    <p class="stat-value">{{ number_format($totalIngresos, 2) }} €</p>
                    <p class="mb-0">Acumulado {{ now()->year }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Gastos Totales</h5>
                    <p class="stat-value">{{ number_format($totalGastos, 2) }} €</p>
                    <p class="mb-0">Acumulado {{ now()->year }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white {{ $totalBeneficios >= 0 ? 'bg-success' : 'bg-danger' }}">
                <div class="card-body">
                    <h5 class="card-title">Beneficios</h5>
                    <p class="stat-value">{{ number_format($totalBeneficios, 2) }} €</p>
                    <p class="mb-0">Acumulado {{ now()->year }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white {{ $rentabilidad >= 0 ? 'bg-info' : 'bg-danger' }}">
                <div class="card-body">
                    <h5 class="card-title">Rentabilidad</h5>
                    <p class="stat-value">{{ number_format($rentabilidad, 2) }}%</p>
                    <p class="mb-0">ROI (Retorno de inversión)</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="chart-container">
                <h3 class="chart-title">Evolución de Ingresos y Gastos ({{ now()->year }})</h3>
                <canvas id="balanceChart" height="300"></canvas>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Beneficios Mensuales ({{ now()->year }})</h3>
                <canvas id="beneficiosChart" height="250"></canvas>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="chart-container">
                <h3 class="chart-title">Distribución Ingresos vs Gastos</h3>
                <canvas id="distributionChart" height="250"></canvas>
            </div>
            
            <div class="chart-container">
                <h3 class="chart-title">Resumen Anual</h3>
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>Total Ingresos:</td>
                            <td class="text-end">{{ number_format($totalIngresos, 2) }} €</td>
                        </tr>
                        <tr>
                            <td>Total Gastos:</td>
                            <td class="text-end">{{ number_format($totalGastos, 2) }} €</td>
                        </tr>
                        <tr>
                            <td><strong>Beneficio Neto:</strong></td>
                            <td class="text-end {{ $totalBeneficios >= 0 ? 'positive' : 'negative' }}">
                                {{ number_format($totalBeneficios, 2) }} €
                            </td>
                        </tr>
                        <tr>
                            <td>ROI:</td>
                            <td class="text-end {{ $rentabilidad >= 0 ? 'positive' : 'negative' }}">
                                {{ number_format($rentabilidad, 2) }}%
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card data-table">
                <div class="card-header">
                    <h3 class="mb-0">Detalle Mensual ({{ now()->year }})</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th class="text-end">Ingresos</th>
                                    <th class="text-end">Gastos</th>
                                    <th class="text-end">Beneficio</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Parseamos los datos JSON para la tabla
                                    $etiquetasArray = json_decode($etiquetas);
                                    $ingresosArray = json_decode($ingresos);
                                    $gastosArray = json_decode($gastos);
                                    $beneficiosArray = json_decode($beneficios);
                                @endphp
                                
                                @for ($i = 0; $i < count($etiquetasArray); $i++)
                                    <tr>
                                        <td>{{ ucfirst($etiquetasArray[$i]) }}</td>
                                        <td class="text-end">{{ number_format($ingresosArray[$i], 2) }} €</td>
                                        <td class="text-end">{{ number_format($gastosArray[$i], 2) }} €</td>
                                        <td class="text-end {{ $beneficiosArray[$i] >= 0 ? 'positive' : 'negative' }}">
                                            {{ number_format($beneficiosArray[$i], 2) }} €
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ number_format($totalIngresos, 2) }} €</th>
                                    <th class="text-end">{{ number_format($totalGastos, 2) }} €</th>
                                    <th class="text-end {{ $totalBeneficios >= 0 ? 'positive' : 'negative' }}">
                                        {{ number_format($totalBeneficios, 2) }} €
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Datos para los gráficos
        const etiquetas = {!! $etiquetas !!};
        const ingresos = {!! $ingresos !!};
        const gastos = {!! $gastos !!};
        const beneficios = {!! $beneficios !!};
        
        // Gráfico de líneas: Ingresos y Gastos
        const balanceCtx = document.getElementById('balanceChart').getContext('2d');
        new Chart(balanceCtx, {
            type: 'line',
            data: {
                labels: etiquetas.map(etiqueta => etiqueta.charAt(0).toUpperCase() + etiqueta.slice(1)),
                datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        borderColor: '#9F17BD',
                        backgroundColor: 'rgba(159, 23, 189, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Gastos',
                        data: gastos,
                        borderColor: '#FFC107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        tension: 0.3,
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
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
                            }
                        }
                    }
                }
            }
        });
        
        // Gráfico de barras: Beneficios
        const beneficiosCtx = document.getElementById('beneficiosChart').getContext('2d');
        new Chart(beneficiosCtx, {
            type: 'bar',
            data: {
                labels: etiquetas.map(etiqueta => etiqueta.charAt(0).toUpperCase() + etiqueta.slice(1)),
                datasets: [{
                    label: 'Beneficios',
                    data: beneficios,
                    backgroundColor: beneficios.map(value => value >= 0 ? 'rgba(40, 167, 69, 0.7)' : 'rgba(220, 53, 69, 0.7)'),
                    borderColor: beneficios.map(value => value >= 0 ? 'rgb(40, 167, 69)' : 'rgb(220, 53, 69)'),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
                            }
                        }
                    }
                }
            }
        });
        
        // Gráfico circular: Distribución de Ingresos vs Gastos
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: ['Ingresos', 'Gastos'],
                datasets: [{
                    data: [{{ $totalIngresos }}, {{ $totalGastos }}],
                    backgroundColor: [
                        'rgba(159, 23, 189, 0.7)',
                        'rgba(255, 193, 7, 0.7)',
                    ],
                    borderColor: [
                        'rgb(159, 23, 189)',
                        'rgb(255, 193, 7)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.raw !== null) {
                                    label += new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(context.raw);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
    
    // Manejar los filtros dinámicos
    document.getElementById('periodo').addEventListener('change', function() {
        const periodoSeleccionado = this.value;
        const containerMensual = document.getElementById('valorMensualContainer');
        const containerTrimestral = document.getElementById('valorTrimestralContainer');
        
        // Ocultar/mostrar selectores según el período
        if (periodoSeleccionado === 'mensual') {
            containerMensual.style.display = 'block';
            containerTrimestral.style.display = 'none';
            document.getElementById('valorTrimestral').value = ''; // Limpiar valor trimestral
        } else if (periodoSeleccionado === 'trimestral') {
            containerMensual.style.display = 'none';
            containerTrimestral.style.display = 'block';
            document.getElementById('valorMensual').value = ''; // Limpiar valor mensual
        } else {
            containerMensual.style.display = 'none';
            containerTrimestral.style.display = 'none';
            document.getElementById('valorMensual').value = '';
            document.getElementById('valorTrimestral').value = '';
        }
        
        // Enviar formulario automáticamente
        document.getElementById('filtroForm').submit();
    });
    
    // Para filtros automáticos en todos los controles con clase filter-control
    document.querySelectorAll('.filter-control').forEach(function(element) {
        if (element.id !== 'periodo') { // Ya manejamos el periodo arriba
            element.addEventListener('change', function() {
                document.getElementById('filtroForm').submit();
            });
        }
    });
</script>
@endsection
