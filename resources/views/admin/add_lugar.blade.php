@extends('layouts.admin')

@section('title', 'Añadir Lugar')

@section('content')
<!-- Añadir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-map.css') }}">

<div class="add-place-container">
    <h1 class="form-title">Añadir Nuevo Lugar</h1>
    <form id="addLugarForm" data-url="{{ route('admin.lugares.store') }}">
        @csrf
        <div class="form-grid">
            <!-- Columna izquierda -->
            <div>
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre del Lugar</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                    <div id="nombre-error" class="error-feedback"></div>
                </div>

                <div class="form-group">
                    <label for="direccion" class="form-label">Dirección</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" required>
                    <div id="direccion-error" class="error-feedback"></div>
                </div>
            </div>
            
            <!-- Columna derecha -->
            <div>
                <div class="form-group">
                    <label for="latitud" class="form-label">Latitud</label>
                    <input type="number" step="any" class="form-control" id="latitud" name="latitud" value="40.416775" required>
                    <div id="latitud-error" class="error-feedback"></div>
                </div>

                <div class="form-group">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="number" step="any" class="form-control" id="longitud" name="longitud" value="-3.703790" required>
                    <div id="longitud-error" class="error-feedback"></div>
                </div>
            </div>
        </div>
        
        <!-- Mapa para seleccionar ubicación -->
        <div class="form-group full-width">
            <label class="form-label">Seleccionar ubicación en el mapa</label>
            <div id="map"></div>
            <small class="form-text">Haz clic en el mapa para seleccionar la ubicación. Las coordenadas se actualizarán automáticamente.</small>
        </div>

        <div class="btn-container">
            <a href="{{ route('admin.lugares') }}" class="cancel-btn">Cancelar</a>
            <button type="button" class="submit-btn" onclick="createLugar()">Enviar</button>
        </div>
    </form>
</div>
@endsection

<!-- Añadir JS de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="places-index" content="{{ route('admin.lugares') }}">

<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-add-lugar.js') }}"></script>
