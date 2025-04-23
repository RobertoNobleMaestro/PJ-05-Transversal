/**
 * Valida un campo específico y muestra mensajes de error si es necesario
 * @param {HTMLElement} input - El campo a validar
 */
function validateField(input) {
    let errorMessage = '';
    const value = input.value.trim();
    
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
    
    const errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.textContent = errorMessage;
    } else if (errorMessage) {
        const span = document.createElement('span');
        span.classList.add('error-message');
        span.style.color = 'red';
        span.textContent = errorMessage;
        input.parentNode.insertBefore(span, input.nextSibling);
    }
}

/**
 * Crea un nuevo vehículo con los datos del formulario
 */
function createVehiculo() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar campos antes de enviar
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            const value = input.value.trim();
            let errorMessage = '';
            
            // Reglas de validación para cada campo
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
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Creando nuevo vehículo',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
    // Obtener los datos del formulario
    const formData = new FormData(form);
    
    // Añadir checkboxes manualmente (ya que solo se incluyen si están marcados)
    formData.set('seguro_incluido', document.getElementById('seguro_incluido').checked ? 1 : 0);
    formData.set('disponibilidad', document.getElementById('disponibilidad').checked ? 1 : 0);
    
    // Obtener la URL del formulario desde un atributo data
    const url = form.dataset.url;
    
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
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Vehículo creado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = document.querySelector('meta[name="vehicles-index"]').content;
                }
            });
        } else if (data.errors) {
            // Construir mensaje de error HTML
            let errorHtml = '<ul class="text-start list-unstyled">';
            
            // Muestra errores de validación
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
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: `<p class="lead">Error al crear vehículo: ${data.message || 'Error desconocido'}</p>`,
                confirmButtonColor: '#9F17BD'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}

// Inicializar cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });
});
