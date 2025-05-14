// Función para cuando se inicialice la página 
document.addEventListener("DOMContentLoaded", function () {
    // Definir como constante las variables globales recogidas del html
    const { latitude, longitude } = window.parkingLocation;

    // Definir como constante la variable del mapa pasando como parámetro las variables globales
    const map = L.map('map').setView([latitude, longitude], 15);

    // Definición de las funciones propias de leaflet pasando como parámetro las variables globales donde es necesario
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup('Ubicación del Parking')
        .openPopup();
});
