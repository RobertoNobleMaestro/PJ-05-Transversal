/**
 * EDICIÓN DE VEHÍCULOS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar la edición
 * de vehículos existentes en el sistema, incluyendo validación de formularios
 * y envío de datos al servidor mediante AJAX.
 */

/**
 * validateField(input) - Valida un campo específico del formulario
 * 
 * @param {HTMLElement} input - El elemento input del formulario a validar
 * @returns {boolean} - Retorna true si el campo es válido, false en caso contrario
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
    
    // Retornar resultado de validación
    return errorMessage === '';
}

/**
 * updateVehiculo(vehiculoId) - Procesa el formulario para actualizar un vehículo existente
 * 
 * @param {number} vehiculoId - El ID del vehículo que se está editando
 * 
 * Esta función se ejecuta al enviar el formulario de edición. Primero realiza una validación
 * completa de todos los campos, muestra errores si es necesario, y si todo es correcto,
 * envía los datos actualizados al servidor mediante una petición AJAX.
 */
function updateVehiculo(vehiculoId) {
    const form = document.getElementById('editVehiculoForm');
    const formData = new FormData(form);

    // Añadir el campo _method para simular el método PUT
    formData.append('_method', 'POST');


    // Mostrar indicador de carga durante el proceso
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Actualizando vehículo',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });

    // Realizar petición AJAX al servidor
    fetch(`/gestor/vehiculos/${vehiculoId}`, {
        method: 'POST', // Usar POST con _method=PUT
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Mostrar mensaje de éxito y redirigir al listado
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Vehículo actualizado exitosamente'}</p>`,
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
            let errorHtml = '<ul class="text-start list-unstyled">';
            Object.keys(data.errors).forEach(field => {
                errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${data.errors[field][0]}</li>`;
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger mt-1';
                    errorDiv.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorDiv);
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
            // Error general en el proceso
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: `<p class="lead">Error al actualizar vehículo: ${data.message || 'Error desconocido'}</p>`,
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
 * y prepara el entorno para la edición de vehículos.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener el formulario y todos sus campos
    const form = document.getElementById('editVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    
    // Configurar validación en tiempo real para cada campo
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });

    showStep(1);
    document.getElementById('next-step').onclick = function(e) {
        e.preventDefault();
        if (validateStep(1)) {
            showStep(2);
        }
    };
    document.getElementById('prev-step').onclick = function(e) {
        e.preventDefault();
        showStep(1);
    };
    document.getElementById('editVehiculoForm').addEventListener('submit', function(e) {
        if (!validateStep(2)) {
            e.preventDefault();
        }
    });
});

// Wizard de dos pasos para editar vehículo
function showStep(step) {
    document.getElementById('wizard-step-1').style.display = step === 1 ? 'block' : 'none';
    document.getElementById('wizard-step-2').style.display = step === 2 ? 'block' : 'none';
}

function validateStep(step) {
    let isValid = true;
    const stepDiv = document.getElementById('wizard-step-' + step);
    const inputs = stepDiv.querySelectorAll('input, select');
    inputs.forEach(input => {
        if (input.name) {
            validateField(input);
            if (input.parentNode.querySelector('.error-message')?.textContent) {
                isValid = false;
            }
        }
    });
    return isValid;
}
