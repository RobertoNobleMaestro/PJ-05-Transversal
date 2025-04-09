document.addEventListener('DOMContentLoaded', function () {
    const inputNombre = document.getElementById('nombre');
    const error_nombre = document.getElementById('error_nombre');
    const inputEmail = document.getElementById('email');
    const error_email = document.getElementById('error_email');
    const inputDni = document.getElementById('dni');
    const error_dni = document.getElementById('error_dni');
    const inputImg = document.getElementById('imagen');
    const error_img = document.getElementById('error_imagen');
    const inputTelf = document.getElementById('telf');
    const error_telf = document.getElementById('error_telf');
    const inputDateNac = document.getElementById('fecha_nacimiento');
    const error_date_nac = document.getElementById('error_fecha_nacimiento');
    const inputDireccion = document.getElementById('direccion');
    const error_direccion = document.getElementById('error_direccion');
    const inputPermiso = document.getElementById('permiso');
    const error_permiso = document.getElementById('error_permiso');
    const form = document.getElementById('registerForm');

    inputNombre.onblur = validaNombre;
    inputEmail.onblur = validaMail;
    inputDni.onblur = validaDni;
    inputImg.onblur = validaImg;
    inputTelf.onblur = validaTelf;
    inputDateNac.onblur = validaDateNac;
    inputDireccion.onblur = validaDireccion;
    inputPermiso.onblur = validaPermiso;
    form.onsubmit = validaForm;

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

        const todoValido = validoNombre && validoEmail && validoDni && validoImg && validoTelf && validoDateNac && validoDireccion && validoPermiso;

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
