@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-balance-scale"></i> Balance Financiero - {{ $sede->nombre }}
                    </h5>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtro por periodo -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form action="{{ route('admin.financiero.balance') }}" method="GET" id="filtroForm">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar"></i> Periodo</span>
                                    </div>
                                    <select name="periodo" id="periodo" class="form-control">
                                        @foreach($periodos as $valor => $nombre)
                                            <option value="{{ $valor }}" {{ $periodoSeleccionado == $valor ? 'selected' : '' }}>
                                                {{ $nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">Aplicar</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.financiero.activo.create') }}" class="btn btn-success mr-2">
                                <i class="fas fa-plus"></i> Registrar Activo
                            </a>
                            <a href="{{ route('admin.financiero.pasivo.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus"></i> Registrar Pasivo
                            </a>
                        </div>
                    </div>

                    <!-- Resumen del Balance -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Balance Inicial</h5>
                                    <h3 class="card-text {{ $balanceInicial >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($balanceInicial, 2) }} €
                                    </h3>
                                    <p class="text-muted">
                                        {{ $fechaInicio->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Balance Periodo</h5>
                                    <h3 class="card-text {{ $balancePeriodo >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($balancePeriodo, 2) }} €
                                    </h3>
                                    <p class="text-muted">
                                        {{ $fechaInicio->format('d/m/Y') }} - {{ $fechaFin->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Balance Final</h5>
                                    <h3 class="card-text {{ $balanceFinal >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($balanceFinal, 2) }} €
                                    </h3>
                                    <p class="text-muted">
                                        {{ $fechaFin->format('d/m/Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráfico de evolución del balance -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Evolución del Balance</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="balanceChart" width="100%" height="40"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles del Periodo -->
                    <div class="row">
                        <!-- Activos -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0">Activos del Periodo</h5>
                                </div>
                                <div class="card-body">
                                    @if($activosPeriodo->isEmpty())
                                        <p class="text-center">No hay activos registrados en este periodo</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Categoría</th>
                                                        <th>Fecha</th>
                                                        <th class="text-right">Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($activosPeriodo as $activo)
                                                        <tr>
                                                            <td>{{ $activo->nombre }}</td>
                                                            <td>
                                                                <span class="badge badge-info">
                                                                    {{ ucfirst($activo->categoria) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $activo->fecha_registro->format('d/m/Y') }}</td>
                                                            <td class="text-right">{{ number_format($activo->valor, 2) }} €</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th colspan="3">Total</th>
                                                        <th class="text-right">{{ number_format($totalActivosPeriodo, 2) }} €</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <!-- Resumen por Categoría -->
                                        <h6 class="mt-4 mb-2">Resumen por Categoría</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Categoría</th>
                                                        <th class="text-right">Valor</th>
                                                        <th class="text-right">Porcentaje</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($totalActivosPorCategoria as $categoria => $total)
                                                        <tr>
                                                            <td>{{ ucfirst($categoria) }}</td>
                                                            <td class="text-right">{{ number_format($total, 2) }} €</td>
                                                            <td class="text-right">
                                                                {{ number_format(($total / $totalActivosPeriodo) * 100, 1) }}%
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Pasivos -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Pasivos del Periodo</h5>
                                </div>
                                <div class="card-body">
                                    @if($pasivosPeriodo->isEmpty())
                                        <p class="text-center">No hay pasivos registrados en este periodo</p>
                                    @else
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Nombre</th>
                                                        <th>Categoría</th>
                                                        <th>Fecha</th>
                                                        <th class="text-right">Valor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($pasivosPeriodo as $pasivo)
                                                        <tr>
                                                            <td>{{ $pasivo->nombre }}</td>
                                                            <td>
                                                                <span class="badge badge-secondary">
                                                                    {{ ucfirst($pasivo->categoria) }}
                                                                </span>
                                                            </td>
                                                            <td>{{ $pasivo->fecha_registro->format('d/m/Y') }}</td>
                                                            <td class="text-right">{{ number_format($pasivo->valor, 2) }} €</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot>
                                                    <tr class="bg-light">
                                                        <th colspan="3">Total</th>
                                                        <th class="text-right">{{ number_format($totalPasivosPeriodo, 2) }} €</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                        <!-- Resumen por Categoría -->
                                        <h6 class="mt-4 mb-2">Resumen por Categoría</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Categoría</th>
                                                        <th class="text-right">Valor</th>
                                                        <th class="text-right">Porcentaje</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($totalPasivosPorCategoria as $categoria => $total)
                                                        <tr>
                                                            <td>{{ ucfirst($categoria) }}</td>
                                                            <td class="text-right">{{ number_format($total, 2) }} €</td>
                                                            <td class="text-right">
                                                                {{ number_format(($total / $totalPasivosPeriodo) * 100, 1) }}%
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional: Ingresos y Gastos -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">Información Adicional del Periodo</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-success">Ingresos por Reservas</h5>
                                                    <h3 class="card-text">{{ number_format($ingresosPorReservas, 2) }} €</h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body">
                                                    <h5 class="card-title text-danger">Gastos por Salarios</h5>
                                                    <h3 class="card-text">{{ number_format($gastosSalarios, 2) }} €</h3>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cambiar periodo automáticamente
    document.getElementById('periodo').addEventListener('change', function() {
        document.getElementById('filtroForm').submit();
    });
    
    // Cargar datos para el gráfico
    fetch('{{ route("admin.financiero.balance.chart") }}?periodo={{ $periodoSeleccionado }}')
        .then(response => response.json())
        .then(data => {
            // Configurar el gráfico
            const ctx = document.getElementById('balanceChart').getContext('2d');
            const myChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.periodos,
                    datasets: [
                        {
                            label: 'Activos',
                            data: data.activos,
                            backgroundColor: 'rgba(40, 167, 69, 0.2)',
                            borderColor: 'rgba(40, 167, 69, 1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            tension: 0.2
                        },
                        {
                            label: 'Pasivos',
                            data: data.pasivos,
                            backgroundColor: 'rgba(255, 193, 7, 0.2)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            pointRadius: 4,
                            tension: 0.2
                        },
                        {
                            label: 'Balance',
                            data: data.balance,
                            backgroundColor: 'rgba(0, 123, 255, 0.2)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 3,
                            pointRadius: 5,
                            tension: 0.2
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: true,
                            text: 'Evolución del Balance'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Error al cargar los datos:', error);
        });
});
</script>
@endsection
