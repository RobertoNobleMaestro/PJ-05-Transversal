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
                // Verificar si usar valoracion o puntuacion según lo que devuelva la API
                const rating = valoracion.valoracion !== undefined ? valoracion.valoracion : valoracion.puntuacion;
                
                for (let i = 0; i < 5; i++) {
                    if (i < rating) {
                        estrellas += '<i class="fas fa-star"></i>';
                    } else {
                        estrellas += '<i class="far fa-star"></i>';
                    }
                }
                
                // Manejar usuario y foto de perfil
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
    // Cargar valoraciones al iniciar la página
    cargarValoraciones(vehiculoId);
    
    // Aquí podrían añadirse más funcionalidades para la página de detalle
}