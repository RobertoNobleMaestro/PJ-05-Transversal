let isEditMode = false;
let idValoracionEditando = null;

// Manejar la apertura del formulario de valoraciones
document.getElementById('btnAbrirFormulario')?.addEventListener('click', function() {
    const boton = document.getElementById('btnAbrirFormulario');
    const formulario = document.getElementById('formulario-valoracion');

    // Eliminar el botón
    boton.remove();

    // Mostrar el formulario con animación
    formulario.style.display = 'block';
    formulario.style.opacity = '0';
    formulario.style.transform = 'translateY(-20px)';

    // Animación de entrada
    setTimeout(() => {
        formulario.style.opacity = '1';
        formulario.style.transform = 'translateY(0)';
    }, 10);

    // Desplazar la pantalla al formulario
    formulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
});

// Manejar la selección de estrellas
document.querySelectorAll('.rating i')?.forEach(star => {
    star.addEventListener('click', function() {
        const value = this.getAttribute('data-value');
        const stars = document.querySelectorAll('.rating i');

        stars.forEach((s, index) => {
            if (index < value) {
                s.classList.remove('far');
                s.classList.add('fas');
            } else {
                s.classList.remove('fas');
                s.classList.add('far');
            }
        });

        const valoracionInput = document.getElementById('valoracion');
        if (valoracionInput) {
            valoracionInput.value = value;
        } else {
            console.error('El elemento con ID "valoracion" no existe en el DOM.');
        }
    });
});

// Manejar el envío del formulario
document.getElementById('form-valoracion')?.addEventListener('submit', function(event) {
    event.preventDefault();
    if (isEditMode && idValoracionEditando) {
        actualizarValoracion(idValoracionEditando);
    } else {
        crearValoracion();
    }
});

// Función para crear nueva valoración
function crearValoracion() {
    const formData = new FormData(document.getElementById('form-valoracion'));

    fetch('/valoraciones', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Gracias!', 'Tu valoración ha sido enviada.', 'success');
            document.getElementById('form-valoracion').reset();
            document.getElementById('formulario-valoracion').style.display = 'none';
            cargarValoraciones(vehiculoId);
        } else {
            Swal.fire('Error', data.message || 'No se pudo enviar la valoración.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Hubo un problema al enviar la valoración.', 'error');
    });
}

// Función para actualizar una valoración existente
function actualizarValoracion(id) {
    const form = document.getElementById('form-valoracion');
    const formData = new FormData(form);
    formData.append('_method', 'PUT'); // Requerido por Laravel

    fetch(`/valoraciones/editar/${id}`, {
        method: 'POST', // Laravel acepta POST + _method para PUT
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Éxito!', 'Valoración actualizada con éxito.', 'success');
            form.reset();
            document.getElementById('formulario-valoracion').style.display = 'none';
            isEditMode = false;
            idValoracionEditando = null;
            cargarValoraciones(vehiculoId);
        } else {
            Swal.fire('Error', data.message || 'No se pudo actualizar la valoración.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema al actualizar la valoración.', 'error');
    });
}

// Manejar la edición de una valoración
function editarValoracion(id) {
    fetch(`/valoraciones/${id}`)
        .then(response => response.json())
        .then(valoracion => {
            document.getElementById('valoracion').value = valoracion.valoracion;
            document.getElementById('comentario').value = valoracion.comentario;

            // Mostrar estrellas seleccionadas visualmente
            document.querySelectorAll('.rating i').forEach((s, index) => {
                if (index < valoracion.valoracion) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });

            const boton = document.querySelector('#form-valoracion button[type="submit"]');
            boton.textContent = 'Actualizar Valoración';

            isEditMode = true;
            idValoracionEditando = id;

            const formulario = document.getElementById('formulario-valoracion');
            formulario.style.display = 'block';
            formulario.scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudo cargar la valoración para editar.', 'error');
        });
}

// Manejar la eliminación de una valoración
function eliminarValoracion(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "¡No podrás revertir esta acción!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6f42c1',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/valoraciones/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', 'Valoración eliminada con éxito.', 'success');
                    cargarValoraciones(vehiculoId);
                } else {
                    Swal.fire('Error', data.message || 'No se pudo eliminar la valoración.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Hubo un problema al eliminar la valoración.', 'error');
            });
        }
    });
}

function cargarValoraciones(vehiculoId) {
    fetch(`/api/vehiculos/${vehiculoId}/valoraciones`)
        .then(response => response.json())
        .then(data => {
            const contenedor = document.getElementById('valoraciones-container');
            
            if (data.length === 0) {
                contenedor.innerHTML = '<p class="text-muted">Este vehículo aún no tiene valoraciones.</p>';
                return;
            }
            
            let html = '';
            data.forEach(valoracion => {
                let estrellas = '';
                const rating = valoracion.valoracion !== undefined ? valoracion.valoracion : 0;
                
                for (let i = 0; i < 5; i++) {
                    estrellas += i < rating 
                        ? '<i class="fas fa-star"></i>' 
                        : '<i class="far fa-star"></i>';
                }
                
                const usuarioNombre = valoracion.usuario ? valoracion.usuario.nombre : 'Usuario';
                const fotoPerfil = valoracion.usuario && valoracion.usuario.foto_perfil 
                    ? `/img/${valoracion.usuario.foto_perfil}` 
                    : '/img/default.png';
                
                html += `
                    <div class="valoracion mb-4">
                        <p>
                            <img src="${fotoPerfil}" 
                                 class="rounded-circle" 
                                 style="width: 35px; height: 35px; object-fit: cover;" alt="Perfil">
                            <strong>${usuarioNombre}</strong>
                        </p>
                        <p>${estrellas}</p>
                        <p>${valoracion.comentario}</p>
                        ${userId === valoracion.id_usuario ? `
                            <div>
                                <button style="border:none;" class="btn-volver" onclick="editarValoracion(${valoracion.id_valoraciones})">Editar</button>
                                <button class="btn-eliminar" onclick="eliminarValoracion(${valoracion.id_valoraciones})">Borrar</button>
                            </div>
                        ` : ''}
                    </div>
                `;
            });

            contenedor.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('valoraciones-container').innerHTML = 
                '<p class="text-danger">Ha ocurrido un error al cargar las valoraciones. Por favor, intenta nuevamente.</p>';
        });
}

document.addEventListener('DOMContentLoaded', function() {
    cargarValoraciones(vehiculoId);
});
