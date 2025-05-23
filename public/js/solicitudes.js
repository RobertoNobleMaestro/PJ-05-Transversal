// Función para cargar las solicitudes pendientes
function cargarSolicitudes() {
    fetch('/api/solicitudes/chofer', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                actualizarListaSolicitudes(data.solicitudes);
            } else {
                console.error('Error al cargar solicitudes:', data.message);
                Swal.fire('Error', 'No se pudieron cargar las solicitudes', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'No se pudieron cargar las solicitudes', 'error');
        });
}

// Función para actualizar la lista de solicitudes
function actualizarListaSolicitudes(solicitudes) {
    const listaSolicitudes = document.getElementById('lista-solicitudes');
    listaSolicitudes.innerHTML = '';

    if (solicitudes.length === 0) {
        listaSolicitudes.innerHTML = '<p>No hay solicitudes pendientes.</p>';
        return;
    }

    solicitudes.forEach(solicitud => {
        const solicitudElement = document.createElement('div');
        solicitudElement.className = 'card solicitud-card';
        solicitudElement.innerHTML = `
            <div class="solicitud-header">
                <h5 class="card-title">Solicitud de ${solicitud.cliente.nombre}</h5>
                <p class="card-subtitle mb-2 text-muted">Cliente: ${solicitud.cliente.nombre}</p>
            </div>
            <div class="solicitud-body">
                <p><strong>Origen:</strong> ${solicitud.latitud_origen}, ${solicitud.longitud_origen}</p>
                <p><strong>Destino:</strong> ${solicitud.latitud_destino}, ${solicitud.longitud_destino}</p>
                <p><strong>Precio:</strong> ${solicitud.precio}€</p>
                <div class="d-flex gap-2">
                    <button onclick="aceptarSolicitud(${solicitud.id})" class="btn btn-aceptar">
                        <i class="fas fa-check"></i> Aceptar
                    </button>
                    <button onclick="rechazarSolicitud(${solicitud.id})" class="btn btn-rechazar">
                        <i class="fas fa-times"></i> Rechazar
                    </button>
                </div>
            </div>
        `;
        listaSolicitudes.appendChild(solicitudElement);
    });
}

// Función para aceptar una solicitud
function aceptarSolicitud(id) {
    Swal.fire({
        title: '¿Aceptar solicitud?',
        text: '¿Estás seguro de que deseas aceptar esta solicitud?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/solicitudes/${id}/aceptar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', 'Solicitud aceptada correctamente', 'success');
                    cargarSolicitudes();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo aceptar la solicitud', 'error');
            });
        }
    });
}

// Función para rechazar una solicitud
function rechazarSolicitud(id) {
    Swal.fire({
        title: '¿Rechazar solicitud?',
        text: '¿Estás seguro de que deseas rechazar esta solicitud?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/api/solicitudes/${id}/rechazar`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', 'Solicitud rechazada correctamente', 'success');
                    cargarSolicitudes();
                } else {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'No se pudo rechazar la solicitud', 'error');
            });
        }
    });
}

// Cargar solicitudes al cargar la página
document.addEventListener('DOMContentLoaded', cargarSolicitudes); 