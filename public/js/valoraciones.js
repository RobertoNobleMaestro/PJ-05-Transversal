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

        // Establecer el valor de la valoración en el campo oculto
        const valoracionInput = document.getElementById('valoracion');
        if (valoracionInput) {
            valoracionInput.value = value;
        } else {
            console.error('El elemento con ID "valoracion" no existe en el DOM.');
        }
    });
});

// Manejar el envío del formulario de valoraciones
document.getElementById('form-valoracion')?.addEventListener('submit', function(event) {
    event.preventDefault();
    
    const formData = new FormData(this);
    
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
            
            // Recargar las valoraciones después de enviar una nueva
            cargarValoraciones(vehiculoId);
        } else {
            Swal.fire('Error', 'No se pudo enviar la valoración.', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Hubo un problema al enviar la valoración.', 'error');
    });
});


// Manejar la edición de una valoración
function editarValoracion(id) {
    fetch(`/valoraciones/${id}`)
        .then(response => response.json())
        .then(valoracion => {
            // Rellenar el formulario con los datos de la valoración
            document.getElementById('valoracion').value = valoracion.valoracion;
            document.getElementById('comentario').value = valoracion.comentario;

            // Cambiar el texto del botón a "Actualizar"
            const boton = document.querySelector('#form-valoracion button[type="submit"]');
            boton.textContent = 'Actualizar Valoración';

            // Cambiar el evento de envío para actualizar en lugar de crear
            document.getElementById('form-valoracion').onsubmit = function(event) {
                event.preventDefault();
                actualizarValoracion(id);
            };
        });
}

// Actualizar una valoración
function actualizarValoracion(id) {
    const formData = new FormData(document.getElementById('form-valoracion'));

    fetch(`/valoraciones/${id}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('¡Éxito!', 'Valoración actualizada con éxito.', 'success');
            // Recargar las valoraciones
            cargarValoraciones();
        } else {
            Swal.fire('Error', 'No se pudo actualizar la valoración.', 'error');
        }
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
                    // Recargar las valoraciones
                    cargarValoraciones();
                } else {
                    Swal.fire('Error', 'No se pudo eliminar la valoración.', 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error', 'Hubo un problema al eliminar la valoración.', 'error');
            });
        }
    });
}

function cargarValoraciones(vehiculoId) {
    fetch(`/api/vehiculos/${vehiculoId}/valoraciones`)
        .then(response => {
            return response.json();
        })
        .then(data => {
            const contenedor = document.getElementById('valoraciones-container');
            
            if (data.length === 0) {
                contenedor.innerHTML = '<p class="text-muted">Este vehículo aún no tiene valoraciones.</p>';
                return;
            }
            
            let html = '';
            data.forEach(valoracion => {
                let estrellas = '';
                const rating = valoracion.valoracion !== undefined ? valoracion.valoracion : valoracion.valoracion;
                
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
// Llamar a la función para cargar las valoraciones al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarValoraciones(vehiculoId);
});