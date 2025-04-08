<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/perfil/style.css') }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <title>Perfil</title>
</head>
<body>
@include('layouts.navbar')
<div class="container mt-4">
  <a href="{{ asset('home') }}" class="btn-volver">
    <i class="fas fa-arrow-left me-2"></i> Volver
  </a>
</div>

<div class="perfil-contenido container">
  <div class="perfil-box d-flex flex-column align-items-center p-4">
    <img id="foto_perfil_preview" src="" class="foto-perfil mb-4" alt="Foto de perfil">
    <div class="info-perfil">
        <div style="width: 60%;" class="d-flex justify-content-center flex-column align-items-center">
        <h5 id="nombre_display"></h5>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>Correo electrónico:</strong> <span id="email_display"></span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Teléfono:</strong> <span id="telefono_info"></span></p>
            </div>
        </div>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>DNI:</strong> <span id="DNI_info"></span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Fecha de nacimiento:</strong> <span id="fecha_nacimiento_info"></span></p>
            </div>
        </div>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>Dirección:</strong> <span id="direccion_info"></span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Licencia de conducir:</strong> <span id="licencia_conducir_info"></span></p>
            </div>
        </div>
        <div class="mb-3 text-center">
          <button type="button" class="btn" id="btnAbrirCamara">
            <i class="fas fa-camera me-2"></i>Tomar foto con cámara
          </button>
        </div>

        <!-- Vista previa cámara -->
        <div class="text-center mb-3" id="camaraContainer" style="display:none;">
          <video id="videoCamara" autoplay style="width: 100%; max-width: 300px; border-radius: 10px;"></video>
          <br>
          <button type="button" class="btn btn-success mt-2" id="btnCapturarFoto">Capturar</button>
          <canvas id="canvasFoto" style="display:none;"></canvas>
        </div>
        <button class="btn btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">Editar perfil</button>            
        </div>
    </div>
  </div>
</div>

  <div class="favoritos-section container text-center mt-4 mb-5">
    <hr class="linea-favoritos">
    <h5 class="titulo-favoritos">Vehiculos alquilados</h5>
    <hr class="linea-favoritos">

    <div class="row mt-4 justify-content-center">
      <!-- Ejemplo de tarjeta de vehículo -->
      <div class="col-6 col-md-3 mb-4">
        <div class="card shadow-sm">
          <img src="" class="card-img-top" alt="Vehículo">
          <div class="card-body text-start p-2">
            <p class="m-0 fw-bold">Coche 1</p>
            <p class="m-0">20.000 €</p>
            <p class="m-0 text-muted small">4.000 km</p>
            <div class="d-flex justify-content-between mt-2">
              <i class="fas fa-shopping-cart"></i>
            </div>
          </div>
        </div>
      </div>
      <!-- Repite esta tarjeta para más favoritos -->
    </div>
  </div>
</div>
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarPerfilLabel">Editar Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="profileForm" class="needs-validation" method="POST" enctype="multipart/form-data">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="DNI" class="form-label">DNI</label>
              <input type="text" class="form-control" id="DNI" name="DNI" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
              <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" name="direccion">
            </div>
            <div class="col-md-6 mb-3">
              <label for="licencia_conducir" class="form-label">Licencia de conducir</label>
              <input type="text" class="form-control" id="licencia_conducir" name="licencia_conducir">
            </div>
            <div class="col-12 mb-3">
              <label for="foto_perfil" class="form-label">Foto de perfil</label>
              <input type="file" class="form-control" id="foto_perfil" name="foto_perfil">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" form="profileForm" class="btn-editar">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</body>
<script src="{{ asset('js/perfil.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</html>
