/**
 * Variables globales para el mapa
 */
let map;
let marker;

/**
 * Inicializa el mapa y configura los eventos cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initMap();
    
    // Configurar validación en tiempo real
    setupLiveValidation();
});

/**
 * Configura la validación en tiempo real para los campos del formulario
 */
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

/**
 * Inicializa el mapa de Leaflet y configura eventos
 */
function initMap() {
    // Coordenadas iniciales (Madrid)
    const lat = parseFloat(document.getElementById('latitud').value) || 40.416775;
    const lng = parseFloat(document.getElementById('longitud').value) || -3.703790;
    
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

/**
 * Valida un campo específico y muestra mensajes de error si es necesario
 * @param {HTMLElement} input - El campo a validar
 * @returns {boolean} - Retorna true si el campo es válido, false en caso contrario
 */
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

/**
 * Crea un nuevo lugar con los datos del formulario
 */
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
    
    // Obtener la URL del formulario y de redirección
    const url = form.dataset.url;
    const redirectUrl = document.querySelector('meta[name="places-index"]').content;
    
    fetch(url, {
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
                    window.location.href = redirectUrl;
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
