@extends('layouts.admin_financiero')

@section('content')
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
                            <div class="alert {{ $esPositivo ? 'alert-success' : 'alert-danger' }}">
                                <i class="fas {{ $esPositivo ? 'fa-thumbs-up' : 'fa-exclamation-triangle' }} me-2"></i>
                                <strong>El balance para este periodo es {{ $esPositivo ? 'positivo' : 'negativo' }}.</strong>
                                @if($esPositivo)
                                    Puedes asignar hasta <strong>{{ number_format($balance, 2, ',', '.') }} €</strong> para presupuestos.
                                @else
                                    Deberías considerar reducir gastos o aumentar ingresos para equilibrar el balance.
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
                                                    <input type="number" class="form-control" name="presupuestos[{{ $categoria }}]" 
                                                        step="0.01" min="0" 
                                                        value="{{ old('presupuestos.' . $categoria, $presupuestoActual ?: $valor) }}"
                                                        aria-label="Presupuesto para {{ $categoria }}">
                                                    <span class="input-group-text">€</span>
                                                </div>
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
                                            <button type="submit" class="btn btn-success w-100">
                                                <i class="fas fa-save me-2"></i> Guardar Presupuestos
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
