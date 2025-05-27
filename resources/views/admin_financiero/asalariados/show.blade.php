@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Detalles del Asalariado</h1>
            <p class="text-muted">Información completa del trabajador</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.asalariados.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #9F17BD;">
                    <h5 class="mb-0">Perfil del Empleado</h5>
                </div>
                <div class="card-body text-center">
                    @if (isset($asalariado) && $asalariado->usuario && $asalariado->usuario->foto_perfil)
                        <img src="{{ asset('storage/' . $asalariado->usuario->foto_perfil) }}" alt="Foto de perfil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px; background-color: #9F17BD;">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                    @endif
                    <h4 class="mb-0">{{ isset($asalariado) && $asalariado->usuario ? $asalariado->usuario->nombre : 'Sin nombre' }}</h4>
                    <p class="text-muted mb-3">{{ isset($asalariado) && $asalariado->usuario ? $asalariado->usuario->email : 'Sin email' }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.asalariados.edit', $asalariado->id) }}" class="btn text-white mb-2" style="background-color: #9F17BD;">
                            <i class="fas fa-edit"></i> Editar información
                        </a>
                        <a href="{{ route('admin.asalariados.nomina', $asalariado->id) }}" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-file-invoice-dollar"></i> Generar nómina
                        </a>
                        @if($asalariado->estado == 'alta')
                            <form action="{{ route('admin.asalariados.baja', $asalariado->id) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('¿Está seguro que desea dar de baja a este asalariado?')">
                                    <i class="fas fa-user-slash"></i> Dar de baja
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.asalariados.alta', $asalariado->id) }}" method="POST" class="d-grid">
                                @csrf
                                <button type="submit" class="btn btn-outline-success" onclick="return confirm('¿Está seguro que desea dar de alta a este asalariado?')">
                                    <i class="fas fa-user-check"></i> Reactivar empleado
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-light">
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header text-white" style="background-color: #9F17BD;">
                    <h5 class="mb-0">Estado Laboral</h5>
                </div>
                <div class="card-body p-3">
                    <div class="text-center mb-3">
                        <div class="badge fs-5 mb-2 {{ isset($asalariado) && $asalariado->estado == 'alta' ? 'bg-success' : 'bg-danger' }} p-2 w-100">
                            {{ $asalariado->estado == 'alta' ? 'EMPLEADO ACTIVO' : 'EMPLEADO INACTIVO' }}
                        </div>
                        <p class="text-muted small">
                            @if(isset($asalariado) && $asalariado->estado == 'alta')
                                Este empleado está actualmente en alta y recibe su salario normalmente.
                            @else
                                Este empleado está dado de baja y no recibe salario actualmente.
                            @endif
                        </p>
                    </div>
                    
                    <div class="progress mb-3" style="height: 25px;">
                        @if($asalariado->estado == 'alta')
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Activo al 100%</div>
                        @elseif(isset($asalariado) && isset($asalariado->dias_trabajados) && $asalariado->dias_trabajados > 0)
                            @php 
                                $porcentaje = min(100, round(($asalariado->dias_trabajados / 30) * 100)); 
                            @endphp
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $porcentaje }}%" aria-valuenow="{{ $porcentaje }}" aria-valuemin="0" aria-valuemax="100">{{ $porcentaje }}% del mes</div>
                        @else
                            <div class="progress-bar bg-danger" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100">Inactivo</div>
                        @endif
                    </div>
                    
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="mb-1">Días trabajados</h6>
                                <h4 class="mb-0">{{ isset($asalariado) && isset($asalariado->dias_trabajados) ? $asalariado->dias_trabajados : 0 }}</h4>
                                <small class="text-muted">en el mes actual</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2">
                                <h6 class="mb-1">Antigüedad</h6>
                                <h4 class="mb-0">{{ isset($asalariado) && isset($asalariado->hiredate) && $asalariado->hiredate ? (is_object($asalariado->hiredate) ? $asalariado->hiredate->diffInMonths(now()) : (is_string($asalariado->hiredate) ? \Carbon\Carbon::parse($asalariado->hiredate)->diffInMonths(now()) : 0)) : 0 }}</h4>
                                <small class="text-muted">meses en la empresa</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header text-white" style="background-color: #9F17BD;">
                    <h5 class="mb-0">Datos Laborales</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Salario mensual:</span>
                            <span>{{ isset($asalariado) && isset($asalariado->salario) ? number_format($asalariado->salario, 2, ',', '.') : '0,00' }} €</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Fecha de contratación:</span>
                            <span>{{ isset($asalariado) && isset($asalariado->hiredate) && $asalariado->hiredate ? (is_object($asalariado->hiredate) ? $asalariado->hiredate->format('d/m/Y') : (is_string($asalariado->hiredate) ? \Carbon\Carbon::parse($asalariado->hiredate)->format('d/m/Y') : 'No disponible')) : 'No disponible' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Día de cobro:</span>
                            <span>{{ isset($asalariado) && isset($asalariado->dia_cobro) ? $asalariado->dia_cobro : 1 }} de cada mes</span>
                        </li>
                        @if(isset($asalariado) && isset($asalariado->estado) && $asalariado->estado == 'baja')
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Salario proporcional:</span>
                            @php 
                                $diasTrabajados = isset($asalariado) && isset($asalariado->dias_trabajados) ? $asalariado->dias_trabajados : 0;
                                $salario = isset($asalariado) && isset($asalariado->salario) ? $asalariado->salario : 0;
                                $proporcion = $diasTrabajados / 30;
                                $salarioProporcional = $salario * $proporcion;
                            @endphp
                            <span>{{ number_format($salarioProporcional, 2, ',', '.') }} €</span>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Sede:</span>
                            <span>
                                @if(isset($asalariado) && isset($asalariado->id_lugar) && $asalariado->id_lugar && isset($sede) && $sede)
                                    {{ $sede->nombre }}
                                @elseif(isset($parking) && $parking && isset($parking->lugar) && $parking->lugar)
                                    {{ $parking->lugar->nombre }}
                                @else
                                    No asignada
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Parking asignado:</span>
                            <span>{{ isset($asalariado) && isset($asalariado->parking) && $asalariado->parking ? $asalariado->parking->nombre : 'Sin asignar' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Estado Laboral</h6>
                                </div>
                                <div class="card-body">
                                    <h4 class="text-success">EMPLEADO ACTIVO</h4>
                                    <p class="text-muted">Este empleado está actualmente en alta y recibe su salario normalmente.</p>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 text-center mb-3">
                                                <h5 class="text-primary">Activo al 100%</h5>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 text-center">
                                                <h3>{{ min(\Carbon\Carbon::now()->day, 22) }}</h3>
                                                <p class="mb-0">Días trabajados</p>
                                                <small class="text-muted">en el mes actual</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3 text-center border-top pt-3">
                                        <h5>Antigüedad</h5>
                                        <h3>12</h3>
                                        <p class="text-muted mb-0">meses en la empresa</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Datos Laborales</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold"><i class="fas fa-money-bill-wave text-muted me-2"></i> Salario mensual:</span>
                                            <span>1.688,00 €</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold"><i class="fas fa-calendar-alt text-muted me-2"></i> Fecha de contratación:</span>
                                            <span>06/06/2024</span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold"><i class="fas fa-calendar-check text-muted me-2"></i> Día de cobro:</span>
                                            <span>5 de cada mes</span>
                                        </li>

                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="fw-bold"><i class="fas fa-parking text-muted me-2"></i> Parking asignado:</span>
                                            <span>Parking Barcelona Centro</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Simulación salarial anual</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-sm">
                                    <thead>
                                        <tr>
                                            <th>Concepto</th>
                                            <th class="text-end">Importe</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Salario base mensual</td>
                                            <td class="text-end">{{ isset($asalariado) && isset($asalariado->salario) ? number_format($asalariado->salario, 2, ',', '.') : '0,00' }} €</td>
                                        </tr>
                                        <tr>
                                            <td>Salario anual (12 meses)</td>
                                            <td class="text-end">{{ isset($asalariado) && isset($asalariado->salario) ? number_format($asalariado->salario * 12, 2, ',', '.') : '0,00' }} €</td>
                                        </tr>
                                        <tr>
                                            <td>Pagas extra (2)</td>
                                            <td class="text-end">{{ isset($asalariado) && isset($asalariado->salario) ? number_format($asalariado->salario * 2, 2, ',', '.') : '0,00' }} €</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td class="fw-bold">Total anual bruto</td>
                                            <td class="text-end fw-bold">{{ isset($asalariado) && isset($asalariado->salario) ? number_format($asalariado->salario * 14, 2, ',', '.') : '0,00' }} €</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .badge.fs-6 {
        font-size: 0.9rem !important;
    }
    .card {
        overflow: hidden;
        border: none;
    }
    .card-header {
        border-bottom: 0;
    }
    .list-group-item {
        border-left: 0;
        border-right: 0;
    }
</style>
@endsection
