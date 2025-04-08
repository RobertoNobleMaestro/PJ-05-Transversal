
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
