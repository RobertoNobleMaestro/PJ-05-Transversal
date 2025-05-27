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
        border-radius: 12px;
        overflow: hidden;
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
    
    .budget-indicator {
        text-align: center;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 10px;
        border: 2px solid;
    }
    
    .budget-indicator h2 {
        margin-bottom: 10px;
        font-weight: 700;
    }
    
    .zone-red {
        background-color: rgba(220, 53, 69, 0.1);
        border-color: #dc3545;
    }
    
    .zone-yellow {
        background-color: rgba(255, 193, 7, 0.1);
        border-color: #ffc107;
    }
    
    .zone-green {
        background-color: rgba(40, 167, 69, 0.1);
        border-color: #28a745;
    }
    
    .recommendation-container {
        background-color: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        margin-bottom: 30px;
    }
    
    .recommendation-title {
        font-size: 1.2rem;
        margin-bottom: 20px;
        color: var(--primary-color);
        font-weight: 600;
    }
    
    .recommendation-item {
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 10px;
        background-color: #f8f9fa;
        border-left: 4px solid var(--primary-color);
    }
    
    .expense-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }
    
    .expense-item:last-child {
        border-bottom: none;
    }
    
    .expense-name {
        font-weight: 500;
    }
    
    .expense-value {
        font-weight: 700;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
</style>
@endsection

@section('content')
<div class="balance-container">
    <div class="balance-header">
        <h1>Estimación de Presupuesto - {{ $sede->nombre }}</h1>
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
            <form id="filtroForm" action="{{ route('financial.presupuesto') }}" method="GET" class="row g-3">
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
                    <a href="{{ route('financial.presupuesto') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Indicador de Zona Financiera -->
    <div class="budget-indicator zone-{{ $colorZona }}">
        <h2>{{ $zonaFinanciera }}</h2>
        <p>{{ $descripcionZona }}</p>
        <div class="progress mb-3" style="height: 25px;">
            @if($margen < 0)
                <!-- Zona Roja -->
                <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    Margen: {{ number_format($margen, 2) }}%
                </div>
            @elseif($margen >= 0 && $margen < 30)
                <!-- Zona Amarilla -->
                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $margen * 100 / 30) }}%" aria-valuenow="{{ min(100, $margen * 100 / 30) }}" aria-valuemin="0" aria-valuemax="100">
                    Margen: {{ number_format($margen, 2) }}%
                </div>
            @else
                <!-- Zona Verde -->
                <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">
                    Margen: {{ number_format($margen, 2) }}%
                </div>
            @endif
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Ingresos Actuales</h5>
                    <p class="stat-value">{{ number_format($ingresosTotales, 2) }} €</p>
                    <p class="mb-0">{{ $periodoTipo == 'anual' ? 'Anual' : ($periodoTipo == 'trimestral' ? 'Trimestral' : 'Mensual') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Gastos Estimados</h5>
                    <p class="stat-value">{{ number_format($gastosTotales, 2) }} €</p>
                    <p class="mb-0">{{ $periodoTipo == 'anual' ? 'Anual' : ($periodoTipo == 'trimestral' ? 'Trimestral' : 'Mensual') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white {{ $margen >= 0 ? 'bg-success' : 'bg-danger' }}">
                <div class="card-body">
                    <h5 class="card-title">Margen</h5>
                    <p class="stat-value">{{ number_format($margen, 2) }}%</p>
                    <p class="mb-0">Respecto a los gastos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Presupuesto Objetivo</h5>
                    <p class="stat-value">{{ number_format($presupuestoObjetivo, 2) }} €</p>
                    <p class="mb-0">Para zona verde (>60%)</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="chart-container">
                <h3 class="chart-title">Distribución de Gastos</h3>
                <canvas id="expensesChart" height="300"></canvas>
            </div>
            

        </div>
        
        <div class="col-md-4">

            
            <div class="chart-container">
                <h3 class="chart-title">Detalle de Gastos</h3>
                <div class="expense-details">
                    <div class="expense-item">
                        <span class="expense-name">Salarios ({{ $numEmpleados }} empleados)</span>
                        <span class="expense-value">{{ number_format($gastosSalarios, 2) }} €</span>
                    </div>
                    <div class="expense-item">
                        <span class="expense-name">Materiales ({{ $numVehiculos }} vehículos)</span>
                        <span class="expense-value">{{ number_format($gastosMateriales, 2) }} €</span>
                    </div>
                    <div class="expense-item">
                        <span class="expense-name">Mantenimiento ({{ $numParkings }} parkings)</span>
                        <span class="expense-value">{{ number_format($gastosParkings, 2) }} €</span>
                    </div>
                    <div class="expense-item">
                        <span class="expense-name">Otros gastos operativos</span>
                        <span class="expense-value">{{ number_format($otrosGastos, 2) }} €</span>
                    </div>
                    <div class="expense-item">
                        <span class="expense-name fw-bold">Total Gastos</span>
                        <span class="expense-value fw-bold">{{ number_format($gastosTotales, 2) }} €</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if($periodoTipo === 'anual')
    <div class="row">
        <div class="col-12">
            <div class="card data-table">
                <div class="card-header">
                    <h3 class="mb-0">Detalle Mensual de Gastos ({{ $añoSeleccionado }})</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Mes</th>
                                    <th>Salarios</th>
                                    <th>Materiales</th>
                                    <th>Mantenimiento</th>
                                    <th>Otros</th>
                                    <th>Total Gastos</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gastosMensuales as $gasto)
                                <tr>
                                    <td>{{ $gasto['mes'] }}</td>
                                    <td>{{ number_format($gasto['salarios'], 2) }} €</td>
                                    <td>{{ number_format($gasto['materiales'], 2) }} €</td>
                                    <td>{{ number_format($gasto['mantenimiento'], 2) }} €</td>
                                    <td>{{ number_format($gasto['otros'], 2) }} €</td>
                                    <td class="fw-bold">{{ number_format($gasto['total'], 2) }} €</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Datos para los gráficos
        const categorias = {!! $categorias !!};
        const valores = {!! $valores !!};
        
        // Colores para las categorías
        const backgroundColors = [
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 159, 64, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(75, 192, 192, 0.7)'
        ];
        
        // Gráfico de distribución de gastos (pie chart)
        const expensesCtx = document.getElementById('expensesChart').getContext('2d');
        new Chart(expensesCtx, {
            type: 'pie',
            data: {
                labels: categorias,
                datasets: [{
                    data: valores,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
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
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });
        

        

        
        // Manejar los filtros dinámicos
        document.getElementById('periodo').addEventListener('change', function() {
            const periodoSeleccionado = this.value;
            
            // Mostrar u ocultar los contenedores según el periodo seleccionado
            if (periodoSeleccionado === 'mensual') {
                document.getElementById('valorMensualContainer').style.display = 'block';
                document.getElementById('valorTrimestralContainer').style.display = 'none';
            } else if (periodoSeleccionado === 'trimestral') {
                document.getElementById('valorMensualContainer').style.display = 'none';
                document.getElementById('valorTrimestralContainer').style.display = 'block';
            } else {
                document.getElementById('valorMensualContainer').style.display = 'none';
                document.getElementById('valorTrimestralContainer').style.display = 'none';
            }
        });
        
        // Aplicar filtros automáticamente al cambiar
        const filterControls = document.querySelectorAll('.filter-control');
        filterControls.forEach(function(control) {
            control.addEventListener('change', function() {
                document.getElementById('filtroForm').submit();
            });
        });
    });
</script>
@endsection
