function validateField(input) {
    let errorMessage = '';
    const value = input.value.trim();
    
    if (input.name === 'email') {
        if (!validateEmail(value)) {
            errorMessage = 'Por favor, ingrese un email válido.';
        }
    } else if (input.name === 'DNI') {
        if (!/^\d{8}[A-Z]$/.test(value)) {
            errorMessage = 'El formato del DNI es inválido. Debe terminar con una letra mayúscula.';
        } else if (!validateDNI(value)) {
            errorMessage = 'El DNI es inválido. La letra no coincide.';
        }
    } else if (input.name === 'password') {
        if (value.length < 8) {
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
    
    // Mostrar u ocultar mensaje de error bajo el campo
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

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}
function validateDNI(dni) {
    const re = /^\d{8}[A-Z]$/;
    if (!re.test(dni)) {
        return false;
    }
    
    // Algoritmo para validar la letra del DNI español
    const number = parseInt(dni.slice(0, 8), 10);
    const letter = dni.charAt(8);
    const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
    const calculatedLetter = letters[number % 23];
    
    return calculatedLetter === letter;
}
function createUser() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar campos antes de enviar
    const form = document.getElementById('asalariadoWizardForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            const value = input.value.trim();
            let errorMessage = '';
            
            // Reglas de validación para cada campo
            if (input.name === 'email') {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!re.test(value)) {
                    errorMessage = 'Por favor, ingrese un email válido.';
                    isValid = false;
                }
            } else if (input.name === 'DNI') {
                if (!/^\d{8}[A-Z]$/.test(value)) {
                    errorMessage = 'El formato del DNI es inválido. Debe terminar con una letra mayúscula.';
                    isValid = false;
                } else {
                    // Validación de la letra del DNI
                    const number = parseInt(value.slice(0, 8), 10);
                    const letter = value.charAt(8);
                    const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
                    const calculatedLetter = letters[number % 23];
                    
                    if (calculatedLetter !== letter) {
                        errorMessage = 'El DNI es inválido. La letra no coincide.';
                        isValid = false;
                    }
                }
            } else if (input.name === 'password') {
                if (value.length < 8) {
                    errorMessage = 'La contraseña debe tener al menos 8 caracteres.';
                    isValid = false;
                }
            } else if (input.name === 'telefono') {
                if (!/^\d{9}$/.test(value)) {
                    errorMessage = 'El teléfono debe contener exactamente 9 números.';
                    isValid = false;
                }
            } else if (input.name === 'direccion') {
                if (value.length < 5) {
                    errorMessage = 'La dirección debe tener al menos 5 caracteres.';
                    isValid = false;
                }
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
    
    // Si hay errores, mostrar alerta general y detener el proceso
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga mientras se procesa la solicitud
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Creando nuevo usuario',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
    // Obtener los datos del formulario
    const formData = new FormData(form);
    
    // Obtener la URL del formulario y de redirección
    const url = form.dataset.url;
    const redirectUrl = document.querySelector('meta[name="users-index"]').content;
    
    // Enviar solicitud AJAX al servidor
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
            // Mostrar mensaje de éxito y redirigir
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Usuario creado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = redirectUrl;
                }
            });
        } else if (data.errors) {
            // Construir mensaje de error HTML para errores de validación del servidor
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
            
            // Mostrar errores en modal
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
                html: `<p class="lead">Error al crear usuario: ${data.message || 'Error desconocido'}</p>`,
                confirmButtonColor: '#9F17BD'
            });
        }
    })
    .catch(error => {
        // Gestionar errores de conexión o del servidor
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('asalariadoWizardForm');
    if (!form) return; // Evita errores si el formulario no existe
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });
});
