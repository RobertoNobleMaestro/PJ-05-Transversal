<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de Vehículos Reservados</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('css/carrito.css') }}">
</head>
<body>
@include('layouts.navbar')
  <div style="padding-left: 30px; padding-top: 30px; padding-bottom: 30px;">
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
      <p><span class="subtexto">–</span> <span class="subtexto">0 opiniones</span></p>
      <hr>
      <p class="subtexto">Precio total del día</p>
      <p class="resumen-precio" id="precioDia">EUR€ —</p>
      <p class="subtexto" id="planIncluido">Incluido en el plan: Básico</p>
      <hr>
      {{-- <a href="/finalizar-compra" class="boton">Continuar</a> --}}
      <a href="{{ route('pago.checkout') }}" class="boton">Continuar</a>
    </div>

  </div>
</body>
<script src="{{ asset('js/carrito.js') }}"></script>
</html>
