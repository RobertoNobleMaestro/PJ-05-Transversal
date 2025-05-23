@extends('layouts.admin')

@section('title', 'Gestión de Parkings')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="admin-container">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-sidebar" id="sidebar">
        <div style="position: fixed;width: 220px;">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('gestor.vehiculos') }}"
                        class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i>
                        Vehículos</a></li>
                <li><a href="{{ route('gestor.historial') }}"
                class="{{ request()->routeIs('gestor.historial') ? 'active' : '' }}"><i
                    class="fas fa-history"></i>Historial</a></li>
                                        <li><a href="{{ route('gestor.parking.index') }}"
                class="{{ request()->routeIs('gestor.parking.index') ? 'active' : '' }}"><i
                    class="fas fa-parking"></i>Parking</a></li>
                    <li><a href="{{ route('gestor.user.index') }}"
                class="{{ request()->routeIs('gestor.user.index') ? 'active' : '' }}"><i
                    class="fas fa-user"></i>Usuarios</a></li>
            </ul>
        </div>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">
                Parkings @if(isset($lugarGestor)) en {{ $lugarGestor->nombre }} @endif
            </h1>
            <a href="{{ route('gestor.index') }}" class="btn-purple">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>

        <!-- Mapa -->
        <div class="card shadow-sm p-4 mb-4" style="background-color: white; border-radius: 12px;">
            <h5 class="mb-3 text-dark font-weight-bold">Mapa de Parkings</h5>
            <div id="map" style="height: 500px; border-radius: 10px;"></div>
                    <div id="editPanel" class="edit-panel-below" style="display: none;">
            <h4 class="text-dark font-weight-bold mb-3">Editar Parking</h4>
            <form id="editParkingForm" method="POST" onsubmit="return validarFormulario(event)">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="parkingId">

                <div class="form-group">
                    <label for="nombre">Nombre del Parking</label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required>
                </div>

                <div class="form-group">
                    <label for="plazas">Cantidad de Plazas</label>
                    <input type="number" class="form-control" name="plazas" id="plazas" required>
                </div>

                <div class="form-group">
                    <label for="latitud">Latitud</label>
                    <input type="text" class="form-control" name="latitud" id="latitud" required>
                </div>

                <div class="form-group">
                    <label for="longitud">Longitud</label>
                    <input type="text" class="form-control" name="longitud" id="longitud" required>
                </div>

                <div class="form-actions mt-3">
                    <button type="submit" class="btn btn-submit">Guardar</button>
                    <button type="button" class="btn btn-cancel" onclick="closeEditPanel()">Cancelar</button>
                </div>
            </form>
        </div>
        </div>
    </div>
    <form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

</div>
<script>
    window.parkingsBladeData = @json($parkings);
    window.sessionSuccess = @json(session('success'));
    window.sessionError = @json(session('error'));
</script>
<script src="{{ asset('js/gestor-parking.js') }}"></script>

