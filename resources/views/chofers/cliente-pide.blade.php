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
    @include('layouts.navbar')
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

    <!-- Modal de Chofer Asignado -->
    <div class="modal fade" id="modalChoferAsignado" tabindex="-1" aria-labelledby="modalChoferAsignadoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalChoferAsignadoLabel">Chofer Asignado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <img id="chofer-foto" src="" alt="Foto del chofer" class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    </div>
                    <div class="chofer-info">
                        <h4 id="chofer-nombre" class="text-center mb-3"></h4>
                        <div class="row">
                            <div class="col-6">
                                <p><strong>Distancia:</strong> <span id="chofer-distancia"></span></p>
                            </div>
                            <div class="col-6">
                                <p><strong>Precio:</strong> <span id="chofer-precio"></span> €</p>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button id="btn-realizar-pago" class="btn btn-primary">Realizar Pago</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Notificación de Aceptación -->
    <div class="modal fade" id="modalNotificacionAceptacion" tabindex="-1" aria-labelledby="modalNotificacionAceptacionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: linear-gradient(135deg, #8c37c1, #6f42c1); color: white;">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalNotificacionAceptacionLabel">
                        <i class="fas fa-bell me-2"></i>¡Solicitud Aceptada!
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>¡Tu chofer ha aceptado la solicitud!</h4>
                        <p class="mb-0">Puedes ver los detalles de tu viaje a continuación.</p>
                    </div>
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" onclick="verDetallesViaje()">
                        Ver Detalles
                    </button>
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