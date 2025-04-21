@extends('layouts.admin')

@section('title', 'Añadir Lugar')

@section('content')
<!-- Añadir CSS de Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
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
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #9F17BD;
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
        margin-top: 1.5rem;
    }
    
    .submit-btn {
        background: #000000;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: #333333;
    }
    
    .cancel-btn {
        background: #9F17BD;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .cancel-btn:hover {
        background: #7E12A3;
    }

    .error-message {
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
    
    .error-feedback {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    .is-invalid {
        border-color: #e53e3e;
    }
    
    .is-invalid:focus {
        border-color: #e53e3e;
        outline: none;
        box-shadow: 0 0 0 2px rgba(229, 62, 62, 0.1);
    }
</style>

<div class="add-place-container">
    <h1 class="form-title">Añadir Nuevo Lugar</h1>
    <form id="addLugarForm">
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
                    <input type="number" step="any" class="form-control" id="latitud" name="latitud" required>
                    <div id="latitud-error" class="error-feedback"></div>
                </div>

                <div class="form-group">
                    <label for="longitud" class="form-label">Longitud</label>
                    <input type="number" step="any" class="form-control" id="longitud" name="longitud" required>
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

<script>
let map;
let marker;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initMap();
    
    // Configurar validación en tiempo real
    setupLiveValidation();
});

// Configurar la validación en tiempo real
function setupLiveValidation() {
    const inputs = document.querySelectorAll('input');
    
    inputs.forEach(input => {
        // Validar cuando el usuario termina de escribir en un campo
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        // Validar mientras el usuario escribe (con un pequeño retraso)
        input.addEventListener('input', function() {
            const inputField = this;
            clearTimeout(inputField.timer);
            inputField.timer = setTimeout(function() {
                validateField(inputField);
            }, 500); // Esperar 500ms después de que el usuario deje de escribir
        });
    });
}

function initMap() {
    // Coordenadas iniciales (Madrid)
    const lat = parseFloat(document.getElementById('latitud').value);
    const lng = parseFloat(document.getElementById('longitud').value);
    
    // Crear mapa
    map = L.map('map').setView([lat, lng], 13);
    
    // Añadir capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Añadir marcador
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

function validateField(input) {
    let isValid = true;
    let errorMessage = '';
    const value = input.value.trim();
    
    // Validación específica para cada campo
    if (input.name === 'nombre' && value === '') {
        errorMessage = 'El nombre es obligatorio';
        isValid = false;
    } else if (input.name === 'nombre' && value.length < 3) {
        errorMessage = 'El nombre debe tener al menos 3 caracteres';
        isValid = false;
    } else if (input.name === 'direccion' && value === '') {
        errorMessage = 'La dirección es obligatoria';
        isValid = false;
    } else if (input.name === 'direccion' && value.length < 5) {
        errorMessage = 'La dirección debe tener al menos 5 caracteres';
        isValid = false;
    } else if (input.name === 'latitud') {
        if (value === '' || isNaN(value)) {
            errorMessage = 'La latitud debe ser un número válido';
            isValid = false;
        } else if (parseFloat(value) < -90 || parseFloat(value) > 90) {
            errorMessage = 'La latitud debe estar entre -90 y 90';
            isValid = false;
        }
    } else if (input.name === 'longitud') {
        if (value === '' || isNaN(value)) {
            errorMessage = 'La longitud debe ser un número válido';
            isValid = false;
        } else if (parseFloat(value) < -180 || parseFloat(value) > 180) {
            errorMessage = 'La longitud debe estar entre -180 y 180';
            isValid = false;
        }
    }
    
    // Limpiar mensaje de error anterior
    const errorElement = document.getElementById(`${input.name}-error`);
    if (errorElement) {
        errorElement.innerHTML = isValid ? '' : `<small class="text-danger">${errorMessage}</small>`;
        
        if (isValid) {
            input.classList.remove('is-invalid');
        } else {
            input.classList.add('is-invalid');
        }
    }
    
    return isValid;
}

function createLugar() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.error-feedback').forEach(el => {
        el.innerHTML = '';
    });
    
    // Eliminar clase is-invalid de todos los inputs
    document.querySelectorAll('input').forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    // Obtener los datos del formulario
    const form = document.getElementById('addLugarForm');
    const formData = new FormData(form);
    
    // Validar todos los campos antes de enviar
    const inputs = form.querySelectorAll('input');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
            html: '<p class="lead">Por favor, revisa los campos marcados en rojo</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Creando nuevo lugar',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
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
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: '<p class="lead">No se pudo encontrar el token CSRF</p>',
                confirmButtonColor: '#9F17BD'
            });
            return;
        }
    }
    
    fetch('{{ route("admin.lugares.store") }}', {
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
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> Completado!</span>',
                html: `<p class="lead">${data.message}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("admin.lugares") }}';
                }
            });
        } else {
            // Mostrar errores de validación si existen
            if (data.errors) {
                let errorHtml = '<ul class="text-start list-unstyled">';
                
                Object.keys(data.errors).forEach(field => {
                    errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${data.errors[field][0]}</li>`;
                    
                    const input = document.getElementById(field);
                    if (input) {
                        const error = document.createElement('div');
                        error.className = 'error-feedback';
                        error.innerText = data.errors[field][0];
                        input.parentElement.appendChild(error);
                    }
                });
                
                errorHtml += '</ul>';
                
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
                    html: errorHtml,
                    confirmButtonColor: '#9F17BD'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: `<p class="lead">${data.message || 'Error al crear el lugar'}</p>`,
                    confirmButtonColor: '#9F17BD'
                });
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error al procesar la solicitud</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}
</script>
