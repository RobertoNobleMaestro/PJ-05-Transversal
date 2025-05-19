@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Resumen Financiero - Sede de {{ $sede->nombre }}</h1>
            <p class="text-muted">Análisis de costes de personal de la sede</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Gasto Mensual Total</h5>
                    <p class="display-4">{{ number_format($totalMensual, 2, ',', '.') }} €</p>
                    <p class="mb-0">Previsión anual: {{ number_format($totalMensual * 12, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Próximo pago general</h5>
                    @php
                        $now = new DateTime();
                        $nextPayday = null;
                        $minDaysLeft = 31;
                        
                        // Encontrar el próximo día de pago más cercano
                        foreach([5, 10, 15, 20, 25, 30] as $day) {
                            $payday = new DateTime($now->format('Y-m-') . $day);
                            if ($payday < $now) {
                                $payday->modify('+1 month');
                            }
                            $daysLeft = $now->diff($payday)->days;
                            if ($daysLeft < $minDaysLeft) {
                                $minDaysLeft = $daysLeft;
                                $nextPayday = $payday;
                            }
                        }
                    @endphp
                    <p class="display-4">{{ $nextPayday ? $nextPayday->format('d/m/Y') : 'N/A' }}</p>
                    <p class="mb-0">En {{ $minDaysLeft }} días</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h5 class="card-title">Asalariados en la sede</h5>
                    @php
                        $totalAsalariados = 0;
                        foreach($estadisticas as $stat) {
                            $totalAsalariados += $stat->cantidad;
                        }
                    @endphp
                    <p class="display-4">{{ $totalAsalariados }}</p>
                    <p class="mb-0">Distribuidos en {{ count($estadisticas) }} roles diferentes</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Distribución por Rol</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Rol</th>
                                    <th class="text-center">Cantidad</th>
                                    <th class="text-center">Salario Promedio</th>
                                    <th class="text-end">Total Mensual</th>
                                    <th class="text-end">Total Anual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($estadisticas as $stat)
                                    <tr>
                                        <td>{{ $stat->nombre_rol }}</td>
                                        <td class="text-center">{{ $stat->cantidad }}</td>
                                        <td class="text-center">
                                            {{ number_format($stat->total_salarios / $stat->cantidad, 2, ',', '.') }} €
                                        </td>
                                        <td class="text-end">{{ number_format($stat->total_salarios, 2, ',', '.') }} €</td>
                                        <td class="text-end">{{ number_format($stat->total_salarios * 12, 2, ',', '.') }} €</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <th>Total</th>
                                    <th class="text-center">{{ $totalAsalariados }}</th>
                                    <th class="text-center">
                                        {{ number_format($totalMensual / $totalAsalariados, 2, ',', '.') }} €
                                    </th>
                                    <th class="text-end">{{ number_format($totalMensual, 2, ',', '.') }} €</th>
                                    <th class="text-end">{{ number_format($totalMensual * 12, 2, ',', '.') }} €</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Próximos Pagos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Día del mes</th>
                                    <th class="text-center">Asalariados</th>
                                    <th class="text-end">Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Agrupar por día de cobro
                                    $paydayStats = collect($estadisticas)
                                        ->groupBy('dia_cobro')
                                        ->map(function ($group) {
                                            return [
                                                'count' => $group->sum('cantidad'),
                                                'amount' => $group->sum('total_salarios')
                                            ];
                                        })
                                        ->sortKeys();
                                @endphp
                                
                                @for ($day = 1; $day <= 31; $day++)
                                    @if(isset($paydayStats[$day]))
                                        <tr>
                                            <td>Día {{ $day }}</td>
                                            <td class="text-center">{{ $paydayStats[$day]['count'] }}</td>
                                            <td class="text-end">{{ number_format($paydayStats[$day]['amount'], 2, ',', '.') }} €</td>
                                        </tr>
                                    @endif
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Distribución por Rol (Gráfico)</h5>
                </div>
                <div class="card-body">
                    <canvas id="roleDistribution" height="300"></canvas>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Distribución de Gastos (Gráfico)</h5>
                </div>
                <div class="card-body">
                    <canvas id="salaryDistribution" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Datos para el gráfico de distribución por rol
        const roleLabels = {!! json_encode($estadisticas->pluck('nombre_rol')->toArray()) !!};
        const roleCounts = {!! json_encode($estadisticas->pluck('cantidad')->toArray()) !!};
        
        // Gráfico de distribución por rol
        new Chart(document.getElementById('roleDistribution'), {
            type: 'pie',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: roleCounts,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Datos para el gráfico de distribución de gastos
        const salarySums = {!! json_encode($estadisticas->pluck('total_salarios')->toArray()) !!};
        
        // Gráfico de distribución de gastos
        new Chart(document.getElementById('salaryDistribution'), {
            type: 'doughnut',
            data: {
                labels: roleLabels,
                datasets: [{
                    data: salarySums,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
