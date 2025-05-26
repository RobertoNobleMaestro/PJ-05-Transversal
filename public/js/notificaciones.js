// Función para actualizar el contador de notificaciones
function actualizarContadorNotificaciones() {
    fetch('/notificaciones/count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-count');
            if (badge) {
                if (data.count > 0) {
                    badge.textContent = data.count;
                    badge.style.display = 'inline-block';
                } else {
                    badge.style.display = 'none';
                }
            }
        })
        .catch(error => console.error('Error al obtener contador de notificaciones:', error));
}

// Función para mostrar el modal de notificación
function mostrarModalNotificacion() {
    const modal = new bootstrap.Modal(document.getElementById('modalNotificacionAceptacion'));
    const contenidoNotificaciones = document.getElementById('contenido-notificaciones');
    
    // Obtener las notificaciones actuales
    fetch('/notificaciones/count')
        .then(response => response.json())
        .then(data => {
            if (data.count === 0) {
                contenidoNotificaciones.innerHTML = `
                    <div class="mb-4">
                        <i class="fas fa-bell-slash fa-3x mb-3"></i>
                        <h4>No tienes notificaciones</h4>
                        <p class="mb-0">Cuando tengas notificaciones, aparecerán aquí.</p>
                    </div>
                `;
            } else {
                // Obtener los detalles de la solicitud
                fetch('/api/solicitudes/detalles')
                    .then(response => response.json())
                    .then(solicitud => {
                        contenidoNotificaciones.innerHTML = `
                            <div class="mb-4">
                                <i class="fas fa-check-circle fa-3x mb-3"></i>
                                <h4>¡Tu chofer ha aceptado la solicitud!</h4>
                                <div class="mt-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <p><strong>Cliente:</strong></p>
                                            <p><strong>Chofer:</strong></p>
                                            <p><strong>Precio:</strong></p>
                                        </div>
                                        <div class="col-6">
                                            <p>${solicitud.cliente_nombre}</p>
                                            <p>${solicitud.chofer ? solicitud.chofer.usuario.nombre : 'No asignado'}</p>
                                            <p>${solicitud.precio}€</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <button type="button" class="btn btn-success me-2" onclick="procesarPago(${solicitud.id})">
                                        <i class="fas fa-credit-card me-2"></i>Pagar
                                    </button>
                                    <button type="button" class="btn btn-danger" onclick="cancelarSolicitud(${solicitud.id})">
                                        <i class="fas fa-times me-2"></i>Cancelar
                                    </button>
                                </div>
                            </div>
                        `;
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error al obtener detalles de la solicitud:', error);
                        contenidoNotificaciones.innerHTML = `
                            <div class="mb-4">
                                <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                                <h4>Error</h4>
                                <p class="mb-0">No se pudieron cargar los detalles de la solicitud.</p>
                            </div>
                        `;
                        modal.show();
                    });
            }
        })
        .catch(error => {
            console.error('Error al obtener notificaciones:', error);
            contenidoNotificaciones.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-exclamation-circle fa-3x mb-3"></i>
                    <h4>Error</h4>
                    <p class="mb-0">No se pudieron cargar las notificaciones.</p>
                </div>
            `;
            modal.show();
        });
}

// Función para procesar el pago
function procesarPago(solicitudId) {
    // Cerrar el modal de notificación
    $('#modalNotificacionAceptacion').modal('hide');

    // Redirigir a la página de pago
    window.location.href = `/notificacion/pago/${solicitudId}`;
}

// Función para cancelar la solicitud
function cancelarSolicitud(solicitudId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, volver'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar loading
            Swal.fire({
                title: 'Procesando...',
                text: 'Por favor espere',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/api/solicitudes/${solicitudId}/cancelar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cerrar el modal de notificación
                    $('#modalNotificacionAceptacion').modal('hide');
                    
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: '¡Cancelado!',
                        text: 'La solicitud ha sido cancelada correctamente',
                        icon: 'success',
                        confirmButtonColor: '#3085d6'
                    }).then(() => {
                        mostrarModalNotificacion();
                    });
                    
                    // Actualizar contador de notificaciones
                    actualizarContadorNotificaciones();
                } else {
                    throw new Error(data.message || 'Error al cancelar la solicitud');
                }
            })
            .catch(error => {
                console.error('Error al cancelar la solicitud:', error);
                Swal.fire({
                    title: 'Error',
                    text: error.message || 'No se pudo cancelar la solicitud',
                    icon: 'error',
                    confirmButtonColor: '#3085d6'
                });
            });
        }
    });
}

// Función para marcar una notificación como leída
function marcarComoLeida(id) {
    fetch(`/notificaciones/${id}/leer`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            actualizarContadorNotificaciones();
        }
    })
    .catch(error => console.error('Error al marcar notificación como leída:', error));
}

// Inicializar cuando el documento esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Actualizar contador inicial
    actualizarContadorNotificaciones();

    // Actualizar contador cada 10 segundos
    setInterval(actualizarContadorNotificaciones, 10000);
}); 