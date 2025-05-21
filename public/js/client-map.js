document.addEventListener('DOMContentLoaded', function() {
    var defaultCoords = [51.505, -0.09]; // Coordenadas por defecto
    var map = L.map('map').setView(defaultCoords, 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    function onLocationFound(e) {
        var userCoords = [e.latitude, e.longitude];

        map.setView(userCoords, 13);

        L.marker(userCoords).addTo(map)
            .bindPopup('Ubicación Actual')
            .openPopup();
    }

    function onLocationError(e) {
        console.error("No se pudo obtener la ubicación:", e.message);
        L.marker(defaultCoords).addTo(map)
            .bindPopup('Ubicación por defecto.<br>Activa la ubicación para mostrar la tuya.')
            .openPopup();
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                onLocationFound({
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                });
            },
            onLocationError
        );
    } else {
        alert("Tu navegador no soporta geolocalización.");
        onLocationError({ message: "No soportado" });
    }
});