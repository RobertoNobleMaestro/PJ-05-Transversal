/**
 * Validaciones para los formularios de recuperación y restablecimiento de contraseña
 */
document.addEventListener('DOMContentLoaded', function() {
    // Elementos comunes
    const resetForm = document.getElementById('reset-password-form');
    const forgotForm = document.getElementById('forgot-password-form');
    
    // Validación en tiempo real del campo de email
    function validateEmail(inputElement, feedbackElement) {
        const email = inputElement.value.trim();
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email === '') {
            showError(inputElement, feedbackElement, 'El campo de correo electrónico no puede estar vacío');
            return false;
        } else if (!emailPattern.test(email)) {
            showError(inputElement, feedbackElement, 'Por favor, introduce una dirección de correo electrónico válida');
            return false;
        } else {
            clearError(inputElement, feedbackElement);
            return true;
        }
    }
    
    // Validación en tiempo real del campo de contraseña
    function validatePassword(inputElement, feedbackElement) {
        const password = inputElement.value;
        const minLength = 8;
        const hasUpperCase = /[A-Z]/.test(password);
        const hasLowerCase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const requirementsElement = document.getElementById('password-requirements');
        
        if (password === '') {
            showError(inputElement, feedbackElement, 'El campo de contraseña no puede estar vacío');
            if (requirementsElement) {
                requirementsElement.classList.remove('text-success');
                requirementsElement.classList.add('text-danger');
            }
            return false;
        } else if (password.length < minLength) {
            showError(inputElement, feedbackElement, `La contraseña debe tener al menos ${minLength} caracteres`);
            if (requirementsElement) {
                requirementsElement.classList.remove('text-success');
                requirementsElement.classList.add('text-danger');
            }
            return false;
        } else if (!(hasUpperCase && hasLowerCase && hasNumbers)) {
            showError(inputElement, feedbackElement, 'La contraseña debe contener al menos una letra mayúscula, una minúscula y un número');
            if (requirementsElement) {
                requirementsElement.classList.remove('text-success');
                requirementsElement.classList.add('text-danger');
            }
            return false;
        } else {
            clearError(inputElement, feedbackElement);
            // Cambiar el texto de requisitos a verde para indicar que se cumplen todos
            if (requirementsElement) {
                requirementsElement.classList.remove('text-danger');
                requirementsElement.classList.add('text-success');
                requirementsElement.textContent = '✓ La contraseña cumple con todos los requisitos';
            }
            return true;
        }
    }
    
    // Validación en tiempo real de coincidencia de contraseñas
    function validatePasswordConfirmation(passwordElement, confirmElement, feedbackElement) {
        const password = passwordElement.value;
        const confirmation = confirmElement.value;
        
        if (confirmation === '') {
            showError(confirmElement, feedbackElement, 'Por favor, confirma tu contraseña');
            return false;
        } else if (password !== confirmation) {
            showError(confirmElement, feedbackElement, 'Las contraseñas no coinciden');
            return false;
        } else {
            clearError(confirmElement, feedbackElement);
            return true;
        }
    }
    
    // Función auxiliar para mostrar errores
    function showError(inputElement, feedbackElement, message) {
        inputElement.classList.add('is-invalid');
        feedbackElement.textContent = message;
        feedbackElement.style.display = 'block';
    }
    
    // Función auxiliar para limpiar errores
    function clearError(inputElement, feedbackElement) {
        inputElement.classList.remove('is-invalid');
        feedbackElement.style.display = 'none';
    }
    
    // Validación del formulario de recuperación de contraseña (forgot-password)
    if (forgotForm) {
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        
        // Validación en tiempo real mientras el usuario escribe
        emailInput.addEventListener('input', function() {
            validateEmail(emailInput, emailFeedback);
        });
        
        // Validación al enviar el formulario
        forgotForm.addEventListener('submit', function(event) {
            const isEmailValid = validateEmail(emailInput, emailFeedback);
            
            if (!isEmailValid) {
                event.preventDefault();
            }
        });
    }
    
    // Validación del formulario de restablecimiento de contraseña (reset-password)
    if (resetForm) {
        const emailInput = document.getElementById('email');
        const emailFeedback = document.getElementById('email-feedback');
        const passwordInput = document.getElementById('password');
        const passwordFeedback = document.getElementById('password-feedback');
        const confirmInput = document.getElementById('password-confirm');
        
        // Validación en tiempo real mientras el usuario escribe
        emailInput.addEventListener('input', function() {
            validateEmail(emailInput, emailFeedback);
        });
        
        passwordInput.addEventListener('input', function() {
            validatePassword(passwordInput, passwordFeedback);
            // Si ya hay texto en el campo de confirmación, validar coincidencia
            if (confirmInput.value !== '') {
                validatePasswordConfirmation(passwordInput, confirmInput, passwordFeedback);
            }
        });
        
        confirmInput.addEventListener('input', function() {
            validatePasswordConfirmation(passwordInput, confirmInput, passwordFeedback);
        });
        
        // Validación al enviar el formulario
        resetForm.addEventListener('submit', function(event) {
            const isEmailValid = validateEmail(emailInput, emailFeedback);
            const isPasswordValid = validatePassword(passwordInput, passwordFeedback);
            const isConfirmationValid = validatePasswordConfirmation(passwordInput, confirmInput, passwordFeedback);
            
            if (!(isEmailValid && isPasswordValid && isConfirmationValid)) {
                event.preventDefault();
            }
        });
    }
});
