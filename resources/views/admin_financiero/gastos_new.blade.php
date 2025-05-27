@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gastos</h1>
            <p class="text-muted">Gestión de gastos según esquema contable estudiantil</p>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.financiero.gastos', ['tipo' => 'mensual']) }}" class="btn {{ $tipoVista == 'mensual' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Mensual
                </a>
                <a href="{{ route('admin.financiero.gastos', ['tipo' => 'trimestral']) }}" class="btn {{ $tipoVista == 'trimestral' ? 'btn-primary' : 'btn-outline-primary' }}">
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

    <!-- Resumen de gastos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Resumen de Gastos</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Total Gastos -->
                <div class="col-md-4 mb-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-3x"></i>
                                </div>
                                <div class="col text-end">
                                    <h4 class="mb-0">{{ number_format($totalGastos, 2, ',', '.') }} €</h4>
                                    <div>Total Gastos {{ $tipoVista == 'mensual' ? 'Mensuales' : 'Trimestrales' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Piezas (Reparaciones) -->
                <div class="col-md-4 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-tools fa-3x"></i>
                                </div>
                                <div class="col text-end">
                                    <h4 class="mb-0">{{ number_format($categorias['Piezas'] ?? 0, 2, ',', '.') }} €</h4>
                                    <div>Gastos en Piezas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Salarios -->
                <div class="col-md-4 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-user-tie fa-3x"></i>
                                </div>
                                <div class="col text-end">
                                    <h4 class="mb-0">{{ number_format($categorias['Asalariados'] ?? 0, 2, ',', '.') }} €</h4>
                                    <div>Salarios Empleados</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de gastos según esquema -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Detalle de Gastos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Categoría</th>
                            <th>Descripción</th>
                            <th>Importe (€)</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Piezas (reparaciones) -->
                        @if(isset($gastosPorTipo['Piezas']) && count($gastosPorTipo['Piezas']) > 0)
                            <tr class="table-warning">
                                <th colspan="4">Piezas para Reparaciones</th>
                            </tr>
                            @foreach($gastosPorTipo['Piezas'] as $gasto)
                            <tr>
                                <td>
                                    <i class="fas fa-tools text-warning me-2"></i> Piezas
                                </td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>{{ number_format($gasto->importe, 2, ',', '.') }} €</td>
                                <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="2">Subtotal Piezas</td>
                                <td>{{ number_format($categorias['Piezas'] ?? 0, 2, ',', '.') }} €</td>
                                <td></td>
                            </tr>
                        @endif

                        <!-- 2. Salarios empleados -->
                        @if(isset($gastosPorTipo['Asalariados']) && count($gastosPorTipo['Asalariados']) > 0)
                            <tr class="table-info">
                                <th colspan="4">Salarios Empleados</th>
                            </tr>
                            @foreach($gastosPorTipo['Asalariados'] as $gasto)
                            <tr>
                                <td>
                                    <i class="fas fa-user-tie text-info me-2"></i> Salarios
                                </td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>{{ number_format($gasto->importe, 2, ',', '.') }} €</td>
                                <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="2">Subtotal Salarios</td>
                                <td>{{ number_format($categorias['Asalariados'] ?? 0, 2, ',', '.') }} €</td>
                                <td></td>
                            </tr>
                        @endif

                        <!-- 3. Pagos a choferes -->
                        @if(isset($gastosPorTipo['Choferes']) && count($gastosPorTipo['Choferes']) > 0)
                            <tr class="table-primary">
                                <th colspan="4">Pagos a Choferes</th>
                            </tr>
                            @foreach($gastosPorTipo['Choferes'] as $gasto)
                            <tr>
                                <td>
                                    <i class="fas fa-id-card text-primary me-2"></i> Choferes
                                </td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td>{{ number_format($gasto->importe, 2, ',', '.') }} €</td>
                                <td>{{ \Carbon\Carbon::parse($gasto->fecha)->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                            <tr class="table-light">
                                <td colspan="2">Subtotal Choferes</td>
                                <td>{{ number_format($categorias['Choferes'] ?? 0, 2, ',', '.') }} €</td>
                                <td></td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th colspan="2">TOTAL GASTOS</th>
                            <th>{{ number_format($totalGastos, 2, ',', '.') }} €</th>
                            <th>{{ $tipoVista == 'mensual' ? 'Mensual' : 'Trimestral' }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Notas sobre gastos -->
    <div class="card mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Notas sobre Gastos</h5>
        </div>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item">
                    <i class="fas fa-tools text-warning me-2"></i>
                    <strong>Piezas:</strong> Gastos en materiales y repuestos para reparación de vehículos
                </li>
                <li class="list-group-item">
                    <i class="fas fa-user-tie text-info me-2"></i>
                    <strong>Asalariados:</strong> Gastos mensuales fijos en personal administrativo
                </li>
                <li class="list-group-item">
                    <i class="fas fa-id-card text-primary me-2"></i>
                    <strong>Choferes:</strong> Pagos a conductores externos por servicios prestados
                </li>
                <li class="list-group-item bg-light">
                    <i class="fas fa-info-circle text-secondary me-2"></i>
                    <strong>Ejemplo:</strong> La compra de neumáticos por 200€ se registra como gasto directo en el momento de la reparación.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
