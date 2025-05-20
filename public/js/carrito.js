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
      contenedor.innerHTML = `
        <div class="carrito-logo">
          <i class="bi bi-cart"></i>
        </div>
      <h4 class="text-center m-0">No tienes vehículos reservados.</h4>
      `;
      return;
    }

    let total = 0;
    let precioDia = 0;

    data.forEach(vehiculo => {
      const div = document.createElement('div');
      div.classList.add('vehiculo-item');
      div.id = `vehiculo-${vehiculo.reserva.id_reserva}`;

      const imagen = vehiculo.imagenes?.[0]?.nombre_archivo 
        ? `<img src="img/vehiculos/${vehiculo.imagenes[0].nombre_archivo}" alt="Vehículo">`
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
            <div class="acciones">
              <button class="btn-eliminar mt-3" onclick="eliminarReserva(${vehiculo.id_vehiculos_reservas})">
                <i class="fas fa-trash-alt" style="color:white"></i> Quitar
              </button>
            </div>        </div>
      `;
      contenedor.appendChild(div);
      if (vehiculo.reserva?.total_precio) {
        total = parseFloat(vehiculo.reserva.total_precio);
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

function eliminarReserva(idReserva) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción eliminará la reserva del vehículo.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch(`/eliminar-reserva/${idReserva}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const vehiculoDiv = document.getElementById(`vehiculo-${idReserva}`);
          if (vehiculoDiv) {
            vehiculoDiv.remove();
          }
          cargarCarrito();
          Swal.fire('Eliminado', 'La reserva ha sido eliminada.', 'success');
        } else {
          Swal.fire('Error', 'No se pudo eliminar la reserva.', 'error');
        }
      })
      .catch(error => {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un problema al intentar eliminar la reserva.', 'error');
      });
    }
  });
}

