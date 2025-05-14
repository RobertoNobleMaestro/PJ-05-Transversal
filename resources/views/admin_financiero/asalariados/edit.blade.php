@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Asalariado</h1>
            <p class="text-muted">Modificar información de salario y parking</p>
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
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #9F17BD; color: white;">
                    <h5 class="mb-0">Información del empleado</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if ($usuario->foto_perfil)
                            <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 120px; height: 120px; background-color: #9F17BD;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        @endif
                        <h5>{{ $usuario->nombre }}</h5>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Rol:</span>
                            <span class="badge {{ $usuario->role->nombre == 'gestor' ? 'bg-primary' : ($usuario->role->nombre == 'mecanico' ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ ucfirst($usuario->role->nombre) }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Email:</span>
                            <span>{{ $usuario->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">DNI:</span>
                            <span>{{ $usuario->dni }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Sede:</span>
                            <span>{{ $sede->nombre }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header" style="background-color: #9F17BD; color: white;">
                    <h5 class="mb-0">Datos salariales y asignación</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('asalariados.update', $asalariado->id) }}" class="p-3">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <label for="salario" class="col-md-4 col-form-label">Salario mensual (€)</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input id="salario" type="number" step="0.01" min="0" class="form-control @error('salario') is-invalid @enderror" name="salario" value="{{ old('salario', $asalariado->salario) }}" required autofocus>
                                    <span class="input-group-text">€</span>
                                    @error('salario')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Ingrese el salario bruto mensual</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="dia_cobro" class="col-md-4 col-form-label">Día de cobro</label>
                            <div class="col-md-8">
                                <input id="dia_cobro" type="number" min="1" max="31" class="form-control @error('dia_cobro') is-invalid @enderror" name="dia_cobro" value="{{ old('dia_cobro', $asalariado->dia_cobro) }}" required>
                                <small class="form-text text-muted">Día del mes en que se realiza el pago (1-31)</small>
                                @error('dia_cobro')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="parking_id" class="col-md-4 col-form-label">Parking asignado</label>
                            <div class="col-md-8">
                                <select id="parking_id" class="form-select @error('parking_id') is-invalid @enderror" name="parking_id" required>
                                    @foreach ($parkings as $parking)
                                        <option value="{{ $parking->id }}" {{ (old('parking_id', $asalariado->parking_id) == $parking->id) ? 'selected' : '' }}>
                                            {{ $parking->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Parking donde trabaja el empleado (solo dentro de su sede)</small>
                                @error('parking_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-4">
                                <a href="{{ route('asalariados.index') }}" class="btn btn-outline-secondary me-2">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn text-white" style="background-color: #9F17BD;">
                                    <i class="fas fa-save me-1"></i> Guardar cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #9F17BD;
        box-shadow: 0 0 0 0.25rem rgba(159, 23, 189, 0.25);
    }
</style>
@endsection
