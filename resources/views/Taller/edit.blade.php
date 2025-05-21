@extends('layouts.admin')

@section('title', 'Editar Mantenimiento')

@section('content')
<style>
    body, .edit-container {
        background: #fff;
        color: #222;
    }
    .edit-title {
        color: #9F17BD;
        font-weight: bold;
        margin-bottom: 1.5rem;
        letter-spacing: 1px;
    }
    .edit-container {
        background: #fff;
        border-radius: 20px;
        border: 2px solid #9F17BD;
        box-shadow: 0 6px 24px rgba(159,23,189,0.08), 0 2px 12px rgba(34,34,34,0.10);
        padding: 2.5rem 2.5rem 2.5rem 2.5rem;
        max-width: 600px;
        margin: 2.5rem auto;
        transition: box-shadow 0.25s;
    }
    .edit-container:hover {
        box-shadow: 0 10px 36px rgba(159,23,189,0.17), 0 4px 24px rgba(34,34,34,0.18);
    }
    .form-label {
        font-weight: 700;
        color: #7e138f;
        margin-bottom: .25rem;
        display: block;
        letter-spacing: .5px;
    }
    .form-select, select, .form-select.select-purple, .form-control {
        color: #222;
        border: 2px solid #9F17BD;
        background: #fff;
        border-radius: 6px;
        box-shadow: none;
        transition: border-color 0.2s;
    }
    .form-select.select-purple:focus, .form-control:focus {
        border-color: #7e138f;
        outline: 0;
        box-shadow: 0 0 0 0.15rem rgba(159,23,189,0.13);
    }
    .form-select.select-purple option {
        color: #222;
    }
    .btn-primary {
        background: linear-gradient(90deg, #9F17BD 70%, #7e138f 100%);
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: .5px;
        box-shadow: 0 2px 8px rgba(159,23,189,0.08);
        transition: background 0.2s, color 0.2s;
    }
    .btn-primary:hover, .btn-primary:focus {
        background: #fff;
        color: #9F17BD;
        border: 2px solid #9F17BD;
    }
    .btn-black {
        background: #222;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        letter-spacing: .5px;
        transition: background 0.2s, color 0.2s;
    }
    .btn-black:hover, .btn-black:focus {
        background: #fff;
        color: #222;
        border: 2px solid #222;
    }
    input[type="date"], input[type="time"] {
        color-scheme: light;
    }
    ::placeholder {
        color: #9F17BD;
        opacity: 1;
    }
    .admin-title, .edit-title {
        color: #9F17BD;
        font-weight: bold;
        margin-bottom: 1.5rem;
    }
    .form-label {
        font-weight: 600;
        color: #9F17BD;
        margin-bottom: .25rem;
        display: block;
    }
    .edit-container {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(159,23,189,0.08);
        padding: 2rem 2.5rem;
        max-width: 600px;
        margin: 0 auto;
    }
    .btn-primary {
        background-color: #9F17BD;
        border-color: #9F17BD;
    }
    .btn-primary:hover, .btn-primary:focus {
        background-color: #7e138f;
        border-color: #7e138f;
    }
</style>
<div class="edit-container mt-4">

    <h2>Editar Mantenimiento</h2>

    <form action="{{ route('Taller.update', $mantenimiento->id) }}" method="POST">
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

        <div class="d-flex gap-2 justify-content-end mt-4">
            <button type="submit" class="btn btn-outline-purple flex-grow-1" style="min-width:0;">Guardar cambios</button>
            <a href="{{ route('Taller.historial') }}" class="btn btn-outline-purple flex-grow-1 text-center" style="min-width:0;">Cancelar</a>
        </div>
    </form>
</div>
@endsection
