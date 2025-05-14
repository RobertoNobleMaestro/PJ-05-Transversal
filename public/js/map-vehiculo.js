// Función para cuando se inicialice la página 
document.addEventListener("DOMContentLoaded", function () {
    // Verificar si existe el mapa y las coordenadas
    const mapElement = document.getElementById('map');
    if (!mapElement || !window.parkingLocation) {
        console.error('No se encuentra el elemento del mapa o las coordenadas del parking');
        return; // No continuar si no hay mapa o coordenadas
    }
    
    // Definir como constante las variables globales recogidas del html
    let { latitud, longitud } = window.parkingLocation;
    
    // Asegurar que las coordenadas son numéricas
    latitud = parseFloat(latitud);
    longitud = parseFloat(longitud);
    
    // Verificar si las coordenadas son válidas después de convertirlas
    if (isNaN(latitud) || isNaN(longitud)) {
        console.error('Coordenadas inválidas:', latitud, longitud);
        return; // No continuar si las coordenadas son inválidas
    }
    
    console.log('Coordenadas del parking:', latitud, longitud);

    // Definir como constante la variable del mapa pasando como parámetro las variables globales
    const map = L.map('map').setView([latitud, longitud], 15);

    // Definición de las funciones propias de leaflet pasando como parámetro las variables globales donde es necesario
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Añadir marcador en la ubicación
    const marker = L.marker([latitud, longitud]).addTo(map);
    
    // Preparar mensaje del popup
    let popupContent = 'Ubicación del Parking';
    
    // Si hay info del parking en window, usarla para el popup
    if (window.parkingInfo && window.parkingInfo.nombre) {
        popupContent = `<strong>${window.parkingInfo.nombre}</strong><br>Recoge tu vehículo aquí`;
    }
    
    // Mostrar popup
    marker.bindPopup(popupContent).openPopup();
    
    // Forzar recalcular tamaño del mapa después de que todo esté cargado
    setTimeout(() => {
        map.invalidateSize();
    }, 500);
});
