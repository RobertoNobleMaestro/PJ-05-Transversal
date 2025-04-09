document.addEventListener('DOMContentLoaded', function() {
    cargarDatosPerfil();
    configurarFormulario();

    // 👇 Fijamos el "action" correcto en el formulario
    const pathParts = window.location.pathname.split('/');
    const id = pathParts[pathParts.length - 1];
    document.getElementById('profileForm').action = `/perfil/${id}/actualizar`;
});

function cargarDatosPerfil() {
    const pathParts = window.location.pathname.split('/');
    const id = pathParts[pathParts.length - 1];

    if (!id) {
        Swal.fire('Error', 'ID de usuario no encontrado', 'error');
        return;
    }

    fetch(`/perfil/${id}/datos`)
        .then(response => response.json())
        .then(data => {
            // Mostrar datos en la vista principal
            document.getElementById('nombre_display').textContent = data.nombre;
            document.getElementById('email_display').textContent = data.email;
            document.getElementById('DNI_info').textContent = data.DNI;
            const fecha = data.fecha_nacimiento;
            const fechaFormateada = fecha.split("T")[0];
            document.getElementById('fecha_nacimiento_info').textContent = fechaFormateada;
            document.getElementById('direccion_info').textContent = data.direccion;
            document.getElementById('licencia_conducir_info').textContent = data.licencia_conducir;
            
            if (data.foto_perfil) {
                document.getElementById('foto_perfil_preview').src = `/img/${data.foto_perfil}`;
            }

            document.getElementById('nombre').value = data.nombre;
            document.getElementById('email').value = data.email;
            document.getElementById('DNI').value = data.DNI;
            document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento.split('T')[0]; // solo YYYY-MM-DD
            document.getElementById('direccion').value = data.direccion;
            document.getElementById('licencia_conducir').value = data.licencia_conducir;
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar los datos del perfil', 'error');
        });
}




function configurarFormulario() {
    const form = document.getElementById('profileForm');
    const fotoInput = document.getElementById('foto_perfil');

    // fotoInput.addEventListener('change', function(e) {
    //     const file = e.target.files[0];
    //     if (file) {
    //         const reader = new FileReader();
    //         reader.onload = function(e) {
    //             document.getElementById('foto_perfil_preview').src = e.target.result;
    //         }
    //         reader.readAsDataURL(file);
    //     }
    // });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        const pathParts = window.location.pathname.split('/');
        const id = pathParts[pathParts.length - 1];

        
        if (!id) {
            Swal.fire('Error', 'ID de usuario no encontrado', 'error');
            return;
        }

        const formData = new FormData(form);
        if (fotoInput.files.length > 0) {
            formData.append('foto_perfil', fotoInput.files[0]);
        }

        fetch(`/perfil/${id}/actualizar`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarPerfil'));
            if (modal) {
                modal.hide();
            }

            cargarDatosPerfil();
            Swal.fire('¡Éxito!', data.message, 'success');
        })

        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo actualizar el perfil', 'error');
        });
    });
}
document.getElementById('modalEditarPerfil').addEventListener('show.bs.modal', cargarDatosPerfil);

document.getElementById('btnAbrirCamara').addEventListener('click', function () {
    const camaraContainer = document.getElementById('camaraContainer');
    const video = document.getElementById('videoCamara');

    camaraContainer.style.display = 'block';

    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function (stream) {
            video.srcObject = stream;
        })
        .catch(function (error) {
            Swal.fire('Error', 'No se pudo acceder a la cámara.', 'error');
            console.error(error);
        });
});

document.getElementById('btnCapturarFoto').addEventListener('click', function () {
    const pathParts = window.location.pathname.split('/');
    const id = pathParts[pathParts.length - 1];
    const video = document.getElementById('videoCamara');
    const canvas = document.getElementById('canvasFoto');
    const context = canvas.getContext('2d');

    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    context.drawImage(video, 0, 0);

    // Detener la cámara
    video.srcObject.getTracks().forEach(track => track.stop());

    // Mostrar preview y generar archivo para subir
    canvas.toBlob(function(blob) {
        const file = new File([blob], 'foto_perfil.jpg', { type: 'image/jpeg' });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);

        // Asignar el archivo al input file
        document.getElementById('foto_perfil').files = dataTransfer.files;

        // Mostrar imagen en preview
        const reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById('foto_perfil_preview').src = e.target.result;
        };
        reader.readAsDataURL(file);

        // --- AUTOMÁTICAMENTE ENVIAR LOS DATOS CON FETCH ---
        const form = document.getElementById('profileForm');
        const formData = new FormData(form);

        fetch(`/perfil/${id}/actualizar`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(data => {
            Swal.fire('¡Foto actualizada!', data.message, 'success');

            const user = data.user;

            // Ocultar cámara
            document.getElementById('camaraContainer').style.display = 'none';
        })
        .catch(error => {
            Swal.fire('Error', error.message, 'error');
            console.error(error);
        });
        // --- FIN FETCH ---
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById('profileForm');
    const nombre = document.getElementById('nombre');
    const email = document.getElementById('email');
    const DNI = document.getElementById('DNI');
    const fechaNacimiento = document.getElementById('fecha_nacimiento');
    const direccion = document.getElementById('direccion');
    const licenciaConducir = document.getElementById('licencia_conducir');
    const submitButton = form.querySelector('button[type="submit"]');

    // Errores
    let nombreError = document.createElement('div');
    let emailError = document.createElement('div');
    let DNIError = document.createElement('div');
    let fechaNacimientoError = document.createElement('div');
    let direccionError = document.createElement('div');
    let licenciaConducirError = document.createElement('div');

    [nombreError, emailError, DNIError, fechaNacimientoError, direccionError, licenciaConducirError].forEach(error => {
        error.style.color = 'red';
        error.style.fontSize = '12px';
    });

    nombre.after(nombreError);
    email.after(emailError);
    DNI.after(DNIError);
    fechaNacimiento.after(fechaNacimientoError);
    direccion.after(direccionError);
    licenciaConducir.after(licenciaConducirError);

    // Estado inicial del formulario
    let originalValues = {
        nombre: nombre.value,
        email: email.value,
        DNI: DNI.value,
        fechaNacimiento: fechaNacimiento.value,
        direccion: direccion.value,
        licenciaConducir: licenciaConducir.value
    };

    function checkChanges() {
        let isChanged = (
            nombre.value !== originalValues.nombre ||
            email.value !== originalValues.email ||
            DNI.value !== originalValues.DNI ||
            fechaNacimiento.value !== originalValues.fechaNacimiento ||
            direccion.value !== originalValues.direccion ||
            licenciaConducir.value !== originalValues.licenciaConducir
        );
        submitButton.disabled = !isChanged;
    }

    function validateNombre() {
        const value = nombre.value.trim();
        const regex = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]+$/;

        if (value === "") {
            nombreError.textContent = "El nombre está vacío";
            nombre.style.borderColor = "red";
            return false;
        } else if (value.length < 3) {
            nombreError.textContent = "Debe tener al menos 3 caracteres";
            nombre.style.borderColor = "red";
            return false;
        } else if (!regex.test(value)) {
            nombreError.textContent = "No puede contener números ni caracteres especiales";
            nombre.style.borderColor = "red";
            return false;
        } else {
            nombreError.textContent = "";
            nombre.style.borderColor = "";
            return true;
        }
    }

    function validateEmail() {
        const value = email.value.trim();
        const regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (value === "") {
            emailError.textContent = "El email está vacío";
            email.style.borderColor = "red";
            return false;
        } else if (!regex.test(value)) {
            emailError.textContent = "El email no tiene un formato válido";
            email.style.borderColor = "red";
            return false;
        } else {
            emailError.textContent = "";
            email.style.borderColor = "";
            return true;
        }
    }

    function validateDNI() {
        const value = DNI.value.trim();
        const regex = /^\d{8}[A-Za-z]$/;

        if (value === "") {
            DNIError.textContent = "El DNI está vacío";
            DNI.style.borderColor = "red";
            return false;
        } else if (!regex.test(value)) {
            DNIError.textContent = "El DNI no tiene un formato válido";
            DNI.style.borderColor = "red";
            return false;
        } else {
            DNIError.textContent = "";
            DNI.style.borderColor = "";
            return true;
        }
    }

    function validateFechaNacimiento() {
        const value = fechaNacimiento.value.trim();
        const today = new Date();
        const birthDate = new Date(value);

        if (value === "") {
            fechaNacimientoError.textContent = "La fecha de nacimiento está vacía";
            fechaNacimiento.style.borderColor = "red";
            return false;
        } else if (birthDate >= today) {
            fechaNacimientoError.textContent = "La fecha de nacimiento no puede ser en el futuro";
            fechaNacimiento.style.borderColor = "red";
            return false;
        } else {
            fechaNacimientoError.textContent = "";
            fechaNacimiento.style.borderColor = "";
            return true;
        }
    }

    function validateDireccion() {
        const value = direccion.value.trim();

        if (value === "") {
            direccionError.textContent = "La dirección está vacía";
            direccion.style.borderColor = "red";
            return false;
        } else {
            direccionError.textContent = "";
            direccion.style.borderColor = "";
            return true;
        }
    }

    function validateLicenciaConducir() {
        const value = licenciaConducir.value.trim();

        if (value === "") {
            document.getElementById('error_licencia_conducir').textContent = "Debe seleccionar una licencia de conducir";
            licenciaConducir.style.borderColor = "red";
            return false;
        } else {
            document.getElementById('error_licencia_conducir').textContent = "";
            licenciaConducir.style.borderColor = "";
            return true;
        }
    }

    function validateForm(event) {
        let isValid = true;
        if (!validateNombre()) isValid = false;
        if (!validateEmail()) isValid = false;
        if (!validateDNI()) isValid = false;
        if (!validateFechaNacimiento()) isValid = false;
        if (!validateDireccion()) isValid = false;
        if (!validateLicenciaConducir()) isValid = false;

        if (!isValid) {
            event.preventDefault();
        }
    }

    // Eventos
    nombre.addEventListener('input', () => { validateNombre(); checkChanges(); });
    email.addEventListener('input', () => { validateEmail(); checkChanges(); });
    DNI.addEventListener('input', () => { validateDNI(); checkChanges(); });
    fechaNacimiento.addEventListener('input', () => { validateFechaNacimiento(); checkChanges(); });
    direccion.addEventListener('input', () => { validateDireccion(); checkChanges(); });
    licenciaConducir.addEventListener('input', () => { validateLicenciaConducir(); checkChanges(); });
    form.addEventListener('submit', validateForm);

    // Deshabilitar el botón de guardar al inicio
    submitButton.disabled = true;
});