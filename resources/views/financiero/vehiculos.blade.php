@extends('layouts.admin_financiero')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Análisis de Rentabilidad por Vehículo</h1>
            <p class="text-muted">Sede: {{ $sede->nombre }} - Análisis del {{ $periodo }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('financial.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Dashboard
            </a>
            <a href="{{ route('financial.proyecciones') }}" class="btn btn-info">
                <i class="fas fa-chart-line"></i> Proyecciones
            </a>
        </div>
    </div>

    <!-- Filtros y resumen -->
    <div class="card shadow mb-4">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-0">Clasificación de Rentabilidad de la Flota</h5>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" id="vehicleSearch" class="form-control" placeholder="Buscar vehículo...">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Alta Rentabilidad
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $vehiculos->where('roi', '>', 30)->count() }} vehículos
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-star fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Rentabilidad Media
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $vehiculos->where('roi', '>', 10)->where('roi', '<=', 30)->count() }} vehículos
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Baja Rentabilidad
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $vehiculos->where('roi', '>', 0)->where('roi', '<=', 10)->count() }} vehículos
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        No Rentables
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        {{ $vehiculos->where('roi', '<=', 0)->count() }} vehículos
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-times fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <canvas id="rentabilidadChart" height="250"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="ocupacionChart" height="250"></canvas>
                </div>
            </div>

            <div class="table-responsive">
                <table id="vehicleTable" class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Modelo</th>
                            <th>Tipo</th>
                            <th>Parking</th>
                            <th>Reservas</th>
                            <th>Ingresos</th>
                            <th>Coste Mensual</th>
                            <th>Beneficio</th>
                            <th>ROI</th>
                            <th>Ocupación</th>
                            <th>Clasificación</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($vehiculos as $vehiculo)
                            <tr class="vehicle-row">
                                <td>{{ $vehiculo->marca }} {{ $vehiculo->modelo }}</td>
                                <td>{{ $vehiculo->tipo->nombre }}</td>
                                <td>{{ $vehiculo->parking->lugar->nombre }}</td>
                                <td>{{ $vehiculo->total_reservas }}</td>
                                <td>{{ number_format($vehiculo->ingresos_totales, 2, ',', '.') }} €</td>
                                <td>{{ number_format($vehiculo->coste_mensual, 2, ',', '.') }} €</td>
                                <td class="{{ $vehiculo->beneficio_mensual >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($vehiculo->beneficio_mensual, 2, ',', '.') }} €
                                </td>
                                <td class="{{ $vehiculo->roi >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($vehiculo->roi, 1) }}%
                                </td>
                                <td>
                                    <div class="progress">
                                        <div class="progress-bar bg-{{ $vehiculo->tasa_ocupacion > 60 ? 'success' : ($vehiculo->tasa_ocupacion > 30 ? 'info' : 'warning') }}" 
                                             role="progressbar" 
                                             style="width: {{ $vehiculo->tasa_ocupacion }}%" 
                                             aria-valuenow="{{ $vehiculo->tasa_ocupacion }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">{{ number_format($vehiculo->tasa_ocupacion, 0) }}%</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $vehiculo->color_clase }}">
                                        {{ $vehiculo->clasificacion }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recomendaciones -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Recomendaciones Operativas</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h5><i class="fas fa-graduation-cap text-primary me-2"></i>Mejora de Flota</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item">
                                    <strong>Renovar vehículos no rentables:</strong> Considerar reemplazar los modelos con ROI negativo.
                                </li>
                                <li class="list-group-item">
                                    <strong>Ajuste de precios:</strong> Aumentar tarifas para vehículos con alta ocupación y bajo ROI.
                                </li>
                                <li class="list-group-item">
                                    <strong>Redistribución:</strong> Trasladar vehículos de baja ocupación a parkings con mayor demanda.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5><i class="fas fa-bullseye text-danger me-2"></i>Estrategia de Marketing</h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="list-group-item">
                                    <strong>Promociones para baja ocupación:</strong> Descuentos en días/modelos menos rentables.
                                </li>
                                <li class="list-group-item">
                                    <strong>Marketing segmentado:</strong> Campañas dirigidas a usuarios de vehículos de alta rentabilidad.
                                </li>
                                <li class="list-group-item">
                                    <strong>Paquetes especiales:</strong> Crear ofertas combinadas con alojamiento para aumentar reservas.
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h5><i class="fas fa-chart-line text-success me-2"></i>Optimización de Costes</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <strong>Mantenimiento preventivo:</strong> Reducir costes de reparaciones programando servicios.
                                </li>
                                <li class="list-group-item">
                                    <strong>Proveedores:</strong> Renegociar contratos con proveedores de servicios y repuestos.
                                </li>
                                <li class="list-group-item">
                                    <strong>Eficiencia energética:</strong> Considerar la incorporación de vehículos híbridos/eléctricos.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Clasificación por rentabilidad
        const clasificaciones = ['Alta rentabilidad', 'Rentabilidad media', 'Baja rentabilidad', 'No rentable'];
        const cantidades = [
            {{ $vehiculos->where('roi', '>', 30)->count() }}, 
            {{ $vehiculos->where('roi', '>', 10)->where('roi', '<=', 30)->count() }}, 
            {{ $vehiculos->where('roi', '>', 0)->where('roi', '<=', 10)->count() }}, 
            {{ $vehiculos->where('roi', '<=', 0)->count() }}
        ];
        const colores = ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
        
        new Chart(document.getElementById('rentabilidadChart'), {
            type: 'pie',
            data: {
                labels: clasificaciones,
                datasets: [{
                    data: cantidades,
                    backgroundColor: colores,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Distribución por Clasificación de Rentabilidad'
                    }
                }
            }
        });
        
        // Distribución de ocupación
        const ocupaciones = [
            'Alta (>70%)', 
            'Media (40-70%)', 
            'Baja (10-40%)', 
            'Muy baja (<10%)'
        ];
        const cantidadesOcupacion = [
            {{ $vehiculos->where('tasa_ocupacion', '>', 70)->count() }}, 
            {{ $vehiculos->where('tasa_ocupacion', '>', 40)->where('tasa_ocupacion', '<=', 70)->count() }}, 
            {{ $vehiculos->where('tasa_ocupacion', '>', 10)->where('tasa_ocupacion', '<=', 40)->count() }}, 
            {{ $vehiculos->where('tasa_ocupacion', '<=', 10)->count() }}
        ];
        const coloresOcupacion = ['#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'];
        
        new Chart(document.getElementById('ocupacionChart'), {
            type: 'doughnut',
            data: {
                labels: ocupaciones,
                datasets: [{
                    data: cantidadesOcupacion,
                    backgroundColor: coloresOcupacion,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Distribución por Tasa de Ocupación'
                    }
                }
            }
        });
        
        // Búsqueda de vehículos
        document.getElementById('vehicleSearch').addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('.vehicle-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    });
</script>
@endpush
@endsection
