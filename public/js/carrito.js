
document.addEventListener('DOMContentLoaded', cargarCarrito);

function cargarCarrito() {
  fetch('/ver-carrito', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(res => res.json())
  .then(data => {
    console.log(data);
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
      if (vehiculo.reserva?.total_precio) {
        total += parseFloat(vehiculo.reserva.total_precio);
      }
      
    });

    // Actualizar resumen general
    document.getElementById('precioDia').textContent = `EUR€ ${total.toFixed(2)}`;
  })
  .catch(error => {
    console.error(error);
    document.getElementById('listaVehiculos').innerHTML = '<p style="color:red;">Error al cargar el carrito.</p>';
  });
}
