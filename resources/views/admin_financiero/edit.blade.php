@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Asalariado</h1>
            <p class="text-muted">Modificación de datos salariales para {{ $asalariado->usuario->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Datos del Asalariado</h5>
        </div>
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-4">
                    <h6 class="text-muted">Información del empleado</h6>
                    <p><strong>Nombre:</strong> {{ $asalariado->usuario->nombre }}</p>
                    <p><strong>Email:</strong> {{ $asalariado->usuario->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $asalariado->usuario->telefono }}</p>
                    <p><strong>DNI:</strong> {{ $asalariado->usuario->dni }}</p>
                    <p><strong>Rol:</strong> {{ $asalariado->usuario->role->formatted_name }}</p>
                </div>
                <div class="col-md-8">
                    <form action="{{ route('admin.financiero.update', $asalariado->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="salario" class="form-label">Salario (€)</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="salario" name="salario" 
                                   value="{{ old('salario', $asalariado->salario) }}" required>
                            @error('salario')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="dia_cobro" class="form-label">Día de cobro</label>
                            <select class="form-control" id="dia_cobro" name="dia_cobro" required>
                                @for ($i = 1; $i <= 31; $i++)
                                    <option value="{{ $i }}" {{ old('dia_cobro', $asalariado->dia_cobro) == $i ? 'selected' : '' }}>
                                        {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            @error('dia_cobro')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="parking_id" class="form-label">Parking asignado</label>
                            <select class="form-control" id="parking_id" name="parking_id" required>
                                @foreach($parkings as $parking)
                                    <option value="{{ $parking->id }}" {{ old('parking_id', $asalariado->parking_id) == $parking->id ? 'selected' : '' }}>
                                        {{ $parking->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('parking_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
