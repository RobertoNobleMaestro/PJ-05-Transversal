// Listener para cargar los elementos del DOM
document.addEventListener('DOMContentLoaded', function(){
    //Recoger los inputs y los mensajes de error del formulario
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

    // Asignación de eventos sobre los inputs
    inputNombre.onblur = validaNombre;
    inputEmail.onblur = validaMail;
    inputDni.onblur = validaDni;
    inputImg.onblur = validaImg;
    inputTelf.onblur = validaTelf;
    inputDateNac.onblur = validaDateNac;
    inputDireccion.onblur = validaDireccion;
    inputPermiso.onblur = validaPermiso;

    // Asignación del evento onsubmit al formulario
    form.onsubmit = validaForm;

    // Función para validar el nombre
    function validaNombre(){
        const nombre = inputNombre.value.trim();
    }

})