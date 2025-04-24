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
    <img id="foto_perfil_preview" src="{{ asset('img/' . $user->foto_perfil) }}" class="foto-perfil mb-4" alt="Foto de perfil">
    <div class="info-perfil">
        <div style="width: 60%;" class="d-flex justify-content-center flex-column align-items-center">
        <h5 id="nombre_display">{{ $user->nombre }}</h5>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>Correo electrónico:</strong> <span id="email_display">{{ $user->email }}</span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Teléfono:</strong> <span id="telefono_info">{{ $user->telefono ?? 'No especificado' }}</span></p>
            </div>
        </div>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>DNI:</strong> <span id="DNI_info">{{ $user->DNI }}</span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Fecha de nacimiento:</strong> <span id="fecha_nacimiento_info">{{ $user->fecha_nacimiento }}</span></p>
            </div>
        </div>
        <div class="row w-100 mb-4 row-dashed">
            <div class="col-md-6">
            <p><strong>Dirección:</strong> <span id="direccion_info">{{ $user->direccion }}</span></p>
            </div>
            <div class="col-md-6">
            <p><strong>Licencia de conducir:</strong> <span id="licencia_conducir_info">{{ $user->licencia_conducir }}</span></p>
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
        <button class="btn-editar" data-bs-toggle="modal" data-bs-target="#modalEditarPerfil">Editar perfil</button>            
        </div>
    </div>
  </div>
</div>

<div class="favoritos-section container text-center mt-4 mb-5">
  <hr class="linea-favoritos">
  <h5 class="titulo-favoritos">Vehículos alquilados</h5>
  <hr class="linea-favoritos">

  <div class="row mt-4 justify-content-center">
    @forelse ($reservas as $reserva)
      @foreach($reserva->vehiculosReservas as $vehiculoReserva)
        @php $vehiculo = $vehiculoReserva->vehiculo; @endphp
        @if($vehiculo)
        <div class="col-6 col-md-3 mb-4">
          <div class="card shadow-sm">
            @php
              $modeloLower = strtolower($vehiculo->modelo);
              $imagenNombre = null;
              
              if (strpos($modeloLower, 'focus') !== false) {
                $imagenNombre = 'focus.png';
              } elseif (strpos($modeloLower, 'golf') !== false) {
                $imagenNombre = 'golf.png';
              } elseif (strpos($modeloLower, 'civic') !== false) {
                $imagenNombre = 'civic.png';
              } elseif (strpos($modeloLower, 'corolla') !== false) {
                $imagenNombre = 'corolla.png';
              } elseif (strpos($modeloLower, 'a3') !== false) {
                $imagenNombre = 'a3.png';
              } else {
                $imagenNombre = 'default-car.png';
              }
            @endphp
            <img src="{{ asset('img/vehiculos/' . $imagenNombre) }}" class="card-img-top" alt="{{ $vehiculo->modelo }}" style="height: 200px; object-fit: cover;">
            <div class="card-body text-start p-2">
              <p class="m-0 fw-bold">{{ $vehiculo->marca ?? 'Sin marca' }} {{ $vehiculo->modelo ?? 'Sin modelo' }}</p>
              <p class="m-0">Precio/día: {{ number_format($vehiculo->precio_dia ?? 0, 2, ',', '.') }} €</p>
              @if($vehiculo->caracteristicas)
                <p class="m-0 text-muted small">{{ number_format($vehiculo->kilometraje ?? 0, 0, ',', '.') }} km</p>
              @endif
              <p class="m-0 text-muted small">Estado: {{ $reserva->estado }}</p>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="text-muted small">
                  {{ \Carbon\Carbon::parse($vehiculoReserva->fecha_ini ?? now())->format('d/m/Y') }} - 
                  {{ \Carbon\Carbon::parse($vehiculoReserva->fecha_final ?? now())->format('d/m/Y') }}
                </span>
                <a href="{{ route('facturas.descargar', ['id_reserva' => $reserva->id_reservas]) }}" class="btn btn-sm btn-outline-secondary" title="Descargar factura">
                  <i class="fas fa-file-invoice"></i> Factura
                </a>
              </div>
            </div>
          </div>
        </div>
        @endif
      @endforeach
    @empty
      <div class="col-12 text-center">
        <p>No tienes vehículos alquilados actualmente.</p>
      </div>
    @endforelse
  </div>
</div>

<!-- Modal editar perfil -->
<div class="modal fade" id="modalEditarPerfil" tabindex="-1" aria-labelledby="modalEditarPerfilLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarPerfilLabel">Editar Perfil</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="profileForm" class="needs-validation" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="nombre" class="form-label">Nombre</label>
              <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $user->nombre }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="email" class="form-label">Correo electrónico</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="DNI" class="form-label">DNI</label>
              <input type="text" class="form-control" id="DNI" name="DNI" value="{{ $user->DNI }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
              <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="direccion" class="form-label">Dirección</label>
              <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $user->direccion }}">
            </div>
            <div class="col-md-6 mb-3">
              <label for="licencia_conducir" class="form-label">Licencia de conducir</label>
              <select class="form-control" id="licencia_conducir" name="licencia_conducir" required>
                  <option value="">Selecciona una opción</option>
                  @foreach ($licencias as $licencia)
                      <option value="{{ $licencia }}" {{ $licencia == $user->licencia_conducir ? 'selected' : '' }}>{{ $licencia }}</option>
                  @endforeach
              </select>
              <span class="error_message" id="error_licencia_conducir"></span>
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

<script src="{{ asset('js/perfil.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>