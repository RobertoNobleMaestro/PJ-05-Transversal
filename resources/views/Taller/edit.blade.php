@extends('layouts.admin')

@section('title', 'Editar Mantenimiento')

@section('content')
<div class="container mt-4">
    <h2>Editar Mantenimiento</h2>

    <form action="{{ route('taller.update', $mantenimiento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="vehiculo_id" class="form-label">Vehículo</label>
            <select name="vehiculo_id" class="form-select" required>
                @foreach($vehiculos as $vehiculo)
                    <option value="{{ $vehiculo->id_vehiculos }}" {{ $mantenimiento->vehiculo_id == $vehiculo->id_vehiculos ? 'selected' : '' }}>
                        {{ $vehiculo->modelo }} - {{ $vehiculo->placa }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="taller_id" class="form-label">Taller</label>
            <select name="taller_id" class="form-select" required>
                @foreach($talleres as $taller)
                    <option value="{{ $taller->id }}" {{ $mantenimiento->taller_id == $taller->id ? 'selected' : '' }}>
                        {{ $taller->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="fecha_programada" class="form-label">Fecha</label>
            <input type="date" name="fecha_programada" class="form-control" value="{{ old('fecha_programada', $mantenimiento->fecha_programada->format('Y-m-d')) }}" required>
        </div>

        <div class="mb-3">
            <label for="hora_programada" class="form-label">Hora</label>
            <input type="time" name="hora_programada" class="form-control" value="{{ old('hora_programada', $mantenimiento->hora_programada) }}" required>
        </div>

        <div class="mb-3">
            <label for="estado" class="form-label">Estado</label>
            <select name="estado" class="form-select" required>
                <option value="pendiente" {{ $mantenimiento->estado === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="completado" {{ $mantenimiento->estado === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="cancelado" {{ $mantenimiento->estado === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Guardar cambios</button>
        <a href="{{ route('taller.historial') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
