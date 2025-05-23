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
    
    .balance-section {
        margin-bottom: 30px;
    }
    
    .balance-card {
        height: 100%;
    }
    
    .badge-categoria {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }
    
    .category-header {
        background-color: #f8f9fa;
        padding: 10px 15px;
        border-radius: 5px;
        margin: 15px 0 10px;
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .asset-item, .liability-item {
        border-left: 3px solid transparent;
        padding: 10px 15px;
        margin-bottom: 5px;
        transition: all 0.2s;
    }
    
    .asset-item:hover, .liability-item:hover {
        background-color: #f8f9fa;
    }
    
    .asset-item {
        border-left-color: var(--success-color);
    }
    
    .liability-item {
        border-left-color: var(--danger-color);
    }
    
    .ratio-card {
        border-left: 4px solid var(--primary-color);
    }
</style>
@endsection

@section('content')
<div class="balance-container">
    <div class="balance-header">
        <h1>Balance General - {{ $sede->nombre }}</h1>
        <div class="text-end">
            <span class="badge bg-primary">{{ $periodoTexto }}</span>
        </div>
    </div>
    
    <!-- Filtros automáticos -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form id="filtroForm" action="{{ route('financial.balance') }}" method="GET" class="row g-3">
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
                    <a href="{{ route('financial.balance') }}" class="btn btn-outline-secondary">Limpiar filtros</a>
                </div>
            </form>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Activos Totales</h5>
                    <p class="stat-value">{{ number_format($totalActivos, 2) }} €</p>
                    <p class="mb-0">Bienes y derechos</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Pasivos Totales</h5>
                    <p class="stat-value">{{ number_format($totalPasivos, 2) }} €</p>
                    <p class="mb-0">Obligaciones y deudas</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card text-white {{ $patrimonioNeto >= 0 ? 'bg-primary' : 'bg-danger' }}">
                <div class="card-body">
                    <h5 class="card-title">Patrimonio Neto</h5>
                    <p class="stat-value">{{ number_format($patrimonioNeto, 2) }} €</p>
                    <p class="mb-0">Activos - Pasivos</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-container">
                <h3 class="chart-title">Activos por Categoría</h3>
                <canvas id="activosChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h3 class="chart-title">Pasivos por Categoría</h3>
                <canvas id="pasivosChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="chart-container">
                <h3 class="chart-title">Composición del Balance</h3>
                <canvas id="balanceChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-container">
                <h3 class="chart-title">Ratios Financieros</h3>
                <div class="row mt-4">
                    <div class="col-md-6 mb-3">
                        <div class="card ratio-card">
                            <div class="card-body">
                                <h5 class="card-title">Ratio de Solvencia</h5>
                                <p class="display-6 mb-0 {{ $ratioSolvencia >= 1 ? 'positive' : 'negative' }}">{{ number_format($ratioSolvencia, 2) }}</p>
                                <small class="text-muted">Activos/Pasivos (>1 es positivo)</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card ratio-card">
                            <div class="card-body">
                                <h5 class="card-title">Ratio de Endeudamiento</h5>
                                <p class="display-6 mb-0 {{ $ratioEndeudamiento <= 60 ? 'positive' : 'negative' }}">{{ number_format($ratioEndeudamiento, 2) }}%</p>
                                <small class="text-muted">(Pasivos/Activos)*100 (<60% es positivo)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card data-table">
                <div class="card-header">
                    <h3 class="mb-0">Detalle de Activos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if(count($activos) > 0)
                            @foreach($activos as $categoria => $items)
                                <div class="category-header">
                                    <span class="badge bg-success badge-categoria">{{ $categoria }}</span>
                                    <span class="float-end">{{ number_format($items->sum('valor'), 2) }} €</span>
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Fecha Registro</th>
                                            <th class="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $activo)
                                            <tr class="asset-item">
                                                <td>
                                                    <strong>{{ $activo->nombre }}</strong>
                                                    @if($activo->descripcion)
                                                        <br><small class="text-muted">{{ $activo->descripcion }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $activo->fecha_registro->format('d/m/Y') }}</td>
                                                <td class="text-end">{{ number_format($activo->valor, 2) }} €</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        @else
                            <div class="alert alert-info">No hay activos registrados actualmente.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card data-table">
                <div class="card-header">
                    <h3 class="mb-0">Detalle de Pasivos</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if(count($pasivos) > 0)
                            @foreach($pasivos as $categoria => $items)
                                <div class="category-header">
                                    <span class="badge bg-danger badge-categoria">{{ $categoria }}</span>
                                    <span class="float-end">{{ number_format($items->sum('valor'), 2) }} €</span>
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nombre</th>
                                            <th>Vencimiento</th>
                                            <th class="text-end">Valor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $pasivo)
                                            <tr class="liability-item">
                                                <td>
                                                    <strong>{{ $pasivo->nombre }}</strong>
                                                    @if($pasivo->descripcion)
                                                        <br><small class="text-muted">{{ $pasivo->descripcion }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($pasivo->fecha_vencimiento)
                                                        {{ $pasivo->fecha_vencimiento->format('d/m/Y') }}
                                                    @else
                                                        <span class="text-muted">No aplica</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">{{ number_format($pasivo->valor, 2) }} €</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endforeach
                        @else
                            <div class="alert alert-info">No hay pasivos registrados actualmente.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Resumen del Balance</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    <th class="text-end">Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Total Activos</strong></td>
                                    <td class="text-end positive">{{ number_format($totalActivos, 2) }} €</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Pasivos</strong></td>
                                    <td class="text-end negative">{{ number_format($totalPasivos, 2) }} €</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>PATRIMONIO NETO</strong></td>
                                    <td class="text-end {{ $patrimonioNeto >= 0 ? 'positive' : 'negative' }}">
                                        <strong>{{ number_format($patrimonioNeto, 2) }} €</strong>
                                    </td>
                                </tr>
                            </tbody>
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
        const categoriasActivos = {!! $categoriasActivos !!};
        const valoresActivos = {!! $valoresActivos !!};
        const categoriasPasivos = {!! $categoriasPasivos !!};
        const valoresPasivos = {!! $valoresPasivos !!};
        
        // Colores para los gráficos
        const coloresActivos = [
            'rgba(40, 167, 69, 0.8)',
            'rgba(50, 177, 79, 0.8)',
            'rgba(60, 187, 89, 0.8)',
            'rgba(70, 197, 99, 0.8)',
            'rgba(80, 207, 109, 0.8)',
            'rgba(90, 217, 119, 0.8)',
        ];
        
        const coloresPasivos = [
            'rgba(220, 53, 69, 0.8)',
            'rgba(230, 63, 79, 0.8)',
            'rgba(240, 73, 89, 0.8)',
            'rgba(250, 83, 99, 0.8)',
            'rgba(255, 93, 109, 0.8)',
            'rgba(255, 103, 119, 0.8)',
        ];
        
        // Gráfico de activos por categoría
        if (categoriasActivos && categoriasActivos.length > 0) {
            const activosCtx = document.getElementById('activosChart').getContext('2d');
            new Chart(activosCtx, {
                type: 'doughnut',
                data: {
                    labels: categoriasActivos,
                    datasets: [{
                        data: valoresActivos,
                        backgroundColor: coloresActivos,
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
        }
        
        // Gráfico de pasivos por categoría
        if (categoriasPasivos && categoriasPasivos.length > 0) {
            const pasivosCtx = document.getElementById('pasivosChart').getContext('2d');
            new Chart(pasivosCtx, {
                type: 'doughnut',
                data: {
                    labels: categoriasPasivos,
                    datasets: [{
                        data: valoresPasivos,
                        backgroundColor: coloresPasivos,
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
        }
        
        // Gráfico de composición del balance
        const balanceCtx = document.getElementById('balanceChart').getContext('2d');
        new Chart(balanceCtx, {
            type: 'bar',
            data: {
                labels: ['Activos', 'Pasivos', 'Patrimonio Neto'],
                datasets: [{
                    label: 'Valor en euros',
                    data: [{{ $totalActivos }}, {{ $totalPasivos }}, {{ $patrimonioNeto }}],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.7)',
                        'rgba(220, 53, 69, 0.7)',
                        'rgba(159, 23, 189, 0.7)',
                    ],
                    borderColor: [
                        'rgb(40, 167, 69)',
                        'rgb(220, 53, 69)',
                        'rgb(159, 23, 189)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
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
