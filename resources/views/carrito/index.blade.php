<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de Vehículos Reservados</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
@include('layouts.navbar')
  <div class="boton-div-volver">
    <a href="{{ asset('home') }}" class="btn-volver">
      <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
  </div>
  <h1 style="padding-left: 30px;">Carrito de Vehículos Reservados</h1>
  <div class="contenedor-principal">
    
    <!-- Lista de vehículos -->
    <div class="vehiculos-lista" id="listaVehiculos">Cargando vehículos...</div>

    <!-- Resumen de reserva -->
    <div class="resumen-carrito" id="resumenReserva">
      <div class="resumen-titulo">Tu Reserva <i class="fas fa-lock"></i></div>
      <hr>
      <p class="subtexto">Precio total: </p>
      <p class="resumen-precio" id="precioDia">EUR€ —</p>
      <p class="subtexto" id="planIncluido">Incluido en el plan: Básico</p>
      <hr>
      {{-- <a href="/finalizar-compra" class="boton">Continuar</a> --}}
      <a href="{{ route('pago.checkout') }}" class="boton">Continuar</a>
    </div>

  </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/carrito.js') }}"></script>
</html>
