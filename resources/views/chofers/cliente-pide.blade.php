<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Link a css -->
    <link rel="stylesheet" href="{{asset('css/chofers/styles.css')}}">
    <!-- Link a leaflet -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <title>Solicita vehículo</title>
</head>
<body>
<!-- Navbar -->
{{-- @include('layouts.navbar') --}}
    <h2 class="mb-4">Solicita un vehículo</h2>
    
    <!-- Elemento oculto con el ID del cliente -->
    <div data-cliente-id="{{ Auth::id() }}" style="display: none;"></div>
    
    <div class="main-container">
        <!-- Mapa con la geolocalización del usuario-->
        <div class="mapa-container">
            <div id="map"></div>
            <div class="search-box">
                <form id="formDestino" class="search-form">
                    <div class="input-group">
                        <input type="text" class="form-control" id="destino" placeholder="¿A dónde quieres ir?" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Panel derecho con choferes -->
        <div class="panel-derecho">
            <!-- Choferes disponibles -->
            <div class="choferes-disponibles">
                <h3>Choferes disponibles</h3>
                <div id="lista-choferes">
                    <!-- Aquí se cargarán los choferes dinámicamente -->
                </div>
            </div>
        </div>
    </div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<!-- Verificación de SweetAlert2 -->
<script>
    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 no está cargado correctamente');
    }
</script>

<script src="{{asset('js/cliente-pide.js')}}"></script>
</body>
</html>