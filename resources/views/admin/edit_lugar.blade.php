@extends('layouts.admin')

@section('title', 'Editar Lugar')

@section('content')
<!-- Au00f1adir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="/"/>
<style>
    .add-place-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .form-title {
        color: #2d3748;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: left;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group.full-width {
        grid-column: span 4;
    }
    
    .form-group.half-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #4a5568;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #9F17BD;
        outline: none;
        box-shadow: 0 0 0 2px rgba(159, 23, 189, 0.1);
    }

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-submit {
        background-color: #000;
        color: white;
    }

    .btn-submit:hover {
        background-color: #333;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background-color: #9F17BD;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #8614a0;
        transform: translateY(-1px);
    }

    .text-danger {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-text {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Estilos para el mapa */
    #map {
        width: 100%;
        height: 300px;
        border-radius: 6px;
        margin-top: 1rem;
    }
</style>

<div class="add-place-container">
    <h1 class="form-title">Editar Lugar</h1>
    <form id="editLugarForm">
        @csrf
        <div class="form-grid">
            <!-- Columna izquierda -->
            <div>
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre del Lugar</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $lugar->nombre }}" required>
                </div>

                <div class="form-group">
                    <label for="direccion" class="form-label">Direcciu00f3n</label>
                    <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $lugar->direccion }}" required>
                </div>
            </div>
            
            <!-- Columna derecha -->
            <div>
                <div class="form-group">
                    <label for="latitud" class="form-label">Latitud</label>
                    <input type="number" step="any" class="form-control" id="latitud" name="latitud" value="{{ $lugar->latitud }}" required>
                </div>

                <div class="form-group">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="number" step="any" class="form-control" id="longitud" name="longitud" value="{{ $lugar->longitud }}" required>
                </div>
            </div>
        </div>
        
        <!-- Mapa para seleccionar ubicaciu00f3n -->
        <div class="form-group full-width">
            <label class="form-label">Seleccionar ubicaciu00f3n en el mapa</label>
            <div id="map"></div>
            <small class="form-text">Haz clic en el mapa para seleccionar la ubicaciu00f3n. Las coordenadas se actualizaru00e1n automu00e1ticamente.</small>
        </div>

        <div class="btn-container">
            <a href="{{ route('admin.lugares') }}" class="btn btn-cancel">Cancelar</a>
            <button type="button" class="btn btn-submit" onclick="updateLugar({{ $lugar->id_lugar }})">Actualizar</button>
        </div>
    </form>
</div>
@endsection

<!-- Au00f1adir JS de Leaflet -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
let map;
let marker;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initMap();
});

function initMap() {
    // Coordenadas del lugar actual
    const lat = parseFloat(document.getElementById('latitud').value);
    const lng = parseFloat(document.getElementById('longitud').value);
    
    // Crear mapa
    map = L.map('map').setView([lat, lng], 15);
    
    // Au00f1adir capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Au00f1adir marcador
    marker = L.marker([lat, lng], {draggable: true}).addTo(map);
    
    // Actualizar coordenadas cuando se arrastra el marcador
    marker.on('dragend', function(event) {
        const position = marker.getLatLng();
        document.getElementById('latitud').value = position.lat.toFixed(6);
        document.getElementById('longitud').value = position.lng.toFixed(6);
    });
    
    // Actualizar marcador al hacer clic en el mapa
    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('latitud').value = e.latlng.lat.toFixed(6);
        document.getElementById('longitud').value = e.latlng.lng.toFixed(6);
    });
}

function updateLugar(lugarId) {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Obtener los datos del formulario
    const form = document.getElementById('editLugarForm');
    const formData = new FormData(form);
    
    // Convertir FormData a objeto para enviar como JSON
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    
    // Obtener el token CSRF de manera segura
    let csrfToken = '';
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    
    if (metaToken) {
        csrfToken = metaToken.getAttribute('content');
    } else {
        // Si no se encuentra el meta tag, buscar en los formularios existentes
        const hiddenInput = document.querySelector('input[name="_token"]');
        if (hiddenInput) {
            csrfToken = hiddenInput.value;
        } else {
            alert('Error: No se pudo encontrar el token CSRF');
            return;
        }
    }
    
    fetch(`/admin/lugares/${lugarId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formDataObj)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.href = '{{ route("admin.lugares") }}';
        } else {
            // Mostrar errores de validaciu00f3n si existen
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const input = document.getElementById(field);
                    if (input) {
                        const error = document.createElement('div');
                        error.className = 'text-danger';
                        error.innerText = data.errors[field][0];
                        input.parentElement.appendChild(error);
                    }
                });
            } else {
                alert(data.message || 'Error al actualizar el lugar');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error al procesar la solicitud');
    });
}
</script>
