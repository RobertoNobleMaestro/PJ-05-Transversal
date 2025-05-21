@extends('layouts.admin')

@section('title', 'Gestión de Parkings')

@section('content')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="admin-container">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-sidebar" id="sidebar">
        <div style="position: fixed;width: 220px;">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="{{ route('gestor.vehiculos') }}" class="{{ request()->routeIs('gestor.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i> Vehículos</a></li>
                <li><a href="{{ route('gestor.chat.listar') }}" class="{{ request()->routeIs('gestor.chat.listar*') ? 'active' : '' }}"><i class="fas fa-comments"></i> Chats</a></li>
                <li><a href="{{ route('gestor.historial') }}" class="{{ request()->routeIs('gestor.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
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
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script>
let mapInstance;

document.addEventListener('DOMContentLoaded', function () {
    mapInstance = L.map('map').setView([40.4168, -3.7038], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapInstance);

    // Evita declarar múltiples veces la misma variable con let marker
    @foreach($parkings as $index => $parking)
        const marker{{ $index }} = L.marker([{{ $parking->latitud }}, {{ $parking->longitud }}]).addTo(mapInstance);
        marker{{ $index }}.bindPopup(`
            <strong>{{ $parking->nombre }}</strong><br>
            Plazas: {{ $parking->plazas }}<br>
            <button class='btn btn-sm btn-warning mt-2'
                onclick="openEditPanel({{ $parking->id }}, '{{ addslashes($parking->nombre) }}', {{ $parking->plazas }}, {{ $parking->latitud }}, {{ $parking->longitud }})">
                Editar
            </button>
            <button class='btn btn-sm btn-danger mt-2'
                onclick="confirmarEliminacion({{ $parking->id }})">
                Eliminar
            </button>

        `);
    @endforeach
});

function openEditPanel(id, nombre, plazas, lat, lng) {
    const form = document.getElementById('editParkingForm');

    document.getElementById('parkingId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('plazas').value = plazas;
    document.getElementById('latitud').value = lat;
    document.getElementById('longitud').value = lng;
    form.action = `/gestor/parking/${id}`;

    const panel = document.getElementById('editPanel');
    panel.classList.add('show');
    panel.style.display = 'block';

    // Asegurarse de que el panel está visible antes de hacer scroll
    setTimeout(() => {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 300);
}

function closeEditPanel() {
    const panel = document.getElementById('editPanel');
    panel.classList.remove('show');
    panel.style.display = 'none';
}
function validarFormulario(e) {
    e.preventDefault(); // Evita el envío inmediato

    const nombre = document.getElementById('nombre').value.trim();
    const plazas = document.getElementById('plazas').value.trim();
    const latitud = document.getElementById('latitud').value.trim();
    const longitud = document.getElementById('longitud').value.trim();

    if (!nombre || !plazas || !latitud || !longitud) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, rellena todos los campos antes de guardar.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    if (isNaN(plazas) || plazas <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Plazas inválidas',
            text: 'La cantidad de plazas debe ser un número mayor que cero.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    if (isNaN(latitud) || isNaN(longitud)) {
        Swal.fire({
            icon: 'error',
            title: 'Coordenadas inválidas',
            text: 'Latitud y longitud deben ser valores numéricos.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    // Confirmación antes de guardar
    Swal.fire({
        title: '¿Guardar cambios?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('editParkingForm').submit();
        }
    });

    return false;
}
    function confirmarEliminacion(parkingId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará el parking de forma permanente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/gestor/parking/${parkingId}`;
            form.submit();
        }
    });
}
</script>

