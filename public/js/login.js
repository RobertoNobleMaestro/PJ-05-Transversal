const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginButton = document.getElementById('login');
const errorEmail = document.getElementById('error_email');
const errorPassword = document.getElementById('error_password');

// Validaciones básicas
function validateEmail() {
    const value = emailInput.value.trim();
    if (value === '') {
        errorEmail.textContent = 'El correo es obligatorio.';
        emailInput.classList.add('is-invalid');
        return false;
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
        errorEmail.textContent = 'El correo no es válido.';
        emailInput.classList.add('is-invalid');
        return false;
    } else {
        errorEmail.textContent = '';
        emailInput.classList.remove('is-invalid');
        return true;
    }
}

function validatePassword() {
    const value = passwordInput.value.trim();
    if (value === '') {
        errorPassword.textContent = 'La contraseña es obligatoria.';
        passwordInput.classList.add('is-invalid');
        return false;
    } else if (value.length < 8) {
        errorPassword.textContent = 'Debe tener al menos 8 caracteres.';
        passwordInput.classList.add('is-invalid');
        return false;
    } else {
        errorPassword.textContent = '';
        passwordInput.classList.remove('is-invalid');
        return true;
    }
}

function checkInputs() {
    const validEmail = validateEmail();
    const validPassword = validatePassword();
    loginButton.disabled = !(validEmail && validPassword);
}

// Listeners de entrada y validación onBlur
emailInput.addEventListener('input', checkInputs);
passwordInput.addEventListener('input', checkInputs);
emailInput.addEventListener('blur', validateEmail);
passwordInput.addEventListener('blur', validatePassword);

// Envío del formulario
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!validateEmail() || !validatePassword()) {
        return;
    }

    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al iniciar sesión'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ha ocurrido un error al intentar iniciar sesión'
        });
    });
});



