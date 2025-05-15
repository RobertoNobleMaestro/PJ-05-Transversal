<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Link a css -->
    <link rel="stylesheet" href="{{asset('css/chofers/styles.css')}}">
    <!-- Link a leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <title>Solicita vehículo</title>
</head>
<body>
<!-- Navbar -->
@include('layouts.navbar')

<!-- Mapa con la geolocalización del usuario-->
<div id="map"></div>
<br>
<!-- Choferes disponibles -->
<h3> Choferes disponibles </h3>

<script src="{{asset('js/client-map.js')}}"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</body>
</html>