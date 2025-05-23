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

function mostrarDetallesSolicitud(solicitud) {
    const infoPanel = document.getElementById('infoPanel');
    infoPanel.innerHTML = `
        <h6 style="margin-bottom: 1rem; font-weight: 600;">Información del Servicio</h6>
        ${crearInfoItem('Cliente', solicitud.cliente.nombre)}
        ${crearInfoItem('Precio', formatearPrecio(solicitud.precio))}
        ${crearInfoItem('Punto de Recogida', `${solicitud.latitud_origen}, ${solicitud.longitud_origen}`)}
        ${crearInfoItem('Destino', `${solicitud.latitud_destino}, ${solicitud.longitud_destino}`)}
    `;

    mostrarModal();

    // Esperar a que el modal esté visible antes de inicializar el mapa
    requestAnimationFrame(() => {
        inicializarMapa(solicitud);
        // Forzar una actualización del tamaño del mapa
        setTimeout(() => {
            if (mapa) {
                mapa.invalidateSize();
            }
        }, 100);
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Botones para ver el mapa
    document.querySelectorAll('.ver-mapa').forEach(button => {
        button.addEventListener('click', function() {
            const solicitud = JSON.parse(this.getAttribute('data-solicitud'));
            mostrarDetallesSolicitud(solicitud);
        });
    });

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


