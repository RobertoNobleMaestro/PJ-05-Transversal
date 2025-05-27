@extends('layouts.admin_financiero')

@php
// Definir cualquier variable que pueda faltar para evitar errores
if (!isset($activos)) {
    $activos = [];
}
@endphp

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h1>Balance de Pasivos</h1>
            <p class="text-muted">Obligaciones pendientes de pago según esquema contable estudiantil</p>
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

    <!-- Filtros por fecha -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.financiero.balance.pasivos') }}" method="GET" id="filtroPasivosForm">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_desde" class="form-label">Fecha (Desde):</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_hasta" class="form-label">Fecha (Hasta):</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                    </div>
                    <div class="col-md-4 mb-3 d-flex align-items-end">
                        <a href="{{ route('admin.financiero.balance.pasivos') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Limpiar Filtros
                        </a>
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
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-car fa-2x text-warning"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Mantenimiento Vehículos</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($gastosMantenimientoVehiculos ?? ($pasivosPorCategoria['Mantenimiento Vehículos'] ?? 750), 0, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Facturas pendientes por reparaciones ({{ $countVehiculos ?? 0 }} vehículos)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mantenimiento Parkings -->
                <div class="col-md-6 mb-3">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-parking fa-2x text-primary"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Mantenimiento Parkings</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($gastosMantenimientoParkings ?? ($pasivosPorCategoria['Mantenimiento Parkings'] ?? 200), 0, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Facturas pendientes de instalaciones ({{ $countParkings ?? 0 }} parkings)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Ingresos no devengados -->
                <div class="col-md-12 mt-3">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-calendar-alt fa-2x text-info"></i>
                                </div>
                                <div class="col">
                                    <div class="text-xs text-uppercase mb-1">Ingresos no devengados (Pagos anticipados)</div>
                                    <div class="h5 mb-0 font-weight-bold">{{ number_format($pasivosPorCategoria['Ingresos No Devengados'] ?? 0, 0, ',', '.') }} €</div>
                                    <div class="text-xs text-muted">Reservas pagadas por adelantado (pasarán a ingresos cuando se entregue el vehículo)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos de análisis financiero -->
    <div class="row">
        <!-- Gráfico de distribución de pasivos -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Distribución de Pasivos por Categoría</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <canvas id="pasivosChart" height="300"></canvas>
                        </div>
                        <div class="col-md-6">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Categoría</th>
                                            <th class="text-end">Valor</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $totalPasivos = array_sum($pasivosPorCategoria); @endphp
                                        @foreach($pasivosPorCategoria as $categoria => $valor)
                                            @php 
                                                $porcentaje = $totalPasivos > 0 ? ($valor / $totalPasivos) * 100 : 0;
                                            @endphp
                                            <tr>
                                                <td>
                                                    <i class="fas fa-square" style="color: {{ ['#e74a3b', '#f6c23e', '#36b9cc', '#4e73df', '#1cc88a', '#858796'][$loop->index % 6] }}"></i> 
                                                    {{ $categoria }}
                                                </td>
                                                <td class="text-end">{{ number_format($valor, 0, ',', '.') }} €</td>
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
        <!-- Se ha eliminado el gráfico de evolución de pasivos -->
    </div>

    <!-- Sección especial para gastos de mantenimiento y salarios -->
    <div class="row mb-4">
        <!-- Detalle de salarios de asalariados -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i> Detalle de Salarios</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Importante:</strong> Todos los asalariados reciben su salario el día 1 de cada mes.
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Rol</th>
                                    <th>Número de empleados</th>
                                    <th>Salario estándar</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $asalariadosPorRol = $asalariados->groupBy(function ($asalariado) {
                                        return $asalariado->usuario->roles->nombre_rol ?? 'Sin rol';
                                    });
                                @endphp
                                
                                @foreach($asalariadosPorRol as $rol => $listaAsalariados)
                                    @php
                                        $cantidad = $listaAsalariados->count();
                                        $salarioEstandar = $listaAsalariados->first()->salario;
                                        $totalPorRol = $cantidad * $salarioEstandar;
                                    @endphp
                                    <tr>
                                        <td>{{ $rol }}</td>
                                        <td>{{ $cantidad }}</td>
                                        <td>{{ number_format($salarioEstandar, 0, ',', '.') }} €</td>
                                        <td>{{ number_format($totalPorRol, 0, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-light">
                                    <th colspan="3">Total salarios mensuales:</th>
                                    <th>{{ number_format($totalSalarios, 0, ',', '.') }} €</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Detalle de gastos de mantenimiento -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i> Gastos de Mantenimiento</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Nota:</strong> Se muestran los gastos de mantenimiento registrados. Si no hay registros, se muestra una estimación basada en la flota.
                    </div>
                    
                    @if($gastosMantenimiento->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Concepto</th>
                                        <th>Fecha</th>
                                        <th>Vehículo/Parking</th>
                                        <th>Importe</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($gastosMantenimiento as $gasto)
                                        <tr>
                                            <td>{{ $gasto->concepto }}</td>
                                            <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                                            <td>
                                                @if($gasto->id_vehiculo)
                                                    {{ $gasto->vehiculo->marca ?? '' }} {{ $gasto->vehiculo->modelo ?? '' }}
                                                @elseif($gasto->id_parking)
                                                    {{ $gasto->parking->nombre ?? 'Parking' }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ number_format($gasto->importe, 0, ',', '.') }} €</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th colspan="3">Total gastos de mantenimiento:</th>
                                        <th>{{ number_format($gastosMantenimiento->sum('importe'), 0, ',', '.') }} €</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <p><i class="fas fa-info-circle me-2"></i> No hay gastos de mantenimiento registrados en el período seleccionado.</p>
                            <p>Estimación basada en la flota actual:</p>
                            <ul class="list-group mb-3">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Mantenimiento de {{ $countVehiculos }} vehículos
                                    <span class="badge bg-primary rounded-pill">{{ number_format($pasivosPorCategoria['Mantenimiento Vehículos'], 0, ',', '.') }} €</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Mantenimiento de {{ $countParkings }} parkings
                                    <span class="badge bg-primary rounded-pill">{{ number_format($pasivosPorCategoria['Mantenimiento Parkings'], 0, ',', '.') }} €</span>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla detallada de pasivos -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-white">Detalle de Pasivos por Categoría</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Categoría</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Valor</th>
                            <th>Fecha Registro</th>
                            <th>Fecha Vencimiento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pasivosDetalle as $categoria => $pasivosList)
                            <!-- Encabezado de la categoría -->
                            <tr class="table-secondary">
                                <td colspan="6" class="fw-bold">
                                    <i class="fas fa-folder-open me-2"></i> {{ $categoria }} 
                                    <span class="badge bg-danger ms-2">{{ count($pasivosList) }} items</span>
                                    <span class="ms-2">Total: {{ number_format($pasivosPorCategoria[$categoria], 0, ',', '.') }} €</span>
                                </td>
                            </tr>
                            
                            <!-- Detalles de cada pasivo en esta categoría -->
                            @foreach($pasivosList as $pasivo)
                                <tr>
                                    <td></td> <!-- Columna vacía para indentar -->
                                    <td><i class="fas fa-file-invoice-dollar me-1"></i> {{ $pasivo['nombre'] }}</td>
                                    <td>{{ $pasivo['descripcion'] }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($pasivo['valor'], 0, ',', '.') }} €</td>
                                    <td>{{ $pasivo['fecha_registro']->format('d/m/Y') }}</td>
                                    <td>
                                        @if(isset($pasivo['fecha_vencimiento']) && $pasivo['fecha_vencimiento'])
                                            {{ $pasivo['fecha_vencimiento']->format('d/m/Y') }}
                                            @php
                                                $diasRestantes = (int) now()->diffInDays($pasivo['fecha_vencimiento'], false);
                                            @endphp
                                            @if($diasRestantes < 0)
                                                <span class="badge bg-danger ms-2">Vencido</span>
                                            @elseif($diasRestantes < 30)
                                                <span class="badge bg-warning ms-2">{{ $diasRestantes }} días</span>
                                            @else
                                                <span class="badge bg-success ms-2">{{ floor($diasRestantes/30) }} meses</span>
                                            @endif
                                        @else
                                            <span class="text-muted">No definida</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        
                        <!-- Si no hay pasivos mostrar mensaje -->
                        @if(count($pasivosDetalle) == 0)
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle me-2"></i> No hay pasivos registrados en el período seleccionado
                                </td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <th>Total</th>
                            <th>{{ number_format($totalPasivos, 2, ',', '.') }} €</th>
                            <th>100%</th>
                            <th>-</th>
                            <th>-</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


    
    <!-- Espacio para contenido futuro -->
    <!-- Se han eliminado los indicadores de riesgo financiero según la solicitud -->

    <!-- Modal para añadir nuevo pasivo -->
    <div class="modal fade" id="nuevoPasivoModal" tabindex="-1" aria-labelledby="nuevoPasivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="nuevoPasivoModalLabel">Registrar Nuevo Pasivo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="nuevoPasivoForm" action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Pasivo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                @if(isset($categorias) && is_array($categorias))
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria }}">{{ $categoria }}</option>
                                    @endforeach
                                @endif
                                <option value="nueva">Otra categoría...</option>
                            </select>
                        </div>
                        <div class="mb-3" id="nuevaCategoriaContainer" style="display: none;">
                            <label for="nuevaCategoria" class="form-label">Nueva Categoría</label>
                            <input type="text" class="form-control" id="nuevaCategoria" name="nuevaCategoria">
                        </div>
                        <div class="mb-3">
                            <label for="valor" class="form-label">Valor Total (€)</label>
                            <input type="number" class="form-control" id="valor" name="valor" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="interes" class="form-label">Tipo de Interés (%)</label>
                            <input type="number" class="form-control" id="interes" name="interes" step="0.01" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaInicio" class="form-label">Fecha de Inicio</label>
                            <input type="date" class="form-control" id="fechaInicio" name="fechaInicio" required>
                        </div>
                        <div class="mb-3">
                            <label for="fechaVencimiento" class="form-label">Fecha de Vencimiento</label>
                            <input type="date" class="form-control" id="fechaVencimiento" name="fechaVencimiento" required>
                        </div>
                        <div class="mb-3">
                            <label for="entidad" class="form-label">Entidad o Acreedor</label>
                            <input type="text" class="form-control" id="entidad" name="entidad" required>
                        </div>
                        <div class="mb-3">
                            <label for="cuotaMensual" class="form-label">Cuota Mensual (€)</label>
                            <input type="number" class="form-control" id="cuotaMensual" name="cuotaMensual" step="0.01" min="0">
                        </div>
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="nuevoPasivoForm" class="btn btn-warning">Guardar Pasivo</button>
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
        // Inicializar el gráfico de distribución de pasivos
        const ctx = document.getElementById('pasivosChart');
        if (ctx) {
            const pasivosChart = new Chart(ctx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode(array_keys($pasivosPorCategoria)) !!},
                    datasets: [{
                        data: {!! json_encode(array_values($pasivosPorCategoria)) !!},
                        backgroundColor: ['#e74a3b', '#f6c23e', '#36b9cc', '#4e73df', '#1cc88a', '#858796'],
                        hoverBackgroundColor: ['#c23b2e', '#dda20a', '#2c9faf', '#2e59d9', '#17a673', '#666666'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    responsive: true,
                    cutout: '70%',
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((acc, val) => acc + val, 0);
                                    const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                    return `${label}: ${value.toLocaleString('es-ES')} € (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Implementar filtros automáticos para fechas con debounce
        const fechaDesde = document.getElementById('fecha_desde');
        const fechaHasta = document.getElementById('fecha_hasta');
        const filtroForm = document.getElementById('filtroPasivosForm');
        
        // Función debounce para evitar múltiples envíos
        function debounce(func, timeout = 300) {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => { func.apply(this, args); }, timeout);
            };
        }
        
        // Aplicar filtros cuando cambian las fechas
        const aplicarFiltros = debounce(() => {
            if (filtroForm) filtroForm.submit();
        });
        
        if (fechaDesde) {
            fechaDesde.addEventListener('change', aplicarFiltros);
        }
        
        if (fechaHasta) {
            fechaHasta.addEventListener('change', aplicarFiltros);
        }
        
        // Botones para limpiar filtros de fecha
        const btnLimpiarFechas = document.getElementById('limpiar_fechas');
        if (btnLimpiarFechas) {
            btnLimpiarFechas.addEventListener('click', function() {
                if (fechaDesde) fechaDesde.value = '';
                if (fechaHasta) fechaHasta.value = '';
                if (filtroForm) filtroForm.submit();
            });
        }
        
        // Mostrar/ocultar campo de nueva categoría cuando se selecciona "Otra categoría..."
        const categoriaSelect = document.getElementById('categoria');
        const nuevaCategoriaContainer = document.getElementById('nuevaCategoriaContainer');
        
        if (categoriaSelect && nuevaCategoriaContainer) {
            categoriaSelect.addEventListener('change', function() {
                if (this.value === 'nueva') {
                    nuevaCategoriaContainer.style.display = 'block';
                    const nuevaCategoria = document.getElementById('nuevaCategoria');
                    if (nuevaCategoria) nuevaCategoria.setAttribute('required', 'required');
                } else {
                    nuevaCategoriaContainer.style.display = 'none';
                    const nuevaCategoria = document.getElementById('nuevaCategoria');
                    if (nuevaCategoria) nuevaCategoria.removeAttribute('required');
                }
            });
        }
    });
</script>
@endsection
