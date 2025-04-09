/**
 * Carga las valoraciones de un vehículo mediante Fetch API
 * @param {number} vehiculoId - ID del vehículo
 */
function cargarValoraciones(vehiculoId) {
    fetch(`/api/vehiculos/${vehiculoId}/valoraciones`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al cargar valoraciones');
            }
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
                const rating = valoracion.valoracion !== undefined ? valoracion.valoracion : valoracion.puntuacion;
                
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

/**
 * Inicializa las funcionalidades de la página de detalle de vehículo
 * @param {number} vehiculoId - ID del vehículo
 */
function iniciarDetalleVehiculo(vehiculoId) {
    cargarValoraciones(vehiculoId);
}

document.addEventListener('DOMContentLoaded', function () {
    const btnCarrito = document.getElementById('btnAñadirCarrito');
    if (btnCarrito) {
        btnCarrito.addEventListener('click', function () {
            const vehiculoId = btnCarrito.getAttribute('data-vehiculo-id');

            if (!vehiculoId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se encontró el ID del vehículo.'
                });
                return;
            }

            fetch(`/vehiculos/${vehiculoId}/añadir-al-carrito`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({})
            })
            .then(response => response.json())
            .then(data => {
                if (data.alert) {
                    Swal.fire({
                        icon: data.alert.icon || 'info',
                        title: data.alert.title || '',
                        text: data.alert.text || ''
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Atención',
                        text: 'No se recibió una respuesta válida del servidor.'
                    });
                }
            })
            .catch(error => {
                console.error('Error al añadir al carrito:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un problema al procesar tu solicitud.'
                });
            });
        });
    }
});
