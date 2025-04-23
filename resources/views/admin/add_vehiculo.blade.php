@extends('layouts.admin')

@section('title', 'Añadir Vehículo')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">

<div class="add-vehicle-container">
    <h1 class="form-title">Añadir Nuevo Vehículo</h1>
    <form id="addVehiculoForm" data-url="{{ route('admin.vehiculos.store') }}">
        @csrf
        <div class="form-grid">
            <!-- Columna izquierda -->
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
            
            <!-- Columna derecha -->
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
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="seguro_incluido" name="seguro_incluido">
                        <label for="seguro_incluido" class="form-check-label">Seguro incluido</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="disponibilidad" name="disponibilidad" checked>
                        <label for="disponibilidad" class="form-check-label">Disponible</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('admin.vehiculos') }}" class="cancel-btn">Cancelar</a>
            <button type="button" class="submit-btn" onclick="createVehiculo()">Enviar</button>
        </div>
    </form>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="vehicles-index" content="{{ route('admin.vehiculos') }}">

<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-add-vehiculo.js') }}"></script>
@endsection
