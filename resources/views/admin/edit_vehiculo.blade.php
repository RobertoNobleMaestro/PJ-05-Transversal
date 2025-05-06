@extends('layouts.admin')

@section('title', 'Editar Vehículo')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">

<div class="add-vehicle-container">
    <h1 class="form-title">Editar Vehículo</h1>
    <form id="editVehiculoForm" data-url="{{ route('gestor.vehiculos.update', $vehiculo->id_vehiculos) }}">
        @csrf
        @method('POST')
        <div class="form-grid">
            <!-- Columna izquierda -->
            <div>
                <div class="form-group">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" value="{{ $vehiculo->marca }}" required>
                </div>

                <div class="form-group">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" value="{{ $vehiculo->modelo }}" required>
                </div>

                <div class="form-group">
                    <label for="año" class="form-label">Año</label>
                    <input type="number" class="form-control" id="año" name="año" value="{{ $vehiculo->año }}" min="1900" max="{{ date('Y') + 1 }}" required>
                </div>



                <div class="form-group">
                    <label for="precio_dia" class="form-label">Precio por día</label>
                    <input type="number" class="form-control" id="precio_dia" name="precio_dia" value="{{ $vehiculo->precio_dia }}" step="0.01" min="0" required>
                </div>
            </div>
            
            <!-- Columna derecha -->
            <div>
                <div class="form-group">
                    <label for="kilometraje" class="form-label">Kilometraje</label>
                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" value="{{ $vehiculo->kilometraje }}" min="0" required>
                </div>

                <div class="form-group">
                    <label for="id_lugar" class="form-label">Lugar</label>
                    <select class="form-control" id="id_lugar" name="id_lugar" required>
                        <option value="">Seleccionar lugar</option>
                        @foreach($lugares as $lugar)
                            <option value="{{ $lugar->id_lugar }}" {{ $vehiculo->id_lugar == $lugar->id_lugar ? 'selected' : '' }}>{{ $lugar->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_tipo" class="form-label">Tipo de vehículo</label>
                    <select class="form-control" id="id_tipo" name="id_tipo" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach($tipo as $tipo)
                            <option value="{{ $tipo->id_tipo }}" {{ $vehiculo->id_tipo == $tipo->id_tipo ? 'selected' : '' }}>{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('gestor.vehiculos') }}" class="btn btn-cancel">Cancelar</a>
            <button type="button" class="btn btn-submit" onclick="updateVehiculo({{ $vehiculo->id_vehiculos }})">Actualizar</button>
        </div>
    </form>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="vehicles-index" content="{{ route('gestor.vehiculos') }}">

<script src="{{ asset('js/gestor-edit-vehiculo.js') }}"></script>
