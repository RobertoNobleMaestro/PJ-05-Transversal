@extends('layouts.admin')

@section('title', 'Editar Vehículo')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/gestor-forms.css') }}">

<div class="add-user-container">
    <h1 class="form-title">Editar Vehículo</h1>
    <form id="editVehiculoForm" data-url="{{ route('gestor.vehiculos.update', $vehiculo->id_vehiculos) }}">
        @csrf
        @method('POST')
        <!-- Barra de pasos -->
        <div class="wizard-steps" style="display: flex; justify-content: center; margin-bottom: 2rem;">
            <div id="step-indicator-1" class="wizard-step active">
                <span class="wizard-circle">1</span>
                <span class="wizard-label">Datos del Vehículo</span>
            </div>
            <div class="wizard-line"></div>
            <div id="step-indicator-2" class="wizard-step">
                <span class="wizard-circle">2</span>
                <span class="wizard-label">Características</span>
            </div>
        </div>
        <!-- Paso 1: Datos del vehículo -->
        <div id="wizard-step-1" class="wizard-content">
            <div class="form-grid">
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

                    <div class="form-group">
                        <label for="parking_id" class="form-label">Parking</label>
                        <select class="form-control" id="parking_id" name="parking_id" required>
                            <option value="">Seleccionar parking</option>
                            @foreach($parkings as $parking)
                                <option value="{{ $parking->id }}" {{ $vehiculo->parking_id == $parking->id ? 'selected' : '' }}>{{ $parking->nombre }} ({{ $parking->lugar->nombre ?? 'Sin lugar' }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="imagenes" class="form-label">Imágenes del vehículo</label>
                <input type="file" class="form-control" id="imagenes" name="imagenes[]" multiple accept="image/*">
            </div>
            <div class="btn-container">
                <a href="{{ route('gestor.vehiculos') }}" class="btn btn-cancel">Cancelar</a>
                <button type="button" class="btn btn-submit" id="next-step">Siguiente</button>
            </div>
        </div>
        <!-- Paso 2: Características -->
        <div id="wizard-step-2" class="wizard-content" style="display:none;">
            <h2 class="form-title">Características del Vehículo</h2>
            <div class="form-grid">
                <div>
                    <div class="form-group">
                        <label for="techo" class="form-label">¿Tiene techo solar?</label>
                        <select class="form-control" id="techo" name="techo" required>
                            <option value="">Seleccionar</option>
                            <option value="1" {{ optional($vehiculo->caracteristicas)->techo == 1 ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ optional($vehiculo->caracteristicas)->techo === 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transmision" class="form-label">Transmisión</label>
                        <input type="text" class="form-control" id="transmision" name="transmision" value="{{ optional($vehiculo->caracteristicas)->transmision }}" required>
                    </div>
                    <div class="form-group">
                        <label for="num_puertas" class="form-label">Número de puertas</label>
                        <input type="number" class="form-control" id="num_puertas" name="num_puertas" min="2" max="6" value="{{ optional($vehiculo->caracteristicas)->num_puertas }}" required>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label for="etiqueta_medioambiental" class="form-label">Etiqueta medioambiental</label>
                        <input type="text" class="form-control" id="etiqueta_medioambiental" name="etiqueta_medioambiental" value="{{ optional($vehiculo->caracteristicas)->etiqueta_medioambiental }}" required>
                    </div>
                    <div class="form-group">
                        <label for="aire_acondicionado" class="form-label">¿Tiene aire acondicionado?</label>
                        <select class="form-control" id="aire_acondicionado" name="aire_acondicionado" required>
                            <option value="">Seleccionar</option>
                            <option value="1" {{ optional($vehiculo->caracteristicas)->aire_acondicionado == 1 ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ optional($vehiculo->caracteristicas)->aire_acondicionado === 0 ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacidad_maletero" class="form-label">Capacidad del maletero (litros)</label>
                        <input type="number" class="form-control" id="capacidad_maletero" name="capacidad_maletero" min="0" value="{{ optional($vehiculo->caracteristicas)->capacidad_maletero }}" required>
                    </div>
                </div>
            </div>
            <div class="btn-container">
                <button type="button" class="btn btn-secondary" id="prev-step">Anterior</button>
                <button type="button" class="btn btn-submit" onclick="updateVehiculo({{ $vehiculo->id_vehiculos }})">Actualizar</button>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="vehicles-index" content="{{ route('gestor.vehiculos') }}">
<script src="{{ asset('js/gestor-edit-vehiculo.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const step1 = document.getElementById('wizard-step-1');
        const step2 = document.getElementById('wizard-step-2');
        document.getElementById('next-step').onclick = function() {
            step1.style.display = 'none';
            step2.style.display = 'block';
        };
        document.getElementById('prev-step').onclick = function() {
            step2.style.display = 'none';
            step1.style.display = 'block';
        };
    });
</script>
@endsection
<style>
    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #bbb;
        font-weight: 500;
    }
    .wizard-step.active {
        color: #222;
    }
    .wizard-circle {
        display: inline-block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #333;
        color: #fff;
        text-align: center;
        line-height: 32px;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .wizard-step.active .wizard-circle {
        background: #9F17BD;
    }
    .wizard-label {
        font-size: 15px;
    }
    .wizard-line {
        width: 80px;
        height: 2px;
        background: #eee;
        margin: 0 16px 18px 16px;
        align-self: center;
    }
    </style>