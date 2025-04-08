document.addEventListener('DOMContentLoaded', function () {
    const loginButton = document.getElementById('login');
    const form = loginButton.closest('form');

    loginButton.addEventListener('click', function (e) {
        e.preventDefault();

        const formData = new FormData(form);
        const data = {
            email: formData.get('email'),
            password: formData.get('pwd')  
        };

        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Ha ocurrido un error al intentar iniciar sesión');
            });
    });
});
document.addEventListener('DOMContentLoaded', function () {
    const inputEmail = document.getElementById('email');
    const error_email = document.getElementById('error_email');
    const inputPwd = document.getElementById('pwd');
    const error_pwd = document.getElementById('error_pwd');
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('login');

    // Asignar eventos
    inputEmail.onblur = validaMail;
    inputPwd.onblur = validaPwd;

    inputEmail.addEventListener('input', validarCampos);
    inputPwd.addEventListener('input', validarCampos);

    loginForm.onsubmit = validaForm;

    // Función para validar mail
    function validaMail() {
        const email = inputEmail.value.trim(); 
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; 

        if (email === "") {
            error_email.textContent = "El campo es obligatorio";
            inputEmail.classList.add('is-invalid');
            return false;
        } else if (!regex.test(email)) {
            error_email.textContent = "El formato del correo no es válido";
            inputEmail.classList.add('is-invalid');
            return false;
        } else {
            error_email.textContent = "";
            inputEmail.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar contraseña
    function validaPwd() {
        const pwd = inputPwd.value.trim();
        if (pwd === "") {
            error_pwd.textContent = "El campo es obligatorio";
            inputPwd.classList.add('is-invalid');
            return false;
        } else if (pwd.length < 8) {
            error_pwd.textContent = "El campo debe tener mínimo 8 carácteres";
            inputPwd.classList.add('is-invalid');
            return false;
        } else {
            error_pwd.textContent = "";
            inputPwd.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar el form completo
    function validaForm(e) {
        e.preventDefault();
        const isEmailValid = validaMail();
        const isPwdValid = validaPwd();

        if (isEmailValid && isPwdValid) {
            loginForm.submit();
        }
    }

    // Deshabilitar botón si los campos no están validados
    function validarCampos() {
        const isEmailValid = validaMail();
        const isPwdValid = validaPwd();

        if (isEmailValid && isPwdValid) {
            loginBtn.disabled = false;
        } else {
            loginBtn.disabled = true;
        }
    }

    // Al iniciar, desactiva el botón
    loginBtn.disabled = true;
});


