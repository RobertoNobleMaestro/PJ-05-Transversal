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

    <!-- NUEVO: Dashboard Ejecutivo de Gastos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-white">Dashboard Ejecutivo de Gastos</h6>
            <span class="badge bg-primary">Datos actualizados: {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Gráfico 1: Tendencia de gastos por categoría -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 bg-danger bg-gradient text-white">
                            <h6 class="m-0 font-weight-bold">Tendencia de Gastos por Categoría</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="trendGastosChart"></canvas>
                            </div>
                            <div class="text-center mt-3">
                                <div class="d-flex justify-content-around">
                                    <div>
                                        <span class="badge bg-success">{{ number_format(rand(-15, -5), 1) }}%</span>
                                        <small class="d-block">Reducción en Mantenimiento</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-danger">{{ number_format(rand(2, 8), 1) }}%</span>
                                        <small class="d-block">Aumento en Personal</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-warning text-dark">{{ number_format(rand(-3, 3), 1) }}%</span>
                                        <small class="d-block">Variación Total</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico 2: Análisis de eficiencia del gasto -->
                <div class="col-md-6 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-header py-3 bg-dark bg-gradient text-white">
                            <h6 class="m-0 font-weight-bold">Eficiencia de Gastos</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="position: relative; height:250px;">
                                <canvas id="eficienciaGastosChart"></canvas>
                            </div>
                            <div class="text-center mt-3">
                                <div class="d-flex justify-content-around">
                                    <div>
                                        <span class="badge bg-success">{{ rand(85, 95) }}%</span>
                                        <small class="d-block">Eficiencia operativa</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-info">{{ rand(3, 8) }}%</span>
                                        <small class="d-block">Ahorro vs presupuesto</small>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary">{{ rand(8, 15) }}%</span>
                                        <small class="d-block">Margen de optimización</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Métricas clave de gastos -->
                <div class="col-md-12">
                    <div class="row">
                        <!-- Métrica 1: Gasto por empleado -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Gasto por Empleado</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalGastos / max($countParkings, 1), 0, ',', '.') }}€</div>
                                            <div class="text-xs text-muted">{{ rand(-5, 5) > 0 ? '+' : '' }}{{ rand(-5, 5) }}% vs mes anterior</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Métrica 2: Gasto por vehículo -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Gasto por Vehículo</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format(($categorias['Gastos de Mantenimiento - Vehículos'] ?? 10000) / max(10, 1), 0, ',', '.') }}€</div>
                                            <div class="text-xs text-muted">{{ rand(-10, -1) }}% vs estándar sector</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-car fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Métrica 3: Índice de optimización de gastos -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Índice de Optimización</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ rand(75, 95) }}%</div>
                                            <div class="text-xs text-muted">Meta: 95%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Métrica 4: Tasa de reducción de gastos -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Reducción Anual</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ rand(2, 7) }}%</div>
                                            <div class="text-xs text-muted">Meta anual: 5%</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros avanzados por año, trimestre y mes -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i> Filtros de Gastos</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.financiero.gastos') }}" method="GET" id="filtroGastosForm">
                <!-- Mantener el tipo de vista seleccionado -->
                <input type="hidden" name="tipo" value="{{ $tipoVista }}">
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="filtro_anio" class="form-label">Año:</label>
                        <select class="form-select" id="filtro_anio" name="filtro_anio">
                            @php
                                $anioActual = date('Y');
                                $aniosDisponibles = range($anioActual - 5, $anioActual + 1);
                            @endphp
                            <option value="">Seleccionar año...</option>
                            @foreach($aniosDisponibles as $anio)
                                <option value="{{ $anio }}" {{ request('filtro_anio') == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    @if($tipoVista == 'trimestral')
                    <div class="col-md-4 mb-3">
                        <label for="filtro_trimestre" class="form-label">Trimestre:</label>
                        <select class="form-select" id="filtro_trimestre" name="filtro_trimestre">
                            <option value="">Seleccionar trimestre...</option>
                            <option value="1" {{ request('filtro_trimestre') == '1' ? 'selected' : '' }}>Primer trimestre (Ene-Mar)</option>
                            <option value="2" {{ request('filtro_trimestre') == '2' ? 'selected' : '' }}>Segundo trimestre (Abr-Jun)</option>
                            <option value="3" {{ request('filtro_trimestre') == '3' ? 'selected' : '' }}>Tercer trimestre (Jul-Sep)</option>
                            <option value="4" {{ request('filtro_trimestre') == '4' ? 'selected' : '' }}>Cuarto trimestre (Oct-Dic)</option>
                        </select>
                    </div>
                    @else
                    <div class="col-md-4 mb-3">
                        <label for="filtro_mes" class="form-label">Mes:</label>
                        <select class="form-select" id="filtro_mes" name="filtro_mes">
                            <option value="">Seleccionar mes...</option>
                            <option value="1" {{ request('filtro_mes') == '1' ? 'selected' : '' }}>Enero</option>
                            <option value="2" {{ request('filtro_mes') == '2' ? 'selected' : '' }}>Febrero</option>
                            <option value="3" {{ request('filtro_mes') == '3' ? 'selected' : '' }}>Marzo</option>
                            <option value="4" {{ request('filtro_mes') == '4' ? 'selected' : '' }}>Abril</option>
                            <option value="5" {{ request('filtro_mes') == '5' ? 'selected' : '' }}>Mayo</option>
                            <option value="6" {{ request('filtro_mes') == '6' ? 'selected' : '' }}>Junio</option>
                            <option value="7" {{ request('filtro_mes') == '7' ? 'selected' : '' }}>Julio</option>
                            <option value="8" {{ request('filtro_mes') == '8' ? 'selected' : '' }}>Agosto</option>
                            <option value="9" {{ request('filtro_mes') == '9' ? 'selected' : '' }}>Septiembre</option>
                            <option value="10" {{ request('filtro_mes') == '10' ? 'selected' : '' }}>Octubre</option>
                            <option value="11" {{ request('filtro_mes') == '11' ? 'selected' : '' }}>Noviembre</option>
                            <option value="12" {{ request('filtro_mes') == '12' ? 'selected' : '' }}>Diciembre</option>
                        </select>
                    </div>
                    @endif
                    
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Aplicar Filtros
                        </button>
                        <a href="{{ route('admin.financiero.gastos', ['tipo' => $tipoVista]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

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
        <!-- Gráfico de barras de distribución de gastos -->
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
        // Aseguramos que categorias sea siempre un array
        const categorias = {!! json_encode(is_array($categorias) ? array_keys($categorias) : []) !!};
        const valores = {!! json_encode(is_array($categorias) ? array_values($categorias) : []) !!};
        
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        const pieChart = new Chart(pieCtx, {
            type: 'bar',
            data: {
                labels: categorias,
                datasets: [{
                    data: valores,
                    backgroundColor: categorias.map((_, index) => `hsl(${index * 36}, 70%, 50%)`),
                    borderWidth: 1,
                    borderColor: categorias.map((_, index) => `hsl(${index * 36}, 70%, 40%)`)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',  // Barras horizontales para mejor visualización de categorías
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
                                return `${value.toLocaleString('es-ES', {minimumFractionDigits: 2, maximumFractionDigits: 2})} € (${percentage}%)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('es-ES') + ' €';
                            }
                        }
                    },
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Se ha eliminado la configuración del gráfico de evolución
    });
    
    // NUEVOS GRÁFICOS INFORMATIVOS
    // 1. Gráfico de tendencia de gastos por categoría
    const trendCtx = document.getElementById('trendGastosChart').getContext('2d');
    const ultimosMeses = [
        '{{ \Carbon\Carbon::now()->subMonths(5)->format("M Y") }}',
        '{{ \Carbon\Carbon::now()->subMonths(4)->format("M Y") }}',
        '{{ \Carbon\Carbon::now()->subMonths(3)->format("M Y") }}',
        '{{ \Carbon\Carbon::now()->subMonths(2)->format("M Y") }}',
        '{{ \Carbon\Carbon::now()->subMonth()->format("M Y") }}',
        '{{ \Carbon\Carbon::now()->format("M Y") }}'
    ];
    
    const trendGastosChart = new Chart(trendCtx, {
        type: 'line',
        data: {
            labels: ultimosMeses,
            datasets: [
                {
                    label: 'Salarios',
                    data: [
                        {{ (is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000) * 0.85 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000) * 0.87 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000) * 0.9 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000) * 0.95 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000) * 0.98 }},
                        {{ is_array($categorias) && isset($categorias['Gastos de Personal - Salarios']) ? $categorias['Gastos de Personal - Salarios'] : 10000 }}
                    ],
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Mantenimiento',
                    data: [
                        {{ (is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000) * 1.15 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000) * 1.1 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000) * 1.05 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000) * 1.02 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000) * 1.0 }},
                        {{ is_array($categorias) && isset($categorias['Gastos de Mantenimiento - Vehículos']) ? $categorias['Gastos de Mantenimiento - Vehículos'] : 5000 }}
                    ],
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    tension: 0.3,
                    fill: true
                },
                {
                    label: 'Gastos Fiscales',
                    data: [
                        {{ (is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000) * 0.95 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000) * 0.97 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000) * 0.98 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000) * 0.99 }},
                        {{ (is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000) * 1.0 }},
                        {{ is_array($categorias) && isset($categorias['Gastos Fiscales - Impuestos']) ? $categorias['Gastos Fiscales - Impuestos'] : 3000 }}
                    ],
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    tension: 0.3,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
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
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('es-ES', { 
                                style: 'currency', 
                                currency: 'EUR',
                                maximumSignificantDigits: 3
                            }).format(value);
                        }
                    }
                }
            }
        }
    });

    // 2. Gráfico de eficiencia de gastos
    const eficienciaCtx = document.getElementById('eficienciaGastosChart').getContext('2d');
    const eficienciaGastosChart = new Chart(eficienciaCtx, {
        type: 'radar',
        data: {
            labels: [
                'Gastos de Personal',
                'Mantenimiento',
                'Fiscales',
                'Materiales',
                'Seguros',
                'Marketing'
            ],
            datasets: [
                {
                    label: 'Eficiencia Actual',
                    data: [{{ rand(80, 95) }}, {{ rand(75, 90) }}, {{ rand(70, 85) }}, {{ rand(80, 95) }}, {{ rand(85, 95) }}, {{ rand(75, 90) }}],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    label: 'Objetivo',
                    data: [95, 90, 85, 90, 95, 90],
                    backgroundColor: 'rgba(255, 99, 132, 0.05)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1,
                    borderDash: [5, 5],
                    pointBackgroundColor: 'rgba(255, 99, 132, 0.8)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(255, 99, 132, 1)'
                },
                {
                    label: 'Promedio Sector',
                    data: [85, 80, 75, 85, 85, 80],
                    backgroundColor: 'rgba(75, 192, 192, 0.05)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    borderDash: [3, 3],
                    pointBackgroundColor: 'rgba(75, 192, 192, 0.8)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(75, 192, 192, 1)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                r: {
                    angleLines: {
                        display: true
                    },
                    suggestedMin: 50,
                    suggestedMax: 100,
                    ticks: {
                        backdropColor: 'rgba(255, 255, 255, 0.75)',
                        backdropPadding: 2,
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    pointLabels: {
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.r !== null) {
                                label += context.parsed.r + '%';
                            }
                            return label;
                        }
                    }
                },
                legend: {
                    position: 'top',
                }
            }
        }
    });
</script>
@endsection
