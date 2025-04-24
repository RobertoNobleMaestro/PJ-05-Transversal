/**
 * EDICIÓN DE USUARIOS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene las funciones necesarias para gestionar la edición
 * de usuarios existentes en el sistema. Implementa validación avanzada de
 * campos específicos (DNI español, email, teléfono) y procesamiento AJAX
 * para actualizar los datos sin recargar la página completa.
 * Forma parte del módulo de gestión de usuarios del panel de administración.
 */

/**
 * validateField(input) - Valida un campo específico del formulario
 * 
 * @param {HTMLElement} input - El elemento input a validar
 * @returns {boolean} - Retorna true si el campo es válido, false en caso contrario
 * 
 * Esta función aplica reglas de validación específicas para cada campo en el formulario
 * de edición de usuarios. A diferencia de la creación, algunas validaciones son
 * más flexibles (como la contraseña que puede estar vacía para mantener la existente).
 * Muestra mensajes de error en tiempo real para mejorar la experiencia de usuario.
 */
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

/**
 * validateEmail(email) - Valida el formato de un email
 * 
 * @param {string} email - El email a validar
 * @returns {boolean} - Retorna true si el email es válido, false en caso contrario
 * 
 * Utiliza una expresión regular para verificar que el email tenga un formato
 * válido con usuario, @ y dominio con punto. Esta validación se aplica
 * tanto en la creación como en la edición de usuarios.
 */
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * validateDNI(dni) - Valida un DNI español según el algoritmo oficial
 * 
 * @param {string} dni - El DNI a validar
 * @returns {boolean} - Retorna true si el DNI es válido, false en caso contrario
 * 
 * Implementa la validación oficial de DNI español comprobando que:
 * 1. El formato sea correcto (8 dígitos + letra mayúscula)
 * 2. La letra corresponda al resultado del algoritmo oficial
 * 
 * Este tipo de validación añade robustez al sistema para evitar
 * datos incorrectos en la base de datos.
 */
function validateDNI(dni) {
    const re = /^\d{8}[A-Z]$/;
    if (!re.test(dni)) {
        return false;
    }
    
    // Algoritmo oficial de validación de DNI español
    const number = parseInt(dni.slice(0, 8), 10);
    const letter = dni.charAt(8);
    const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
    const calculatedLetter = letters[number % 23];
    
    return calculatedLetter === letter;
}

/**
 * updateUser(userId) - Procesa el formulario para actualizar un usuario existente
 * 
 * @param {number} userId - ID del usuario que se está editando
 * 
 * Esta función gestiona todo el proceso de actualización de un usuario:
 * 1. Valida todos los campos del formulario
 * 2. Muestra alertas si hay errores de validación
 * 3. Envía los datos al servidor mediante AJAX (Fetch API)
 * 4. Procesa la respuesta del servidor (éxito o errores)
 * 5. Redirecciona al listado si la actualización es exitosa
 * 
 * El campo de contraseña es especial: si se deja vacío, se mantiene
 * la contraseña existente del usuario.
 */
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
    
    // Mostrar indicador de carga durante el proceso
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Actualizando usuario',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
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
    const form = document.getElementById('editUserForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });
});
