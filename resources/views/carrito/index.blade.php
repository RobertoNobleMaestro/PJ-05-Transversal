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
      background: #f5f5f5;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      height: fit-content;
    }

    .vehiculo-item {
      background: white;
      margin-bottom: 20px;
      padding: 20px;
      border-radius: 10px;
      display: flex;
      gap: 20px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .vehiculo-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
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
      color: #28a745;
    }

    .resumen-titulo {
      font-size: 18px;
      font-weight: bold;
    }

    .resumen-precio {
      font-size: 22px;
      color: #27ae60;
      font-weight: bold;
    }

    .boton {
      display: block;
      width: 100%;
      margin-top: 20px;
      padding: 12px;
      background: #28a745;
      color: white;
      text-align: center;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }

    .boton:hover {
      background: #218838;
    }

    .subtexto {
      font-size: 14px;
      color: #777;
    }

    hr {
      margin: 12px 0;
      border: 0;
      border-top: 1px solid #ccc;
    }
  </style>
</head>
<body>
  <h1>Carrito de Vehículos Reservados</h1>
  <div class="contenedor-principal">
    
    <!-- Lista de vehículos -->
    <div class="vehiculos-lista" id="listaVehiculos">Cargando vehículos...</div>

    <!-- Resumen de reserva -->
    <div class="resumen-carrito" id="resumenReserva">
      <div class="resumen-titulo">Tu Reserva 🔒</div>
      <p><img src="/storage/default-logo.png" style="height: 20px;"> Proveedor</p>
      <p><span class="subtexto">–</span> <span class="subtexto">0 opiniones</span></p>
      <hr>
      <p class="subtexto">Precio total del día</p>
      <p class="resumen-precio" id="precioDia">EUR$ —</p>
      <p class="subtexto" id="planIncluido">Incluido en el plan: Básico</p>
      <hr>
      <p><strong>Monto total:</strong></p>
      <p class="resumen-precio" id="montoTotal">EUR$ —</p>
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
        document.getElementById('precioDia').textContent = `EUR$ ${precioDia.toFixed(2)}`;
        document.getElementById('montoTotal').textContent = `EUR$ ${total.toFixed(2)}`;
      })
      .catch(error => {
        console.error(error);
        document.getElementById('listaVehiculos').innerHTML = '<p style="color:red;">Error al cargar el carrito.</p>';
      });
    }
  </script>
</body>
</html>
