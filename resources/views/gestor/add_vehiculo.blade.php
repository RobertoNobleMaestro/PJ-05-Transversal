@extends('layouts.admin')

@section('title', 'Añadir Vehículo')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/gestor-forms.css') }}">

<div class="add-user-container">
    <h1 class="form-title">Añadir Nuevo Vehículo</h1>
    <form id="addVehiculoForm" data-url="{{ route('gestor.vehiculos.store') }}">
        @csrf
        <div class="form-grid">
            <div>
                <div class="form-group">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" required>
                </div>

                <div class="form-group">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" required>
                </div>

                <div class="form-group">
                    <label for="año" class="form-label">Año</label>
                    <input type="number" class="form-control" id="año" name="año" min="1900" max="{{ date('Y') + 1 }}" required>
                </div>

                <div class="form-group">
                    <label for="precio_dia" class="form-label">Precio por día</label>
                    <input type="number" class="form-control" id="precio_dia" name="precio_dia" step="0.01" min="0" required>
                </div>
            </div>
            
            <div>
                <div class="form-group">
                    <label for="kilometraje" class="form-label">Kilometraje</label>
                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" min="0" required>
                </div>

                <div class="form-group">
                    <label for="id_lugar" class="form-label">Lugar</label>
                    <select class="form-control" id="id_lugar" name="id_lugar" required>
                        <option value="">Seleccionar lugar</option>
                        @foreach($lugares as $lugar)
                            <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_tipo" class="form-label">Tipo de vehículo</label>
                    <select class="form-control" id="id_tipo" name="id_tipo" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach($tipo as $tipo)
                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('gestor.vehiculos') }}" class="btn btn-cancel">Cancelar</a>
            <button type="button" class="btn btn-submit" onclick="createVehiculo()">Enviar</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="vehicles-index" content="{{ route('gestor.vehiculos') }}">
<script src="{{ asset('js/gestor-add-vehiculo.js') }}"></script>
@endsection
