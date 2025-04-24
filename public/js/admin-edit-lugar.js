/**
 * EDICIÓN DE LUGARES/SUCURSALES - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene las funciones necesarias para gestionar la edición
 * de lugares/sucursales existentes en el sistema, utilizando integración con mapas
 * para actualizar la ubicación geográfica y validación de formularios.
 * Los lugares son clave para la gestión de vehículos y reservas en la aplicación.
 */

/**
 * Variables globales para el mapa de Leaflet
 */
let map;
let marker;

/**
 * Inicializa el mapa y configura los eventos cuando el DOM está listo
 * 
 * Esta función se ejecuta automáticamente cuando la página ha cargado y
 * prepara todos los componentes interactivos del formulario y el mapa
 * para actualizar las coordenadas de un lugar existente.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar mapa
    initMap();
    
    // Configurar validación en tiempo real
    setupLiveValidation();
});

/**
 * setupLiveValidation() - Configura la validación en tiempo real para los campos
 * 
 * Esta función configura escuchadores de eventos para todos los campos del
 * formulario, permitiendo la validación mientras el usuario escribe y 
 * cuando pierde el foco en un campo, mejorando la experiencia de usuario.
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
 * initMap() - Inicializa el mapa interactivo de Leaflet
 * 
 * Esta función crea y configura un mapa interactivo que permite:
 * - Visualizar la ubicación actual del lugar
 * - Modificar la ubicación arrastrando el marcador
 * - Seleccionar una nueva ubicación haciendo clic en el mapa
 * - Actualizar automáticamente los campos de latitud y longitud
 */
function initMap() {
    // Coordenadas del lugar actual
    const lat = parseFloat(document.getElementById('latitud').value) || 40.416775;
    const lng = parseFloat(document.getElementById('longitud').value) || -3.703790;
    
    // Crear mapa centrado en la ubicación actual del lugar
    map = L.map('map').setView([lat, lng], 15);
    
    // Añadir capa de OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Añadir marcador arrastrable en la posición actual
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
 * validateField(input) - Valida un campo específico del formulario
 * 
 * @param {HTMLElement} input - El elemento input del formulario a validar
 * @returns {boolean} - Retorna true si el campo es válido, false en caso contrario
 * 
 * Esta función aplica reglas de validación específicas para cada tipo de campo
 * (nombre, dirección, latitud, longitud) y muestra mensajes de error si es necesario.
 */
function validateField(input) {
    // Implementar lógica de validación para cada campo
    let isValid = true;
    let errorMessage = '';
    const errorElement = document.getElementById(`${input.name}-error`);
    
    // Validación específica para cada tipo de campo
    if (input.name === 'nombre') {
        if (input.value.trim() === '') {
            errorMessage = 'El nombre del lugar es obligatorio';
            isValid = false;
        } else if (input.value.trim().length < 3) {
            errorMessage = 'El nombre debe tener al menos 3 caracteres';
            isValid = false;
        }
    } else if (input.name === 'direccion') {
        if (input.value.trim() === '') {
            errorMessage = 'La dirección es obligatoria';
            isValid = false;
        } else if (input.value.trim().length < 5) {
            errorMessage = 'La dirección debe tener al menos 5 caracteres';
            isValid = false;
        }
    } else if (input.name === 'latitud') {
        if (input.value === '' || isNaN(input.value)) {
            errorMessage = 'La latitud debe ser un número válido';
            isValid = false;
        } else if (parseFloat(input.value) < -90 || parseFloat(input.value) > 90) {
            errorMessage = 'La latitud debe estar entre -90 y 90';
            isValid = false;
        }
    } else if (input.name === 'longitud') {
        if (input.value === '' || isNaN(input.value)) {
            errorMessage = 'La longitud debe ser un número válido';
            isValid = false;
        } else if (parseFloat(input.value) < -180 || parseFloat(input.value) > 180) {
            errorMessage = 'La longitud debe estar entre -180 y 180';
            isValid = false;
        }
    }
    
    // Mostrar u ocultar mensaje de error según validación
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
 * updateLugar(lugarId) - Procesa el formulario para actualizar un lugar existente
 * 
 * @param {number} lugarId - ID del lugar que se está editando
 * 
 * Esta función se ejecuta al enviar el formulario de edición. Realiza validación
 * completa de todos los campos, muestra errores si es necesario, y si todo es correcto,
 * envía los datos actualizados al servidor mediante una petición AJAX.
 * La actualización de lugares es crítica, ya que afecta a vehículos y reservas relacionadas.
 */
function updateLugar(lugarId) {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.error-feedback').forEach(el => {
        el.innerHTML = '';
    });
    
    // Eliminar clase is-invalid de todos los inputs
    document.querySelectorAll('input').forEach(input => {
        input.classList.remove('is-invalid');
    });
    
    // Obtener los datos del formulario
    const form = document.getElementById('editLugarForm');
    const formData = new FormData(form);
    
    // Validar todos los campos antes de enviar
    const inputs = form.querySelectorAll('input');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    // Si hay errores, mostrar alerta y detener el envío
    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
            html: '<p class="lead">Por favor, revisa los campos marcados en rojo</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga durante el proceso
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Actualizando lugar',
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
    
    // Realizar petición AJAX al servidor con método PUT
    fetch(url, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formDataObj)
    })
    .then(response => response.json())
    .then(data => {
        // Procesar la respuesta del servidor
        if (data.status === 'success') {
            // Mostrar mensaje de éxito y redirigir al listado de lugares
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
                        error.className = 'error-message';
                        error.innerText = data.errors[field][0];
                        input.parentElement.appendChild(error);
                    }
                });
                
                errorHtml += '</ul>';
                
                // Mostrar alerta con todos los errores
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
                    html: errorHtml,
                    confirmButtonColor: '#9F17BD'
                });
            } else {
                // Mostrar mensaje de error general
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: `<p class="lead">${data.message || 'Error al actualizar el lugar'}</p>`,
                    confirmButtonColor: '#9F17BD'
                });
            }
        }
    })
    .catch(error => {
        // Manejar errores de conexión o del servidor
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error al procesar la solicitud</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}
