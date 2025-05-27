@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Balance de Activos</h1>
            <p class="text-muted">Activos fijos según esquema contable estudiantil</p>
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

    <!-- Filtros para buscar un parking o vehículo específico -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.financiero.balance.activos') }}" method="GET" id="filtroActivosForm">
                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label for="filtro_vehiculo" class="form-label">Buscar Vehículo:</label>
                        <div class="input-group">
                            <input type="text" class="form-control filter-input" id="filtro_vehiculo" name="filtro_vehiculo" 
                                placeholder="Marca, modelo..." value="{{ request('filtro_vehiculo') }}">
                            <button type="button" class="btn btn-outline-secondary limpiar-filtro" data-target="filtro_vehiculo">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="filtro_ano_fabricacion" class="form-label">Año de fabricación:</label>
                        <select class="form-select filter-input" id="filtro_ano_fabricacion" name="filtro_ano_fabricacion">
                            <option value="">Todos</option>
                            @php
                                $anoActual = date('Y');
                                $anosVehiculos = range($anoActual - 10, $anoActual);
                            @endphp
                            @foreach($anosVehiculos as $ano)
                                <option value="{{ $ano }}" {{ request('filtro_ano_fabricacion') == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="filtro_ano" class="form-label">Año de referencia:</label>
                        <select class="form-select filter-input" id="filtro_ano" name="filtro_ano">
                            @php
                                $anoActual = date('Y');
                                $anosDisponibles = range($anoActual - 5, $anoActual + 10);
                            @endphp
                            <option value="{{ $anoActual }}">{{ $anoActual }} (actual)</option>
                            @foreach($anosDisponibles as $ano)
                                @if($ano != $anoActual)
                                    <option value="{{ $ano }}" {{ request('filtro_ano') == $ano ? 'selected' : '' }}>{{ $ano }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="filtro_estado" class="form-label">Estado:</label>
                        <select class="form-select filter-input" id="filtro_estado" name="filtro_estado">
                            <option value="">Todos</option>
                            <option value="activo" {{ request('filtro_estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                            <option value="amortizado" {{ request('filtro_estado') == 'amortizado' ? 'selected' : '' }}>Amortizados</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="filtro_parking" class="form-label">Parking:</label>
                        <select class="form-select filter-input" id="filtro_parking" name="filtro_parking">
                            <option value="">Todos</option>
                            @foreach($parkings as $parking)
                                <option value="{{ $parking->id }}" {{ request('filtro_parking') == $parking->id ? 'selected' : '' }}>{{ $parking->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3 d-flex align-items-end">
                        <a href="{{ route('admin.financiero.balance.activos') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar todos
                        </a>
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
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($valorVehiculos, 0, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">{{ $countVehiculosActivos }} vehículos activos ({{ number_format($precioPromedioVehiculo, 0, ',', '.') }}€/u.)</div>
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
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($valorParkings, 0, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">{{ number_format($metrosCuadradosTotales, 0, ',', '.') }} m² totales ({{ $countParkings }} parkings)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nueva disposición: 60% tabla, 40% gráfico y leyenda -->
    <div class="row mb-4">
        <!-- Contenedor principal para la nueva estructura (60% tabla, 40% gráfico) -->
        <div class="row">
            <!-- Tabla detallada de activos (60% del ancho) -->
            <div class="col-md-7">
                <div class="card shadow mb-4 h-100">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-white">Detalle de Activos</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Descripción</th>
                                        <th>Año</th>
                                        <th>Valor Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $año_referencia = request('filtro_ano') ? intval(request('filtro_ano')) : date('Y');
                                        $vehiculosHeader = false;
                                        $parkingsHeader = false;
                                    @endphp
                                    
                                    @foreach($elementosPaginados as $elemento)
                                        @php
                                            $tipo = $elemento['tipo'];
                                            $objeto = $elemento['objeto'];
                                        @endphp
                                        
                                        @if($tipo == 'vehiculo' && !$vehiculosHeader)
                                            <tr class="table-primary">
                                                <td colspan="4"><strong><i class="fas fa-car me-2"></i> VEHÍCULOS</strong></td>
                                            </tr>
                                            @php $vehiculosHeader = true; @endphp
                                        @endif
                                        
                                        @if($tipo == 'parking' && !$parkingsHeader)
                                            <tr class="table-success">
                                                <td colspan="4"><strong><i class="fas fa-parking me-2"></i> PARKINGS</strong></td>
                                            </tr>
                                            @php $parkingsHeader = true; @endphp
                                        @endif
                                        
                                        @if($tipo == 'vehiculo')
                                            <tr>
                                                <td><i class="fas fa-car text-primary me-2"></i> {{ $objeto->tipo ? $objeto->tipo->nombre : 'Vehículo' }}</td>
                                                <td>{{ $objeto->marca }} {{ $objeto->modelo }} ({{ $objeto->matricula }})</td>
                                                <td>{{ $objeto->año }}</td>
                                                <td>
                                                    @php
                                                        $valor_actual = $objeto->calcularValorActual($año_referencia);
                                                    @endphp
                                                    @if($valor_actual > 0)
                                                        {{ number_format($valor_actual, 0, ',', '.') }} €
                                                    @else
                                                        <span class="badge bg-danger">AMORTIZADO</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @elseif($tipo == 'parking')
                                            <tr>
                                                <td><i class="fas fa-parking text-success me-2"></i> Parking</td>
                                                <td>{{ $objeto->nombre }} ({{ $objeto->plazas }} plazas)</td>
                                                <td>{{ $objeto->created_at ? $objeto->created_at->year : 'N/A' }}</td>
                                                <td>
                                                    {{ number_format($objeto->calcularValorTotal($año_referencia), 0, ',', '.') }} €
                                                    <br>
                                                    @php
                                                        // Calcular metros cuadrados estimados (25m² por plaza)
                                                        $metrosEstimados = $objeto->plazas * 25;
                                                        
                                                        // Calcular precio por metro cuadrado a partir del valor total
                                                        $valorTotal = $objeto->calcularValorTotal($año_referencia);
                                                        $precioPorMetro = $metrosEstimados > 0 ? round($valorTotal / $metrosEstimados) : 0;
                                                    @endphp
                                                    <small class="text-muted">{{ number_format($metrosEstimados, 0, ',', '.') }} m² x {{ number_format($precioPorMetro, 0, ',', '.') }} €</small>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                    
                                    @if(count($elementosPaginados) == 0)
                                    <tr>
                                        <td colspan="4" class="text-center">No hay activos registrados</td>
                                    </tr>
                                    @endif
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th colspan="3" class="text-end">Total Activos:</th>
                                        <th>{{ number_format($totalActivos, 0, ',', '.') }} €</th>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <!-- Controles de paginación -->
                            @if($totalPaginas > 1)
                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Paginación de activos">
                                    <ul class="pagination">
                                        <!-- Botón Anterior -->
                                        <li class="page-item {{ $paginaActual <= 1 ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ route('admin.financiero.balance.activos', array_merge(request()->query(), ['pagina' => $paginaActual - 1])) }}" aria-label="Anterior">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        
                                        <!-- Páginas -->
                                        @for($i = 1; $i <= $totalPaginas; $i++)
                                            <li class="page-item {{ $i == $paginaActual ? 'active' : '' }}">
                                                <a class="page-link" href="{{ route('admin.financiero.balance.activos', array_merge(request()->query(), ['pagina' => $i])) }}">
                                                    {{ $i }}
                                                </a>
                                            </li>
                                        @endfor
                                        
                                        <!-- Botón Siguiente -->
                                        <li class="page-item {{ $paginaActual >= $totalPaginas ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ route('admin.financiero.balance.activos', array_merge(request()->query(), ['pagina' => $paginaActual + 1])) }}" aria-label="Siguiente">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                            <div class="text-center text-muted small mt-2">
                                Mostrando {{ count($elementosPaginados) }} de {{ $countVehiculos + $countParkings }} activos
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Gráfico y leyenda (40% del ancho) -->
            <div class="col-md-5">
                <!-- Gráfico de distribución -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Distribución de Activos por Categoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-center">
                            <canvas id="activosPorCategoriaChart" style="width: 100%; max-width: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Leyenda del gráfico -->
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Detalle por Categoría</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Categoría</th>
                                        <th>Valor</th>
                                        <th>%</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($categorias as $index => $categoria)
                                    @php
                                        $valor = $valores[$index];
                                        $porcentaje = $totalActivos > 0 ? round(($valor / $totalActivos) * 100, 1) : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <i class="fas fa-square" style="color: {{ ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'][$index % 6] }}"></i> 
                                            {{ $categoria }}
                                        </td>
                                        <td>{{ number_format($valor, 0, ',', '.') }} €</td>
                                        <td>{{ number_format($porcentaje, 1) }}%</td>
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
    <div class="modal fade" id="nuevoActivoModal" tabindex="-1" aria-labelledby="nuevoActivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="nuevoActivoModalLabel">Registrar Nuevo Activo</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="nuevoActivoForm" action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Activo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                @foreach($categorias as $categoria)
                                    <option value="{{ $categoria }}">{{ $categoria }}</option>
                                @endforeach
                                <option value="nueva">Otra categoría...</option>
                            </select>
                        </div>
                        <div class="mb-3" id="nuevaCategoriaContainer" style="display: none;">
                            <label for="nuevaCategoria" class="form-label">Nueva Categoría</label>
                            <input type="text" class="form-control" id="nuevaCategoria" name="nuevaCategoria">
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor (€)</label>
                            <input type="number" class="form-control" id="valor" name="valor" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaAdquisicion" class="form-label">Fecha de Adquisición</label>
                            <input type="date" class="form-control" id="fechaAdquisicion" name="fechaAdquisicion" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="nuevoActivoForm" class="btn btn-primary">Guardar Activo</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Chart.js para el gráfico de activos
        const ctx = document.getElementById('activosPorCategoriaChart').getContext('2d');
        
        const activosChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: {!! json_encode($categorias) !!},
                datasets: [{
                    data: {!! json_encode($valores) !!},
                    backgroundColor: [
                        '#4e73df', // primary
                        '#1cc88a', // success
                        '#36b9cc', // info
                        '#f6c23e', // warning
                        '#e74a3b', // danger
                        '#858796'  // secondary
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value/total) * 100).toFixed(1);
                                return `${label}: ${value.toLocaleString('es-ES')} € (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
        
        // Implementar filtros automáticos
        const filterInputs = document.querySelectorAll('.filter-input');
        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                document.getElementById('filtroActivosForm').submit();
            });
        });
        
        // Botones para limpiar filtros individuales
        const limpiarBotones = document.querySelectorAll('.limpiar-filtro');
        limpiarBotones.forEach(boton => {
            boton.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                if (targetId) {
                    document.getElementById(targetId).value = '';
                    document.getElementById('filtroActivosForm').submit();
                }
            });
        });
        
        // Función para aplicar filtros con retraso para evitar muchas solicitudes
        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }
        
        // Aplicar filtros al escribir (con retraso)
        const aplicarFiltros = debounce(() => {
            if (filtroForm) filtroForm.submit();
        });
        
        // Eventos para filtros automáticos
        if (filtroVehiculo) {
            filtroVehiculo.addEventListener('input', aplicarFiltros);
        }
        
        if (filtroParking) {
            filtroParking.addEventListener('input', aplicarFiltros);
        }
        
        // Botones para limpiar filtros
        const btnLimpiarVehiculo = document.getElementById('limpiar_vehiculo');
        if (btnLimpiarVehiculo && filtroVehiculo) {
            btnLimpiarVehiculo.addEventListener('click', function() {
                filtroVehiculo.value = '';
                if (filtroForm) filtroForm.submit();
            });
        }
        
        const btnLimpiarParking = document.getElementById('limpiar_parking');
        if (btnLimpiarParking && filtroParking) {
            btnLimpiarParking.addEventListener('click', function() {
                filtroParking.value = '';
                if (filtroForm) filtroForm.submit();
            });
        }
        
        // Mostrar campo de nueva categoría cuando se selecciona "Otra categoría..."
        const categoriaSelect = document.getElementById('categoria');
        if (categoriaSelect) {
            categoriaSelect.addEventListener('change', function() {
                const nuevaCategoriaContainer = document.getElementById('nuevaCategoriaContainer');
                if (nuevaCategoriaContainer) {
                    if (this.value === 'nueva') {
                        nuevaCategoriaContainer.style.display = 'block';
                        const nuevaCategoria = document.getElementById('nuevaCategoria');
                        if (nuevaCategoria) nuevaCategoria.setAttribute('required', 'required');
                    } else {
                        nuevaCategoriaContainer.style.display = 'none';
                        const nuevaCategoria = document.getElementById('nuevaCategoria');
                        if (nuevaCategoria) nuevaCategoria.removeAttribute('required');
                    }
                }
            });
        }
    });
</script>
@endsection
