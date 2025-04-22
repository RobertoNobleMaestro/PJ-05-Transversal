document.addEventListener('DOMContentLoaded', function () {
    // Recogida de inputs y campos de error del formulario
    const inputNombre = document.getElementById('nombre');
    const error_nombre = document.getElementById('error_nombre');
    const inputEmail = document.getElementById('email');
    const error_email = document.getElementById('error_email');
    const inputDni = document.getElementById('dni');
    const error_dni = document.getElementById('error_dni');
    const inputImg = document.getElementById('opcionImagen');
    const error_img = document.getElementById('error_imagen');
    const inputTelf = document.getElementById('telf');
    const error_telf = document.getElementById('error_telf');
    const inputDateNac = document.getElementById('fecha_nacimiento');
    const error_date_nac = document.getElementById('error_fecha_nacimiento');
    const inputDireccion = document.getElementById('direccion');
    const error_direccion = document.getElementById('error_direccion');
    const inputPermiso = document.getElementById('permiso');
    const error_permiso = document.getElementById('error_permiso');
    const inputPassword = document.getElementById('password');
    const error_password = document.getElementById('error_password');
    const inputConfirmPassword = document.getElementById('confirm_password');
    const error_confirm_password = document.getElementById('error_password_confirmation');
    const form = document.getElementById('registerForm');

    // Asignación de eventos onblur y definición del nombre de la función
    inputNombre.onblur = validaNombre;
    inputEmail.onblur = validaMail;
    inputDni.onblur = validaDni;
    inputImg.onblur = validaImg;
    inputTelf.onblur = validaTelf;
    inputDateNac.onblur = validaDateNac;
    inputDireccion.onblur = validaDireccion;
    inputPermiso.onblur = validaPermiso;
    inputPassword.onblur = validaPassword;
    inputConfirmPassword.onblur = validaConfirmPassword;

    // Asignación del evento submit y definición de la función para validar el formulario
    form.onsubmit = validaForm;

    // Función para validar el nombre
    function validaNombre() {
        const nombre = inputNombre.value.trim();
        const regex = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;

        if (nombre === "") {
            error_nombre.textContent = "El campo es obligatorio";
            inputNombre.classList.add('is-invalid');
            return false;
        } else if (!regex.test(nombre)) {
            error_nombre.textContent = "Solo se permiten letras";
            inputNombre.classList.add('is-invalid');
            return false;
        } else {
            error_nombre.textContent = "";
            inputNombre.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar el mail
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

    // Función para validar el dni
    function validaDni() {
        const dni = inputDni.value.trim().toUpperCase();
        const dniRegex = /^[0-9]{8}[A-Z]$/;
        const letras = "TRWAGMYFPDXBNJZSQVHLCKE";

        if (dni === "") {
            error_dni.textContent = "El campo es obligatorio";
            inputDni.classList.add('is-invalid');
            return false;
        }

        if (!dniRegex.test(dni)) {
            error_dni.textContent = "Formato incorrecto. Debe tener 8 números y 1 letra";
            inputDni.classList.add('is-invalid');
            return false;
        }

        const numero = parseInt(dni.substring(0, 8));
        const letraEsperada = letras[numero % 23];
        const letraIngresada = dni.charAt(8);

        if (letraIngresada !== letraEsperada) {
            error_dni.textContent = "DNI incorrecto, vuelva a introducirlo por favor";
            inputDni.classList.add('is-invalid');
            return false;
        }

        error_dni.textContent = "";
        inputDni.classList.remove('is-invalid');
        return true;
    }

    // Función para validar la imagen
    function validaImg() {
        const img = inputImg.value.trim();

        if (img === "") {
            error_img.textContent = "El campo no debe estar vacío";
            inputImg.classList.add('is-invalid');
            return false;
        } else {
            error_img.textContent = "";
            inputImg.classList.remove('is-invalid');
            return true;
        }
    }

    // Función de validación de telefono
    function validaTelf() {
        const telf = inputTelf.value.trim();
        const regex = /^\d{3}\s\d{2}\s\d{2}\s\d{2}$/;

        if (telf === "") {
            error_telf.textContent = "El campo no debe estar vacío";
            inputTelf.classList.add('is-invalid');
            return false;
        } else if (!regex.test(telf)) {
            error_telf.textContent = "Formato esperado: 123 45 67 89";
            inputTelf.classList.add('is-invalid');
            return false;
        } else {
            error_telf.textContent = "";
            inputTelf.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar la fecha de nacimiento
    function validaDateNac() {
        const dateNac = inputDateNac.value.trim();

        if (dateNac === "") {
            error_date_nac.textContent = "El campo no debe estar vacío";
            inputDateNac.classList.add('is-invalid');
            return false;
        }

        const fechaNacimiento = new Date(dateNac);
        const hoy = new Date();

        if (fechaNacimiento > hoy) {
            error_date_nac.textContent = "La fecha no puede ser posterior a hoy";
            inputDateNac.classList.add('is-invalid');
            return false;
        }

        const edad = hoy.getFullYear() - fechaNacimiento.getFullYear();
        const mes = hoy.getMonth() - fechaNacimiento.getMonth();
        const dia = hoy.getDate() - fechaNacimiento.getDate();

        const esMenor = edad < 18 || (edad === 18 && mes < 0) || (edad === 18 && mes === 0 && dia < 0);

        if (esMenor) {
            error_date_nac.textContent = "Debes tener al menos 18 años";
            inputDateNac.classList.add('is-invalid');
            return false;
        }

        error_date_nac.textContent = "";
        inputDateNac.classList.remove('is-invalid');
        return true;
    }

    // Función para validar el campo de dirección
    function validaDireccion() {
        const direccion = inputDireccion.value.trim();

        if (direccion === "") {
            error_direccion.textContent = "El campo no debe estar vacío";
            inputDireccion.classList.add('is-invalid');
            return false;
        } else {
            error_direccion.textContent = "";
            inputDireccion.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar el campo de permiso
    function validaPermiso() {
        const permiso = inputPermiso.value.trim();

        if (permiso === "") {
            error_permiso.textContent = "El campo no debe estar vacío";
            inputPermiso.classList.add('is-invalid');
            return false;
        } else {
            error_permiso.textContent = "";
            inputPermiso.classList.remove('is-invalid');
            return true;
        }
    }
    // Función para validar la contraseña
    function validaPassword() {
        const password = inputPassword.value.trim();

        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;
        // Requiere al menos 1 minúscula, 1 mayúscula, 1 número, y mínimo 8 caracteres

        if (password === "") {
            error_password.textContent = "El campo es obligatorio";
            inputPassword.classList.add('is-invalid');
            return false;
        } else if (!regex.test(password)) {
            error_password.textContent = "Debe tener al menos 8 caracteres, incluyendo mayúsculas, minúsculas y números";
            inputPassword.classList.add('is-invalid');
            return false;
        } else {
            error_password.textContent = "";
            inputPassword.classList.remove('is-invalid');
            return true;
        }
    }

    // Función para validar que las contraseñas coinciden
    function validaConfirmPassword() {
        const password = inputPassword.value.trim();
        const confirmPassword = inputConfirmPassword.value.trim();

        if (confirmPassword === "") {
            error_confirm_password.textContent = "Debes confirmar la contraseña";
            inputConfirmPassword.classList.add('is-invalid');
            return false;
        } else if (password !== confirmPassword) {
            error_confirm_password.textContent = "Las contraseñas no coinciden";
            inputConfirmPassword.classList.add('is-invalid');
            return false;
        } else {
            error_confirm_password.textContent = "";
            inputConfirmPassword.classList.remove('is-invalid');
            return true;
        }
    }


    // Función para vlidar el formulario
    function validaForm(e) {
        e.preventDefault();

        const validoNombre = validaNombre();
        const validoEmail = validaMail();
        const validoDni = validaDni();
        const validoImg = validaImg();
        const validoTelf = validaTelf();
        const validoDateNac = validaDateNac();
        const validoDireccion = validaDireccion();
        const validoPermiso = validaPermiso();
        const validoPassword = validaPassword();
        const validoConfirmPassword = validaConfirmPassword();
        
        const todoValido = validoNombre && validoEmail && validoDni && validoImg && validoTelf &&
                           validoDateNac && validoDireccion && validoPermiso &&
                           validoPassword && validoConfirmPassword;

        if (todoValido) {
            form.submit();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error al enviar el formulario"
            });
        }
    }
});

//=== CAMARA ===
document.addEventListener('DOMContentLoaded', function () {
    const opcionImagen = document.getElementById('opcionImagen');
    const imagenInput = document.getElementById('imagenInput');
    const camaraContainer = document.getElementById('camaraContainer');
    const video = document.getElementById('videoCamara');
    const btnCapturar = document.getElementById('btnCapturarFoto');
    const canvas = document.getElementById('canvasFoto');

    let stream = null;

    opcionImagen.addEventListener('change', function () {
        if (this.value === 'camara') {
            imagenInput.style.display = 'none';
            camaraContainer.style.display = 'block';
            iniciarCamara();
        } else {
            camaraContainer.style.display = 'none';
            imagenInput.style.display = 'block';
            detenerCamara();
        }
    });

    async function iniciarCamara() {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: true });
            video.srcObject = stream;
        } catch (error) {
            alert('No se pudo acceder a la cámara.');
            opcionImagen.value = 'archivo';
            imagenInput.style.display = 'block';
            camaraContainer.style.display = 'none';
        }
    }

    function detenerCamara() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
    }

    btnCapturar.addEventListener('click', function () {
        const contexto = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        contexto.drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.style.display = 'block';

        // Convertir imagen capturada en archivo para enviar en el form
        canvas.toBlob(function (blob) {
            const archivo = new File([blob], "foto_perfil.png", { type: 'image/png' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(archivo);
            imagenInput.files = dataTransfer.files;
        }, 'image/png');
    });

    window.addEventListener('beforeunload', detenerCamara);
});


// === REGISTRO CON FETCH ===
document.addEventListener('DOMContentLoaded', function () {
    const registerButton = document.getElementById('registerButton');
    const form = document.getElementById('registerForm');
    const loginUrl = "/login";

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        document.querySelectorAll('.error_message').forEach(span => span.textContent = '');

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
            .then(async response => {
                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    console.error("Respuesta inválida del servidor:", e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de servidor',
                        text: 'El servidor respondió con datos inválidos.',
                        confirmButtonText: 'Ok'
                    });
                    return;
                }

                if (response.ok && data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Registro exitoso!',
                        text: data.message,
                        confirmButtonText: 'Aceptar'
                    }).then(() => {
                        window.location.href = loginUrl;
                    });
                } else if (response.status === 422 && data.errors) {
                    for (let campo in data.errors) {
                        const span = document.getElementById('error_' + campo);
                        if (span) {
                            span.textContent = data.errors[campo][0];
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error de validación',
                        text: data.message || 'Por favor corrige los errores del formulario.',
                        confirmButtonText: 'Ok'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error inesperado',
                        text: data.message || 'Ocurrió un problema, intenta más tarde.',
                        confirmButtonText: 'Ok'
                    });
                    console.error('Error detallado:', data.error);
                }
            })
            .catch(error => {
                console.error('Error de red o JS:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Ups, algo salió mal',
                    text: 'No se pudo completar el registro. Intenta más tarde.',
                    confirmButtonText: 'Ok'
                });
            });
    });
});




