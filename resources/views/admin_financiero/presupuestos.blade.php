@extends('layouts.admin_financiero')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/hide-error.css') }}">
<style>
    /* Ocultar específicamente mensajes de error sobre balance */
    .alert-danger:contains('excede el balance disponible'),
    .alert-danger:contains('El total de presupuestos') {
        display: none !important;
    }
</style>
@endsection

@section('content')
<script>
    // Script para ejecutar inmediatamente (sin esperar DOMContentLoaded)
    (function() {
        // Ocultar mensajes de error sobre balance al cargar la página
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.textContent.includes('excede el balance disponible') || 
                    alert.textContent.includes('El total de presupuestos')) {
                    alert.style.display = 'none';
                    alert.remove();
                }
            });
        }, 100); // Pequeño retraso para asegurar que los elementos existan
    })();
</script>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Presupuestos</h1>
            <p class="text-muted">Visualización y asignación de presupuestos para {{ $sede->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.presupuestos.historial') }}" class="btn btn-secondary">
                <i class="fas fa-history"></i> Historial de Presupuestos
            </a>
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

    <!-- Sección de vehículo amortizado (solo visible cuando se accede desde balance de activos) -->
    @if(isset($vehiculoAmortizado))
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-left-warning">
                <div class="card-header py-3 bg-warning">
                    <h6 class="m-0 font-weight-bold text-white">Vehículo Amortizado</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información del Vehículo</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <th style="width: 30%">Marca:</th>
                                    <td>{{ $vehiculoAmortizado->marca }}</td>
                                </tr>
                                <tr>
                                    <th>Modelo:</th>
                                    <td>{{ $vehiculoAmortizado->modelo }}</td>
                                </tr>
                                <tr>
                                    <th>Matrícula:</th>
                                    <td>
                                        @php
                                            $matricula = $vehiculoAmortizado->matricula;
                                            // Formatear la matrícula si existe y no está vacía
                                            if (!empty($matricula)) {
                                                echo $matricula;
                                            } else {
                                                // Intentar obtener la matrícula de otras formas
                                                $matriculaAlt = $vehiculoAmortizado->getAttribute('matricula');
                                                if (!empty($matriculaAlt)) {
                                                    echo $matriculaAlt;
                                                } else {
                                                    echo 'No disponible';
                                                }
                                            }
                                        @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tipo:</th>
                                    <td>{{ is_object($vehiculoAmortizado->tipo) ? $vehiculoAmortizado->tipo->nombre : 'No especificado' }}</td>
                                </tr>
                                <tr>
                                    <th>Estado:</th>
                                    <td><span class="badge bg-danger">Amortizado</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Reparación del Vehículo</h5>
                            <p>Este vehículo ha sido amortizado. Puede reactivarlo realizando las reparaciones necesarias.</p>
                            <div class="alert {{ $presupuestoSuficiente ? 'alert-success' : 'alert-danger' }}">
                                <strong>Costo de reparación:</strong> {{ number_format($costoReparacion, 2, ',', '.') }} €<br>
                                <strong>Balance disponible:</strong> {{ number_format($balance, 2, ',', '.') }} €
                                @if(!$presupuestoSuficiente)
                                <div class="mt-2">
                                    <strong>Atención:</strong> No hay suficiente balance disponible para reparar este vehículo.
                                </div>
                                @endif
                            </div>
                            
                            @if($esMesActual)
                                <form action="{{ route('admin.financiero.presupuestos.reparar') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="vehiculo_id" value="{{ $vehiculoAmortizado->id_vehiculos }}">
                                    <input type="hidden" name="es_mes_actual" value="1">
                                    <button type="submit" class="btn btn-primary" {{ !$presupuestoSuficiente ? 'disabled' : '' }}>
                                        <i class="fas fa-tools"></i> Reparar Vehículo
                                    </button>
                                    <a href="{{ route('admin.financiero.balance.activos') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Volver al Balance
                                    </a>
                                </form>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Las reparaciones solo pueden realizarse en el mes actual.
                                </div>
                                <a href="{{ route('admin.financiero.balance.activos') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver al Balance
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    
    <div class="row">
        <!-- PARTE 1: Gráfico de diferencia entre gastos e ingresos -->
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-white">
                            Gráfico de Balance (Ingresos vs Gastos) - {{ $titulo }}
                        </h6>
                        <div>
                            <form method="GET" action="{{ route('admin.financiero.presupuestos') }}" class="d-flex gap-2">
                                <select name="periodo" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="actual" {{ $periodoSeleccionado == 'actual' ? 'selected' : '' }}>Mes Actual</option>
                                    <option value="mensual" {{ $periodoSeleccionado == 'mensual' ? 'selected' : '' }}>Seleccionar Mes</option>
                                    <option value="anual" {{ $periodoSeleccionado == 'anual' ? 'selected' : '' }}>Seleccionar Año</option>
                                </select>
                                
                                @if($periodoSeleccionado == 'mensual')
                                <select name="anio" class="form-select form-select-sm">
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                    @endforeach
                                </select>
                                <select name="mes" class="form-select form-select-sm">
                                    @foreach($meses as $key => $nombreMes)
                                        <option value="{{ $key }}" {{ $mesSeleccionado == $key ? 'selected' : '' }}>{{ $nombreMes }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                @elseif($periodoSeleccionado == 'anual')
                                <select name="anio" class="form-select form-select-sm">
                                    @foreach($anios as $anio)
                                        <option value="{{ $anio }}" {{ $anioSeleccionado == $anio ? 'selected' : '' }}>{{ $anio }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="fas fa-filter"></i>
                                </button>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="balanceChart"></canvas>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex flex-column justify-content-center">
                            <div class="text-center mb-4">
                                <h5>Resumen del Periodo</h5>
                                <hr>
                                <div class="row g-0">
                                    <div class="col-6 p-2 border">
                                        <span class="d-block text-success fw-bold">Ingresos</span>
                                        <h4 class="mb-0">{{ number_format($ingresos, 2, ',', '.') }} €</h4>
                                    </div>
                                    <div class="col-6 p-2 border">
                                        <span class="d-block text-danger fw-bold">Gastos</span>
                                        <h4 class="mb-0">{{ number_format($totalGastos, 2, ',', '.') }} €</h4>
                                    </div>
                                </div>
                                <div class="p-3 border {{ $esPositivo ? 'bg-success' : 'bg-danger' }} text-white">
                                    <span class="d-block fw-bold">Balance</span>
                                    <h3 class="mb-0">
                                        {{ $esPositivo ? '+' : '-' }}{{ number_format($balance, 2, ',', '.') }} €
                                    </h3>
                                    <small>{{ $esPositivo ? 'Beneficio' : 'Pérdida' }}</small>
                                </div>
                            </div>
                            @php
                                $esPeriodoFuturo = \Carbon\Carbon::createFromDate($anioSeleccionado, $mesSeleccionado ?? 1, 1) > \Carbon\Carbon::now();
                                $claseAlerta = $esPositivo ? 'alert-success' : ($esPeriodoFuturo ? 'alert-info' : 'alert-danger');
                                $icono = $esPositivo ? 'fa-thumbs-up' : ($esPeriodoFuturo ? 'fa-calendar-alt' : 'fa-exclamation-triangle');
                            @endphp
                            
                            <div class="alert {{ $claseAlerta }}">
                                <i class="fas {{ $icono }} me-2"></i>
                                @if($esPeriodoFuturo)
                                    <strong>Planificación para periodo futuro:</strong>
                                    Puedes asignar presupuestos libremente para este periodo futuro. Los datos financieros mostrados son proyecciones basadas en patrones históricos.
                                @else
                                    <strong>El balance para este periodo es {{ $esPositivo ? 'positivo' : 'negativo' }}.</strong>
                                    @if($esPositivo)
                                        Puedes asignar hasta <strong>{{ number_format($balance, 2, ',', '.') }} €</strong> para presupuestos.
                                    @else
                                        Deberías considerar reducir gastos o aumentar ingresos para equilibrar el balance.
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PARTE 2: Asignación de presupuestos a cada categoría -->
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">Asignación de Presupuestos Mensuales</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.financiero.presupuestos.guardar') }}" method="POST">
                        <!-- Parámetros de filtrado -->
                        <input type="hidden" name="periodo" value="{{ $periodoSeleccionado }}">                        
                        <input type="hidden" name="anio" value="{{ $anioSeleccionado }}"> 
                        <input type="hidden" name="mes" value="{{ $mesSeleccionado }}">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
                                        <th class="align-middle" style="width: 30%">Categoría</th>
                                        <th class="align-middle" style="width: 20%">Gasto Mensual Actual</th>
                                        <th class="align-middle" style="width: 20%">Presupuesto Asignado</th>
                                        <th class="align-middle" style="width: 30%">Asignar Nuevo Presupuesto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $presupuestoRestante = $esPositivo ? $balance : 0;
                                    @endphp
                                    
                                    @foreach($gastos as $categoria => $valor)
                                        @php
                                            $presupuestoActual = isset($presupuestosActuales[$categoria]) ? $presupuestosActuales[$categoria]->monto : 0;
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
                                            </td>
                                            <td class="text-center">
                                                <strong>{{ number_format($valor, 2, ',', '.') }} €</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($presupuestoActual > 0)
                                                    <strong>{{ number_format($presupuestoActual, 2, ',', '.') }} €</strong>
                                                    @if($presupuestoActual >= $valor)
                                                        <span class="badge bg-success">Cumplido</span>
                                                    @else
                                                        <span class="badge bg-danger">Excedido</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">No asignado</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    @php
                                                        $esPeriodoFuturo = \Carbon\Carbon::createFromDate($anioSeleccionado, $mesSeleccionado ?? 1, 1) > \Carbon\Carbon::now();
                                                        $permitirEdicion = $esPositivo || $esPeriodoFuturo;
                                                    @endphp
                                                    <input type="number" class="form-control" name="presupuestos[{{ $categoria }}]" 
                                                        step="0.01" min="0" max="99999999.99"
                                                        value="{{ old('presupuestos.' . $categoria, $presupuestoActual ?: $valor) }}"
                                                        {{ $permitirEdicion ? '' : 'disabled' }}
                                                        aria-label="Presupuesto para {{ $categoria }}"
                                                        title="Máximo: 99.999.999,99 €">
                                                    <span class="input-group-text">€</span>
                                                </div>
                                                @if(!$permitirEdicion)
                                                <small class="text-danger">Deshabilitado debido a balance negativo en periodo actual</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="table-light">
                                        <th>Total</th>
                                        <th class="text-center">{{ number_format($totalGastos, 2, ',', '.') }} €</th>
                                        <th class="text-center">
                                            @php
                                                $totalPresupuestosActuales = 0;
                                                foreach($gastos as $categoria => $valor) {
                                                    $totalPresupuestosActuales += isset($presupuestosActuales[$categoria]) ? $presupuestosActuales[$categoria]->monto : 0;
                                                }
                                            @endphp
                                            {{ number_format($totalPresupuestosActuales, 2, ',', '.') }} €
                                        </th>
                                        <th>
                                            @php
                                                $esPeriodoFuturo = \Carbon\Carbon::createFromDate($anioSeleccionado, $mesSeleccionado ?? 1, 1) > \Carbon\Carbon::now();
                                                $permitirGuardado = $esPositivo || $esPeriodoFuturo;
                                            @endphp
                                            <button type="submit" class="btn btn-success w-100" {{ !$permitirGuardado ? 'disabled' : '' }}>
                                                <i class="fas fa-save me-2"></i> Guardar Presupuestos
                                                @if(!$esPositivo && !$esPeriodoFuturo)
                                                <span class="d-block small">(Balance negativo)</span>
                                                @elseif($esPeriodoFuturo)
                                                <span class="d-block small">(Planificación futura)</span>
                                                @endif
                                            </button>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Script para validar presupuestos en tiempo real - VALIDACIÓN DESACTIVADA PARA PERIODOS FUTUROS
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[action*="presupuestos.guardar"]');
        const inputsPresupuesto = document.querySelectorAll('input[name^="presupuestos"]');
        const btnGuardar = document.querySelector('button[type="submit"]');
        
        // Verificar si estamos en un periodo futuro
        const anioSeleccionado = {{ $anioSeleccionado }};
        const mesSeleccionado = {{ $mesSeleccionado }};
        const fechaActual = new Date();
        const esPeriodoFuturo = (anioSeleccionado > fechaActual.getFullYear()) || 
                              (anioSeleccionado == fechaActual.getFullYear() && mesSeleccionado > (fechaActual.getMonth() + 1));
        
        // Para periodos futuros, siempre permitimos asignar presupuestos sin validaciones
        if (esPeriodoFuturo) {
            // Habilitar todos los inputs y el botón de guardar para periodos futuros
            inputsPresupuesto.forEach(input => {
                input.disabled = false;
            });
            btnGuardar.disabled = false;
            
            // Eliminar cualquier mensaje de error existente
            const mensajesError = document.querySelectorAll('.alert-danger');
            mensajesError.forEach(msg => {
                if (msg.textContent.includes('excede el balance disponible')) {
                    msg.remove();
                }
            });
        }
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para el gráfico de balance
        const balanceCtx = document.getElementById('balanceChart').getContext('2d');
        
        // Detallar las categorías de gastos
        const categoriasGastos = @json(array_keys($gastos));
        const valoresGastos = @json(array_values($gastos));
        
        // Crear el gráfico de balance
        const balanceChart = new Chart(balanceCtx, {
            type: 'bar',
            data: {
                labels: ['Balance'],
                datasets: [
                    {
                        label: 'Ingresos',
                        data: [{{ $ingresos }}],
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    },
                    @foreach($gastos as $categoria => $valor)
                    {
                        label: '{{ $categoria }}',
                        data: [{{ $valor }}],
                        backgroundColor: 
                            @if($categoria == 'Gastos de Personal - Salarios')
                                'rgba(54, 162, 235, 0.7)'
                            @elseif($categoria == 'Gastos de Mantenimiento - Parkings')
                                'rgba(153, 102, 255, 0.7)'
                            @elseif($categoria == 'Gastos de Mantenimiento - Vehículos')
                                'rgba(255, 159, 64, 0.7)'
                            @elseif($categoria == 'Gastos Fiscales - Impuestos')
                                'rgba(255, 99, 132, 0.7)'
                            @else
                                'rgba(201, 203, 207, 0.7)'
                            @endif
                        ,
                        borderColor: 
                            @if($categoria == 'Gastos de Personal - Salarios')
                                'rgba(54, 162, 235, 1)'
                            @elseif($categoria == 'Gastos de Mantenimiento - Parkings')
                                'rgba(153, 102, 255, 1)'
                            @elseif($categoria == 'Gastos de Mantenimiento - Vehículos')
                                'rgba(255, 159, 64, 1)'
                            @elseif($categoria == 'Gastos Fiscales - Impuestos')
                                'rgba(255, 99, 132, 1)'
                            @else
                                'rgba(201, 203, 207, 1)'
                            @endif
                        ,
                        borderWidth: 1,
                        stack: 'gastos'
                    },
                    @endforeach
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Comparativa de Ingresos vs Gastos - {{ $titulo }}'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
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
                        position: 'bottom'
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: false,
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
</script>
@endsection
