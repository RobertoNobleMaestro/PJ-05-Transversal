// Variables globales
let map;
let userMarker;
let choferMarkers = [];
let destinoMarker;
let userLat;
let userLng;
let destinoLat;
let destinoLng;
let destinoSeleccionado = false;

// Definimos los iconos personalizados
const userIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const choferIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

const destinoIcon = L.icon({
    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41],
    popupAnchor: [1, -34],
    shadowSize: [41, 41]
});

// Función auxiliar para mostrar alertas
function mostrarAlerta(icono, titulo, texto) {
    return Swal.fire({
        icon: icono,
        title: titulo,
        text: texto,
        confirmButtonColor: '#8c37c1',
        confirmButtonText: 'Aceptar'
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar el mapa con vista por defecto
    map = L.map('map').setView([0, 0], 11);
    
    // Añadir la capa de OpenStreetMap
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Obtener la ubicación del usuario usando la API de geolocalización
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;

                // Centrar el mapa en la ubicación del usuario
                map.setView([userLat, userLng], 13);

                // Añadir marcador del usuario con el icono personalizado
                userMarker = L.marker([userLat, userLng], {icon: userIcon})
                    .addTo(map)
                    .bindPopup('Tu ubicación')
                    .openPopup();

                // Buscar choferes cercanos a la ubicación del usuario
                obtenerChoferesCercanos(userLat, userLng);
            },
            function(error) {
                console.error("Error al obtener la ubicación:", error);
                mostrarAlerta('error', 'Error de geolocalización', 'No se pudo obtener tu ubicación. Por favor, asegúrate de tener la geolocalización activada.');
            }
        );
    } else {
        mostrarAlerta('error', 'Navegador no compatible', 'Tu navegador no soporta geolocalización.');
    }

    // Manejar el envío del formulario de destino
    document.getElementById('formDestino').addEventListener('submit', function(e) {
        e.preventDefault();
        const destino = document.getElementById('destino').value;
        geocodificarDestino(destino);
    });
});

function geocodificarDestino(direccion) {
    // API 
    // Usar el servicio de geocodificación de OpenStreetMap
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(direccion)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data && data.length > 0) {
                destinoLat = parseFloat(data[0].lat);
                destinoLng = parseFloat(data[0].lon);
                
                // Actualizar o crear el marcador de destino
                if (destinoMarker) {
                    destinoMarker.setLatLng([destinoLat, destinoLng]);
                } else {
                    destinoMarker = L.marker([destinoLat, destinoLng], {icon: destinoIcon})
                        .addTo(map)
                        .bindPopup('Destino');
                }
                
                destinoSeleccionado = true;
            } else {
                mostrarAlerta('error', 'Dirección no encontrada', 'No se pudo encontrar la dirección especificada.');
            }
        })
        .catch(error => {
            console.error('Error en la geocodificación:', error);
            mostrarAlerta('error', 'Error al procesar la dirección', 'Por favor, inténtalo de nuevo.');
        });
}

function calcularPrecioEstimado(choferId, choferLat, choferLng) {
    if (!destinoSeleccionado) {
        mostrarAlerta('warning', 'Destino no seleccionado', 'Por favor, introduce primero tu destino.');
        return;
    }

    // Calcular distancias usando la fórmula de Haversine
    const distanciaChoferUsuario = calcularDistancia(choferLat, choferLng, userLat, userLng);
    const distanciaUsuarioDestino = calcularDistancia(userLat, userLng, destinoLat, destinoLng);
    const distanciaTotal = distanciaChoferUsuario + distanciaUsuarioDestino;

    // Constantes con el precio por km que definimos en la empresa y del cálculo del precio total
    const precioPorKm = 2.5;
    const precioTotal = distanciaTotal * precioPorKm;

    // Mostrar el precio estimado en el elemento del chofer
    const precioElement = document.getElementById(`precio-chofer-${choferId}`);
    if (precioElement) {
        precioElement.querySelector('.precio-total').textContent = precioTotal.toFixed(2);
        precioElement.classList.remove('d-none');
    }
}

function calcularDistancia(lat1, lon1, lat2, lon2) {
    const R = 6371; // Radio de la Tierra en km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function obtenerChoferesCercanos(latitud, longitud) {
    // Limpiar marcadores anteriores para evitar duplicados
    choferMarkers.forEach(marker => marker.remove());
    choferMarkers = [];

    // Realizar la petición al servidor para obtener choferes cercanos
    fetch(`/api/choferes-cercanos?latitud=${latitud}&longitud=${longitud}`)
        .then(response => response.json())
        .then(choferes => {
            // Crear marcadores para cada chofer con el icono personalizado
            choferes.forEach(chofer => {
                const marker = L.marker([chofer.latitud, chofer.longitud], {icon: choferIcon})
                    .addTo(map)
                    .bindPopup(`
                        <strong>${chofer.nombre}</strong><br>
                        Distancia: ${chofer.distancia.toFixed(2)} km
                    `);
                choferMarkers.push(marker);
            });

            // Actualizar la lista de choferes disponibles en el panel lateral
            actualizarListaChoferes(choferes);
        })
        .catch(error => {
            console.error("Error al obtener choferes:", error);
            mostrarAlerta('error', 'Error al obtener choferes', 'No se pudieron obtener los choferes disponibles.');
        });
}

function actualizarListaChoferes(choferes) {
    const listaChoferes = document.getElementById('lista-choferes');
    if (!listaChoferes) return;

    // Limpiar lista actual
    listaChoferes.innerHTML = '';

    if (choferes.length === 0) {
        listaChoferes.innerHTML = '<p>No hay choferes disponibles en tu zona.</p>';
        return;
    }

    // Crear elementos para cada chofer en el panel lateral
    choferes.forEach(chofer => {
        const choferElement = document.createElement('div');
        choferElement.className = 'chofer-item';
        choferElement.innerHTML = `
            <div class="chofer-info">
                <h4>${chofer.nombre}</h4>
                <p>Distancia: ${chofer.distancia.toFixed(2)} km</p>
                <div class="precio-chofer d-none" id="precio-chofer-${chofer.id}">
                    <p class="fw-bold">Precio total: <span class="precio-total">0</span> €</p>
                </div>
            </div>
            <button onclick="seleccionarChofer(${chofer.id}, ${chofer.latitud}, ${chofer.longitud})" class="btn-seleccionar">
                <i class="fas fa-chevron-right"></i>
            </button>
        `;
        listaChoferes.appendChild(choferElement);
    });
}

function seleccionarChofer(choferId, choferLat, choferLng) {
    if (!destinoSeleccionado) {
        mostrarAlerta('warning', 'Destino no seleccionado', 'Por favor, introduce primero tu destino.');
        return;
    }
    
    // Calcular y mostrar el precio estimado para este chofer
    calcularPrecioEstimado(choferId, choferLat, choferLng);
    
    // TODO: Implementar la lógica adicional de selección
    console.log("Chofer seleccionado:", choferId);
}
