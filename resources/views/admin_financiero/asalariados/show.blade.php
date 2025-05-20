@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Detalles del Asalariado</h1>
            <p class="text-muted">Información completa del trabajador</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('asalariados.index') }}" class="btn btn-secondary">
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

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #9F17BD;">
                    <h5 class="mb-0">Perfil del Empleado</h5>
                </div>
                <div class="card-body text-center">
                    @if ($usuario->foto_perfil)
                        <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil" class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 150px; height: 150px; background-color: #9F17BD;">
                            <i class="fas fa-user fa-4x text-white"></i>
                        </div>
                    @endif
                    <h4 class="mb-0">{{ $usuario->nombre }}</h4>
                    <p class="text-muted mb-3">{{ $usuario->email }}</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('asalariados.edit', $asalariado->id) }}" class="btn text-white" style="background-color: #9F17BD;">
                            <i class="fas fa-edit"></i> Editar información
                        </a>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Rol:</span>
                        <span class="badge {{ $usuario->role->nombre == 'gestor' ? 'bg-primary' : ($usuario->role->nombre == 'mecanico' ? 'bg-warning text-dark' : 'bg-success') }}">
                            {{ ucfirst($usuario->role->nombre) == 'Admin_financiero' ? 'Admin Financiero' : ucfirst($usuario->role->nombre) }}
                        </span>
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
                            <span class="fw-bold">Salario:</span>
                            <span class="badge bg-success fs-6">{{ number_format($asalariado->salario, 2, ',', '.') }} €</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Día de cobro:</span>
                            <span>Día {{ $asalariado->dia_cobro }} de cada mes</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Parking asignado:</span>
                            <span>{{ $parking->nombre }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Sede:</span>
                            <span>{{ $sede->nombre }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #9F17BD;">
                    <h5 class="mb-0">Información Detallada</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Información Personal</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-id-card text-muted me-2"></i>
                                            <span class="fw-bold">DNI:</span> {{ $usuario->dni }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-phone text-muted me-2"></i>
                                            <span class="fw-bold">Teléfono:</span> {{ $usuario->telefono }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-calendar text-muted me-2"></i>
                                            <span class="fw-bold">Fecha nacimiento:</span> {{ $usuario->fecha_nacimiento ? $usuario->fecha_nacimiento->format('d/m/Y') : 'No disponible' }}
                                        </li>
                                        <li>
                                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                            <span class="fw-bold">Dirección:</span> {{ $usuario->direccion }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Información Adicional</h6>
                                </div>
                                <div class="card-body">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-car text-muted me-2"></i>
                                            <span class="fw-bold">Licencia:</span> {{ $usuario->licencia_conducir ?: 'No disponible' }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-building text-muted me-2"></i>
                                            <span class="fw-bold">Parking:</span> {{ $parking->nombre }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                            <span class="fw-bold">Ciudad:</span> {{ $sede->nombre }}
                                        </li>
                                        <li>
                                            <i class="fas fa-clock text-muted me-2"></i>
                                            <span class="fw-bold">Usuario desde:</span> {{ $usuario->created_at->format('d/m/Y') }}
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
                                            <td class="text-end">{{ number_format($asalariado->salario, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td>Salario anual (12 meses)</td>
                                            <td class="text-end">{{ number_format($asalariado->salario * 12, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr>
                                            <td>Pagas extra (2)</td>
                                            <td class="text-end">{{ number_format($asalariado->salario * 2, 2, ',', '.') }} €</td>
                                        </tr>
                                        <tr class="table-success">
                                            <td class="fw-bold">Total anual bruto</td>
                                            <td class="text-end fw-bold">{{ number_format($asalariado->salario * 14, 2, ',', '.') }} €</td>
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
