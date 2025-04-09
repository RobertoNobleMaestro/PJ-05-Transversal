// Actualización estadísticas pagina principal
document.addEventListener('DOMContentLoaded', () => {
    fetch('/home-stats')
        .then(res => res.json())
        .then(data => {
            // Actualizar estadísticas
            document.querySelector('.stat-box .fa-users + .stat-content h3').textContent = data.usuariosClientes;
            document.querySelector('.fa-car + .stat-content h3').textContent = new Intl.NumberFormat().format(data.vehiculos);
            document.querySelector('.fa-star + .stat-content h3').textContent = data.valoracionMedia;

            // Actualizar tipos de vehículo dinámicamente
            const container = document.querySelector('.btn-group-toggle');
            if (container && data.tipos) {
                container.innerHTML = ''; // limpiar
                data.tipos.forEach((tipo, index) => {
                    container.innerHTML += `
              <label class="btn btn-outline-primary ${index === 0 ? 'active' : ''}">
                <input type="radio" name="tipoVehiculo" value="${tipo.nombre}" autocomplete="off" ${index === 0 ? 'checked' : ''}>
                ${tipo.nombre}
              </label>
            `;
                });
            }
        })
        .catch(error => console.error('Error al cargar los datos:', error));
});

// Actualización breadcrumbs dependiendo del boton que este seleccionado
document.addEventListener('DOMContentLoaded', () => {
    const breadcrumbTipo = document.getElementById('breadcrumb-tipo');

    // Inicializa con el tipo seleccionado por defecto
    const tipoInicial = document.querySelector('input[name="tipoVehiculo"]:checked');
    if (tipoInicial && breadcrumbTipo) {
        breadcrumbTipo.textContent = tipoInicial.value;
    }

    // Actualiza cuando se cambia el tipo
    document.addEventListener('change', function (e) {
        if (e.target.name === 'tipoVehiculo') {
            const tipoSeleccionado = e.target.value;
            if (breadcrumbTipo) {
                breadcrumbTipo.textContent = tipoSeleccionado;
            }
        }
    });
});

// Actualización imagen de perfil navbar
function refrescarImagenPerfilNavbar() {
    fetch("/perfil-imagen")
        .then(response => response.json())
        .then(data => {
            const img = document.getElementById('navbar-profile-img');
            if (img && data.foto) {
                img.src = data.foto + '?' + new Date().getTime(); // evita caché
            }
        })
        .catch(error => console.error('Error al actualizar la imagen:', error));
}