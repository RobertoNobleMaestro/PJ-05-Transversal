@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Panel de Administración Financiera - {{ $sede->nombre }}</h1>
            <p class="text-muted">Gestión de trabajadores asalariados de la sede</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('asalariados.index') }}" class="btn btn-success me-2">
                <i class="fas fa-users"></i> Gestión de Asalariados
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Listado de Asalariados - Sede de {{ $sede->nombre }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Salario</th>
                            <th>Día de Cobro</th>
                            <th>Parking Asignado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asalariados as $asalariado)
                            <tr>
                                <td>
                                    {{ $asalariado->usuario->nombre }}
                                    @if($asalariado->usuario->id_usuario === Auth::id())
                                        <span class="badge bg-info">Tú</span>
                                    @endif
                                </td>
                                <td>{{ $asalariado->usuario->role->nombre_rol }}</td>
                                <td>{{ number_format($asalariado->salario, 2, ',', '.') }} €</td>
                                <td>Día {{ $asalariado->dia_cobro }}</td>
                                <td>{{ $asalariado->parking->nombre }}</td>
                                <td>
                                    <a href="{{ route('admin.financiero.show', $asalariado->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.financiero.edit', $asalariado->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No hay asalariados registrados en esta sede.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Total Asalariados</h5>
                    <p class="card-text display-4">{{ isset($asalariados) ? $asalariados->count() : 0 }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Gasto Mensual Total</h5>
                    <p class="card-text display-4">{{ isset($asalariados) ? number_format($asalariados->sum('salario'), 2, ',', '.') : '0,00' }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Salario Promedio</h5>
                    <p class="card-text display-4">
                        {{ isset($asalariados) && $asalariados->count() > 0 ? number_format($asalariados->avg('salario'), 2, ',', '.') : '0,00' }} €
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
