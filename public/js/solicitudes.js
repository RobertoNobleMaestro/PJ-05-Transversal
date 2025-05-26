let mapa = null;

function formatearPrecio(precio) {
    return precio.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
}

function crearInfoItem(label, valor) {
    return `
        <div class="info-item">
            <div class="info-label">${label}</div>
            <div class="info-value">${valor}</div>
        </div>
    `;
}

function mostrarModal() {
    const modal = document.getElementById('modalVisualizador');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
}

function cerrarModal() {
    const modal = document.getElementById('modalVisualizador');
    modal.style.display = 'none';
    document.body.style.overflow = '';
    if (mapa) {
        mapa.remove();
        mapa = null;
    }
}

function inicializarMapa(solicitud) {
    if (mapa) {
        mapa.remove();
        mapa = null;
    }

    try {
        mapa = L.map('mapa');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(mapa);

        const iconoOrigen = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const iconoDestino = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

        const origen = [parseFloat(solicitud.latitud_origen), parseFloat(solicitud.longitud_origen)];
        const destino = [parseFloat(solicitud.latitud_destino), parseFloat(solicitud.longitud_destino)];

        L.marker(origen, { icon: iconoOrigen })
            .addTo(mapa)
            .bindPopup('Punto de Recogida');

        L.marker(destino, { icon: iconoDestino })
            .addTo(mapa)
            .bindPopup('Destino');

        const bounds = L.latLngBounds([origen, destino]);
        mapa.fitBounds(bounds.pad(0.1));

        return true;
    } catch (error) {
        console.error('Error al inicializar el mapa:', error);
        return false;
    }
}

// Función para cargar las solicitudes
function cargarSolicitudes() {
    const tbody = document.querySelector('tbody');
    const loadingDiv = document.getElementById('loading-solicitudes');
    
    // Mostrar loading
    loadingDiv.classList.remove('d-none');
    
    // Ocultar tabla mientras carga
    tbody.style.display = 'none';
    
    fetch('/api/solicitudes/chofer')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpiar tabla
                tbody.innerHTML = '';
                
                if (data.solicitudes.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="3" class="text-center">No hay solicitudes pendientes</td>
                        </tr>
                    `;
                } else {
                    // Llenar tabla con las solicitudes
                    data.solicitudes.forEach(solicitud => {
                        tbody.innerHTML += `
                            <tr>
                                <td>${solicitud.cliente.nombre}</td>
                                <td>${parseFloat(solicitud.precio).toFixed(2)} €</td>
                                <td>
                                    <button type="button" class="btn-action btn-aceptar"
                                            onclick="aceptarSolicitud(${solicitud.id})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-rechazar"
                                            onclick="rechazarSolicitud(${solicitud.id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }
            } else {
                throw new Error(data.message || 'Error al cargar las solicitudes');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-danger">
                        Error al cargar las solicitudes. Por favor, recarga la página.
                    </td>
                </tr>
            `;
        })
        .finally(() => {
            // Ocultar loading y mostrar tabla
            loadingDiv.classList.add('d-none');
            tbody.style.display = '';
        });
}

// Función para aceptar una solicitud
function aceptarSolicitud(id) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    Swal.fire({
        title: '¿Aceptar solicitud?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/solicitudes/${id}/aceptar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Aceptada!',
                        'La solicitud ha sido aceptada correctamente.',
                        'success'
                    );
                    // Recargar las solicitudes
                    cargarSolicitudes();
                } else {
                    throw new Error(data.message || 'Error al aceptar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error',
                    'No se pudo aceptar la solicitud. Por favor, inténtalo de nuevo.',
                    'error'
                );
            });
        }
    });
}

// Función para rechazar una solicitud
function rechazarSolicitud(id) {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    Swal.fire({
        title: '¿Rechazar solicitud?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/solicitudes/${id}/rechazar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire(
                        '¡Rechazada!',
                        'La solicitud ha sido rechazada correctamente.',
                        'success'
                    );
                    // Recargar las solicitudes
                    cargarSolicitudes();
                } else {
                    throw new Error(data.message || 'Error al rechazar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire(
                    'Error',
                    'No se pudo rechazar la solicitud. Por favor, inténtalo de nuevo.',
                    'error'
                );
            });
        }
    });
}

// Cargar solicitudes al iniciar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarSolicitudes();
    
    // Actualizar cada 30 segundos
    setInterval(cargarSolicitudes, 30000);
    
    // Escuchar eventos de solicitud aceptada
    window.Echo.private('solicitud.{{ Auth::id() }}')
        .listen('SolicitudAceptada', (e) => {
            cargarSolicitudes();
        });
});

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Cerrar modal con el botón
    document.getElementById('cerrarModal').addEventListener('click', cerrarModal);

    // Cerrar modal al hacer clic fuera
    document.getElementById('modalVisualizador').addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModal();
        }
    });

    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && document.getElementById('modalVisualizador').style.display === 'block') {
            cerrarModal();
        }
    });
});


