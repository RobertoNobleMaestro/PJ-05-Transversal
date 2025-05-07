/**
 * AÑADIR VEHÍCULO - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar la creación
 * de nuevos vehículos en el sistema, incluyendo validación de formularios y envío
 * de datos al servidor mediante AJAX.
 */

/**
 * validateField(input) - Valida un campo específico del formulario
 * 
 * @param {HTMLElement} input - El elemento input del formulario a validar
 * 
 * Esta función aplica reglas de validación específicas para cada tipo de campo
 * (marca, modelo, año, precio, etc.) y muestra mensajes de error junto al campo
 * si no cumple con los requisitos.
 */
function validateField(input) {
    let errorMessage = '';
    const value = input.value.trim();
    
    // Aplicar reglas de validación específicas según el tipo de campo
    if (input.name === 'marca' || input.name === 'modelo') {
        if (value.length < 2) {
            errorMessage = 'Este campo debe tener al menos 2 caracteres.';
        }
    } else if (input.name === 'año') {
        const currentYear = new Date().getFullYear();
        if (parseInt(value) < 1900 || parseInt(value) > currentYear + 1) {
            errorMessage = `El año debe estar entre 1900 y ${currentYear + 1}.`;
        }
    } else if (input.name === 'precio_dia') {
        if (parseFloat(value) <= 0) {
            errorMessage = 'El precio debe ser mayor que 0.';
        }
    } else if (input.name === 'kilometraje') {
        if (parseInt(value) < 0) {
            errorMessage = 'El kilometraje no puede ser negativo.';
        }
    } else if ((input.name === 'id_lugar' || input.name === 'id_tipo') && value === '') {
        errorMessage = 'Por favor, seleccione una opción.';
    } else if (input.required && value === '') {
        errorMessage = 'Este campo es obligatorio.';
    }
    
    // Mostrar u ocultar mensaje de error
    const errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.textContent = errorMessage;
    } else if (errorMessage) {
        // Crear nuevo elemento para mostrar el error
        const span = document.createElement('span');
        span.classList.add('error-message');
        span.style.color = 'red';
        span.textContent = errorMessage;
        input.parentNode.insertBefore(span, input.nextSibling);
    }
}

/**
 * createVehiculo() - Procesa el formulario para crear un nuevo vehículo
 * 
 * Esta función se ejecuta al enviar el formulario. Primero realiza una validación
 * completa de todos los campos, muestra errores si es necesario, y si todo es correcto,
 * envía los datos al servidor mediante una petición AJAX.
 */
function createVehiculo() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar todos los campos del formulario
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    // Recorrer cada campo y validarlo
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            const value = input.value.trim();
            let errorMessage = '';
            
            // Reglas de validación para cada tipo de campo
            if (input.name === 'marca' || input.name === 'modelo') {
                if (value.length < 2) {
                    errorMessage = 'Este campo debe tener al menos 2 caracteres.';
                    isValid = false;
                }
            } else if (input.name === 'año') {
                const currentYear = new Date().getFullYear();
                if (parseInt(value) < 1900 || parseInt(value) > currentYear + 1) {
                    errorMessage = `El año debe estar entre 1900 y ${currentYear + 1}.`;
                    isValid = false;
                }
            } else if (input.name === 'precio_dia') {
                if (parseFloat(value) <= 0) {
                    errorMessage = 'El precio debe ser mayor que 0.';
                    isValid = false;
                }
            } else if (input.name === 'kilometraje') {
                if (parseInt(value) < 0) {
                    errorMessage = 'El kilometraje no puede ser negativo.';
                    isValid = false;
                }
            } else if ((input.name === 'id_lugar' || input.name === 'id_tipo') && value === '') {
                errorMessage = 'Por favor, seleccione una opción.';
                isValid = false;
            } else if (input.required && value === '') {
                errorMessage = 'Este campo es obligatorio.';
                isValid = false;
            }
            
            // Mostrar mensaje de error si es necesario
            if (errorMessage) {
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.textContent = errorMessage;
                } else {
                    const span = document.createElement('span');
                    span.classList.add('error-message');
                    span.style.color = 'red';
                    span.textContent = errorMessage;
                    input.parentNode.insertBefore(span, input.nextSibling);
                }
            }
        }
    });
    
    // Si hay errores, mostrar alerta y detener el envío
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga durante el proceso
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Creando nuevo vehículo',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
    // Preparar los datos del formulario para el envío
    const formData = new FormData(form);
    // Obtener la URL del formulario desde un atributo data
    const url = form.dataset.url;
    
    // Realizar petición AJAX al servidor
    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Procesar la respuesta del servidor
        if (data.status === 'success') {
            // Mostrar mensaje de éxito y redirigir al listado
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Vehículo creado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir al listado de vehículos
                    window.location.href = document.querySelector('meta[name="vehicles-index"]').content;
                }
            });
        } else if (data.errors) {
            // Procesamiento de errores de validación del servidor
            // Construir mensaje de error HTML
            let errorHtml = '<ul class="text-start list-unstyled">';
            
            // Mostrar cada error de validación
            Object.keys(data.errors).forEach(field => {
                errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${data.errors[field][0]}</li>`;
                
                // Mostrar error junto al campo correspondiente
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger mt-1';
                    errorDiv.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorDiv);
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
            // Error general en el proceso
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: `<p class="lead">Error al crear vehículo: ${data.message || 'Error desconocido'}</p>`,
                confirmButtonColor: '#9F17BD'
            });
        }
    })
    .catch(error => {
        // Manejar errores de conexión o del servidor
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos para la validación en tiempo real de campos
 * y el evento submit del formulario.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el formulario y todos sus campos
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    
    // Configurar validación en tiempo real para cada campo
    inputs.forEach(input => {
        if (input.name) { // Solo procesar elementos con nombre
            input.addEventListener('blur', function() {
                validateField(this);
            });
            
            // También validar al cambiar para campos select
            if (input.tagName === 'SELECT') {
                input.addEventListener('change', function() {
                    validateField(this);
                });
            }
        }
    });
    
    // Configurar el evento submit del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevenir el envío tradicional
        createVehiculo(); // Procesar mediante AJAX
    });
});
