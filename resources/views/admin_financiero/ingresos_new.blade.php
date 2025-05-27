@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Ingresos</h1>
            <p class="text-muted">Gestión de ingresos según esquema contable estudiantil</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.financiero.ingresos', ['tipo' => 'mensual']) }}" class="btn {{ $tipoVista == 'mensual' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Mensual
                </a>
                <a href="{{ route('admin.financiero.ingresos', ['tipo' => 'trimestral']) }}" class="btn {{ $tipoVista == 'trimestral' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Trimestral
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

    <!-- Resumen de ingresos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Resumen de Ingresos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Total Ingresos -->
                <div class="col-md-12 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-3x"></i>
                                </div>
                                <div class="col text-end">
                                    <h4 class="mb-0">{{ number_format($totalIngresos, 2, ',', '.') }} €</h4>
                                    <div>Total Ingresos {{ $tipoVista == 'mensual' ? 'Mensuales' : 'Trimestrales' }}</div>
                                    <small>Servicios realizados en el período seleccionado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de ingresos según esquema -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalle de Ingresos por Reservas</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Vehículo</th>
                            <th>Descripción</th>
                            <th>Ingresos (€)</th>
                            <th>Fecha de Servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fuentesIngresos as $fuente => $valor)
                            @if($valor > 0)
                                <tr>
                                    <td>
                                        <i class="fas fa-car text-success me-2"></i> 
                                        {{ str_replace('Ingresos por ', '', $fuente) }}
                                    </td>
                                    <td>Alquiler de vehículo completado</td>
                                    <td>{{ number_format($valor, 2, ',', '.') }} €</td>
                                    <td>{{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot class="table-success">
                        <tr>
                            <th colspan="2">TOTAL INGRESOS</th>
                            <th>{{ number_format($totalIngresos, 2, ',', '.') }} €</th>
                            <th>{{ $tipoVista == 'mensual' ? 'Mensual' : 'Trimestral' }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Evolución de ingresos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Evolución de Ingresos</h5>
        </div>
        <div class="card-body">
            <div style="height: 250px;">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Notas sobre ingresos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Reglas de Contabilización de Ingresos</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    <strong>Principio de devengo:</strong> Los ingresos se registran cuando se presta el servicio (entrega del vehículo), no cuando se recibe el pago.
                </li>
                <li class="list-group-item">
                    <i class="fas fa-calendar-check text-success me-2"></i>
                    <strong>Reservas:</strong> Una reserva de 500€ se registra como ingreso cuando se entrega el vehículo, no cuando se paga.
                </li>
                <li class="list-group-item bg-light">
                    <i class="fas fa-exclamation-circle text-warning me-2"></i>
                    <strong>Pagos anticipados:</strong> Si un cliente paga por adelantado, ese dinero se registra como un pasivo ("Ingresos no devengados") hasta entregar el vehículo.
                </li>
            </ul>
        </div>
    </div>

    <!-- Ejemplo práctico -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Ejemplo Práctico</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>Mes 1</th>
                            <th>Mes 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-down text-danger me-2"></i> <strong>Reserva recibida:</strong> 1.000€ (para Mes 2)</li>
                                    <li class="ms-4 text-muted">↳ Se registra como pasivo (no es ganancia aún)</li>
                                    <li class="mt-2"><i class="fas fa-arrow-down text-danger me-2"></i> <strong>Pago a choferes:</strong> 1.500€</li>
                                    <li class="ms-4 text-muted">↳ Se registra como gasto</li>
                                    <li class="mt-2"><i class="fas fa-arrow-down text-danger me-2"></i> <strong>Mantenimiento:</strong> 800€ (factura pendiente)</li>
                                    <li class="ms-4 text-muted">↳ Se registra como pasivo</li>
                                </ul>
                            </td>
                            <td>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-arrow-up text-success me-2"></i> <strong>Reserva del Mes 1:</strong> 1.000€</li>
                                    <li class="ms-4 text-muted">↳ Ahora sí es ganancia porque se prestó el servicio</li>
                                    <li class="mt-2"><i class="fas fa-arrow-down text-danger me-2"></i> <strong>Pago de factura:</strong> 800€</li>
                                    <li class="ms-4 text-muted">↳ El pasivo desaparece y se registra como gasto</li>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para el gráfico de línea
        const periodosGrafico = {!! json_encode($periodosGrafico) !!};
        const datosEvolucion = {!! json_encode($datosEvolucion) !!};
        
        // Configuración del gráfico de línea
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: periodosGrafico,
                datasets: [{
                    label: 'Ingresos',
                    data: datosEvolucion,
                    borderColor: 'rgba(40, 167, 69, 1)',
                    backgroundColor: 'rgba(40, 167, 69, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' €';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
