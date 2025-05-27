function validateField(input) {
    let errorMessage = '';
    const value = input.value.trim();
    
    if (input.name === 'email') {
        if (!validateEmail(value)) {
            errorMessage = 'Por favor, ingrese un email válido.';
        }
    } else if (input.name === 'password') {
        if (value.length < 8 && value.length > 0) { // Solo validar si hay valor (puede estar vacío en edición)
            errorMessage = 'La contraseña debe tener al menos 8 caracteres.';
        }
    } else if (input.name === 'telefono') {
        if (!/^\d{9}$/.test(value)) {
            errorMessage = 'El teléfono debe contener exactamente 9 números.';
        }
    } else if (input.name === 'direccion') {
        if (value.length < 5) {
            errorMessage = 'La dirección debe tener al menos 5 caracteres.';
        }
    } else if (input.required && value === '') {
        errorMessage = 'Este campo es obligatorio.';
    }
    
    // Mostrar u ocultar mensaje de error según validación
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
    
    return errorMessage === '';
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateDNI(dni) {
    const re = /^\d{8}[A-Z]$/;
    if (!re.test(dni)) {
        return false;
    }
    const number = parseInt(dni.slice(0, 8), 10);
    const letter = dni.charAt(8);
    const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
    const calculatedLetter = letters[number % 23];
    return calculatedLetter === letter;
}

function updateUser(userId) {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar campos antes de enviar
    const form = document.getElementById('editUserForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            if (!validateField(input)) {
                isValid = false;
            }
        }
    });
    
    // Si hay errores, mostrar alerta y detener el proceso
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Obtener los datos del formulario y convertir a objeto JSON
    const formData = new FormData(form);
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    
    // Obtener URL y datos de redirección
    const url = form.dataset.url || `/admin/users/${userId}`;
    const redirectUrl = document.querySelector('meta[name="users-index"]').content;
    
    // Realizar petición AJAX al servidor con método POST y datos JSON
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formDataObj)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Mostrar mensaje de éxito y redirigir al listado
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Usuario actualizado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = redirectUrl;
                }
            });
        } else if (data.errors) {
            // Construir mensaje de error HTML para validaciones del servidor
            let errorHtml = '<ul class="text-start list-unstyled">';
            
            // Mostrar errores de validación provenientes del servidor
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
            
            // Mostrar modal con todos los errores
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
                html: `<p class="lead">Error al actualizar usuario: ${data.message || 'Error desconocido'}</p>`,
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
 * Configura los eventos de validación en tiempo real para todos los campos
 * del formulario de edición de usuarios, mejorando la experiencia de usuario
 * con retroalimentación inmediata mientras se editan los datos.
 */
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('asalariadoWizardForm');
    if (!form) {
        console.error('No se encontró el formulario con ID "asalariadoWizardForm"');
        return;
    }

    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });

    window.updateUser = function(userId) {
        document.querySelectorAll('.text-danger').forEach(el => el.remove());

        let isValid = true;
        const missingFields = [];

        inputs.forEach(input => {
            if (input.required && input.value.trim() === '' && !input.disabled) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'Este campo es obligatorio.';
                input.parentNode.appendChild(errorDiv);
            } else if (input.name === 'email' && !validateEmail(input.value.trim())) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'Por favor, ingrese un email válido.';
                input.parentNode.appendChild(errorDiv);
            } else if (input.name === 'DNI' && !input.disabled && !validateDNI(input.value.trim())) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'El DNI es inválido.';
                input.parentNode.appendChild(errorDiv);
            } else if (input.name === 'password' && input.value.trim().length < 8 && input.value.trim().length > 0) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'La contraseña debe tener al menos 8 caracteres.';
                input.parentNode.appendChild(errorDiv);
            } else if (input.name === 'telefono' && !/^\d{9}$/.test(input.value.trim())) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'El teléfono debe contener exactamente 9 números.';
                input.parentNode.appendChild(errorDiv);
            } else if (input.name === 'direccion' && input.value.trim().length < 5) {
                isValid = false;
                missingFields.push(input.name);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'text-danger mt-1';
                errorDiv.textContent = 'La dirección debe tener al menos 5 caracteres.';
                input.parentNode.appendChild(errorDiv);
            }
        });

        if (!isValid) {
            let errorMessage = 'Por favor, complete los siguientes campos correctamente:<br><ul>';
            missingFields.forEach(field => {
                errorMessage += `<li>${field}</li>`;
            });
            errorMessage += '</ul>';

            Swal.fire({
                icon: 'warning',
                title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
                html: errorMessage,
                confirmButtonColor: '#9F17BD'
            });
            return;
        }


        const formData = new FormData(form);
        const formDataObj = {};
        formData.forEach((value, key) => {
            formDataObj[key] = value;
        });

        fetch(`/gestor/users/${userId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: JSON.stringify(formDataObj)
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                    html: `<p class="lead">${data.message || 'Usuario actualizado exitosamente'}</p>`,
                    confirmButtonColor: '#9F17BD',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = document.querySelector('meta[name="users-index"]').content;
                    }
                });
            } else if (data.errors) {
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
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: `<p class="lead">Error al actualizar usuario: ${data.message || 'Error desconocido'}</p>`,
                    confirmButtonColor: '#9F17BD'
                });
            }
        })
    };
});
