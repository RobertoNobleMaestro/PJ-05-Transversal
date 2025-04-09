<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carrito de Vehículos Reservados</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 30px;
    }

    .contenedor-principal {
      display: flex;
      gap: 30px;
    }

    .vehiculos-lista {
      flex: 3;
    }

    .resumen-carrito {
      flex: 1;
      background: white;
      padding: 20px;
      border-radius: 16px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
      height: fit-content;
    }

    .vehiculo-item {
      background: white;
      margin-bottom: 20px;
      padding: 20px;
      border-radius: 16px;
      display: flex;
      gap: 20px;
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .vehiculo-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .vehiculo-item img {
      width: 150px;
      height: auto;
      border-radius: 6px;
      object-fit: cover;
    }

    .detalle {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .detalle h3 {
      font-size: 20px;
      margin-bottom: 10px;
    }

    .detalle p {
      margin: 5px 0;
      font-size: 14px;
    }

    .detalle i {
      margin-right: 8px;
      color: #6f42c1;
    }

    .resumen-titulo {
      font-size: 18px;
      font-weight: bold;
    }

    .resumen-precio {
      font-size: 22px;
      color: #6f42c1;
      font-weight: bold;
    }

    .boton {
      display: block;
      margin-top: 20px;
      padding: 12px;
      background: linear-gradient(to right, #6f42c1, #9b59b6);
      color: white;
      text-align: center;
      border-radius: 15px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .boton:hover {
      background: linear-gradient(to right, #5a2b97, #8e44ad);
      transform: translateY(-2px) scale(1.05);
    }

    .subtexto {
      font-size: 14px;
      color: #444;
    }

    hr {
      margin: 12px 0;
      border: 0;
      border-top: 1px solid #ccc;
    }

    .btn-volver {
      display: inline-block;
      background: linear-gradient(to right, #6f42c1, #e100ff);
      color: white;
      padding: 10px 20px;
      border-radius: 15px;
      text-decoration: none;
      font-weight: 500;
      font-size: 1rem;
      transition: all 0.3s ease;
      box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
    }

    .btn-volver:hover {
      background: linear-gradient(to right, #5a2b97, #c400e3);
      transform: scale(1.05);
      color: white;
    }

    .btn-volver i {
      margin-right: 6px;
    }
  </style>
</head>
<body>
  <div class="container mt-4">
    <a href="{{ asset('home') }}" class="btn-volver">
      <i class="fas fa-arrow-left me-2"></i> Volver
    </a>
  </div>
  <h1>Carrito de Vehículos Reservados</h1>
  <div class="contenedor-principal">
    
    <!-- Lista de vehículos -->
    <div class="vehiculos-lista" id="listaVehiculos">Cargando vehículos...</div>

    <!-- Resumen de reserva -->
    <div class="resumen-carrito" id="resumenReserva">
      <div class="resumen-titulo">Tu Reserva 🔒</div>
      <p><span class="subtexto">–</span> <span class="subtexto">0 opiniones</span></p>
      <hr>
      <p class="subtexto">Precio total del día</p>
      <p class="resumen-precio" id="precioDia">EUR€ —</p>
      <p class="subtexto" id="planIncluido">Incluido en el plan: Básico</p>
      <hr>
      <p><strong>Monto total:</strong></p>
      <p class="resumen-precio" id="montoTotal">EUR€ —</p>
      <a href="/finalizar-compra" class="boton">Continuar</a>
    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', cargarCarrito);

    function cargarCarrito() {
      fetch('/ver-carrito', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(res => res.json())
      .then(data => {
        const contenedor = document.getElementById('listaVehiculos');
        contenedor.innerHTML = '';

        if (!data.length) {
          contenedor.innerHTML = '<p>No tienes vehículos reservados.</p>';
          return;
        }

        let total = 0;
        let precioDia = 0;

        data.forEach(vehiculo => {
          const div = document.createElement('div');
          div.classList.add('vehiculo-item');

          const imagen = vehiculo.imagenes?.[0]?.nombre_archivo 
            ? `<img src="/storage/${vehiculo.imagenes[0].nombre_archivo}" alt="Vehículo">`
            : `<div style="width:150px;height:100px;background:#ccc;border-radius:6px;"></div>`;

          const caracteristicas = vehiculo.caracteristicas || {};
          const detalles = [
            caracteristicas.aire_acondicionado ? 'Aire Acondicionado' : '',
            caracteristicas.transmision,
            caracteristicas.num_puertas ? `${caracteristicas.num_puertas} puertas` : '',
            caracteristicas.capacidad_maletero ? `Maletero ${caracteristicas.capacidad_maletero}L` : '',
            caracteristicas.etiqueta_medioambiental ? `Etiqueta ${caracteristicas.etiqueta_medioambiental}` : ''
          ].filter(Boolean).join(' · ');

          div.innerHTML = `
            ${imagen}
            <div class="detalle">
              <h3>${vehiculo.marca} ${vehiculo.modelo}</h3>
              <p><i class="fas fa-car"></i> <strong>Tipo:</strong> ${vehiculo.tipo?.nombre || 'N/D'}</p>
              <p><i class="fas fa-users"></i> <strong>Capacidad:</strong> — pasajeros</p>
              <p><i class="fas fa-map-marker-alt"></i> <strong>Lugar:</strong> ${vehiculo.lugar?.nombre || 'N/D'}</p>
              <p><i class="fas fa-cogs"></i> <strong>Características:</strong> ${detalles}</p>
              <p><i class="fas fa-shield-alt"></i> <strong>Plan:</strong> Básico</p>
            </div>
          `;
          contenedor.appendChild(div);

          // Acumular precios
          if (vehiculo.precio_dia) {
            precioDia += parseFloat(vehiculo.precio_dia);
          }
          if (vehiculo.pago?.total_precio) {
            total += parseFloat(vehiculo.pago.total_precio);
          }
        });

        // Actualizar resumen general
        document.getElementById('precioDia').textContent = `EUR€ ${precioDia.toFixed(2)}`;
        document.getElementById('montoTotal').textContent = `EUR€ ${total.toFixed(2)}`;
      })
      .catch(error => {
        console.error(error);
        document.getElementById('listaVehiculos').innerHTML = '<p style="color:red;">Error al cargar el carrito.</p>';
      });
    }
  </script>
</body>
</html>
