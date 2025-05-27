// Archivo de solicitudes optimizado
let mapa = null;

// Formatear precio en formato Euros
function formatearPrecio(precio) {
    return parseFloat(precio).toFixed(2) + ' €';
}

// Mostrar/ocultar modal
function mostrarModal() {
    const modal = document.getElementById('modalVisualizador');
    if (modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
}

function cerrarModal() {
    const modal = document.getElementById('modalVisualizador');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }
    if (mapa) {
        mapa.remove();
        mapa = null;
    }
}

// Cargar solicitudes simplificado
function cargarSolicitudes() {
    console.log('Cargando solicitudes...');
    
    // Referencias a elementos del DOM
    const tbody = document.querySelector('tbody');
    const loadingDiv = document.getElementById('loading-solicitudes');
    
    if (!tbody || !loadingDiv) {
        console.error('No se encontraron elementos necesarios en el DOM');
        return;
    }
    
    // Mostrar cargando
    loadingDiv.style.display = 'block';
    
    // Hacer la solicitud
    const xhr = new XMLHttpRequest();
    xhr.open('GET', window.location.origin + '/api/solicitudes/chofer', true);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    
    xhr.onload = function() {
        console.log('Respuesta recibida:', xhr.status);
        console.log('Texto de respuesta:', xhr.responseText);
        
        // Ocultar el spinner de carga
        loadingDiv.style.display = 'none';
        tbody.style.display = 'table-row-group';
        
        if (xhr.status === 200) {
            try {
                const data = JSON.parse(xhr.responseText);
                console.log('Datos parseados:', data);
                
                // Limpiar tabla
                tbody.innerHTML = '';
                
                if (data.success && Array.isArray(data.solicitudes)) {
                    if (data.solicitudes.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="3" class="text-center">No hay solicitudes pendientes</td></tr>';
                    } else {
                        // Procesar cada solicitud
                        data.solicitudes.forEach(function(solicitud) {
                            // Asegurarse de que existan todos los datos necesarios
                            if (!solicitud || !solicitud.id) {
                                console.error('Solicitud inválida:', solicitud);
                                return;
                            }
                            
                            // Obtener datos seguros
                            const id = solicitud.id;
                            const nombre = solicitud.cliente && solicitud.cliente.nombre ? solicitud.cliente.nombre : 'Cliente desconocido';
                            const precio = solicitud.precio ? formatearPrecio(solicitud.precio) : 'N/A';
                            
                            // Crear fila de manera segura
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>${nombre}</td>
                                <td>${precio}</td>
                                <td>
                                    <button type="button" class="btn-action btn-aceptar" onclick="aceptarSolicitud(${id})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-rechazar" onclick="rechazarSolicitud(${id})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-info" onclick="mostrarDetalle(${id})">
                                        <i class="fas fa-info"></i>
                                    </button>
                                </td>
                            `;
                            tbody.appendChild(tr);
                        });
                    }
                } else {
                    tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error en la respuesta del servidor</td></tr>';
                    console.error('Estructura de respuesta inesperada:', data);
                }
            } catch (error) {
                tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error al procesar la respuesta</td></tr>';
                console.error('Error al parsear JSON:', error);
            }
        } else {
            tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error del servidor: ' + xhr.status + '</td></tr>';
            console.error('Error en la solicitud HTTP:', xhr.status);
        }
    };
    
    xhr.onerror = function() {
        loadingDiv.style.display = 'none';
        tbody.style.display = 'table-row-group';
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error de conexión</td></tr>';
        console.error('Error de red en la solicitud');
    };
    
    xhr.send();
}

// Función para aceptar solicitud
function aceptarSolicitud(id) {
    if (!id) {
        console.error('ID de solicitud inválido');
        return;
    }
    
    console.log('Aceptando solicitud:', id);
    Swal.fire({
        title: '¿Aceptar solicitud?',
        text: "Aceptarás esta solicitud de transporte",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Sí, aceptar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/api/solicitudes/${id}/aceptar`, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            Swal.fire('¡Aceptada!', 'La solicitud ha sido aceptada correctamente.', 'success');
                            cargarSolicitudes();
                        } else {
                            Swal.fire('Error', response.message || 'Error al aceptar la solicitud', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Error al procesar la respuesta', 'error');
                        console.error('Error al parsear JSON:', error);
                    }
                } else {
                    Swal.fire('Error', 'Error del servidor: ' + xhr.status, 'error');
                    console.error('Error en la solicitud HTTP:', xhr.status);
                }
            };
            
            xhr.onerror = function() {
                Swal.fire('Error', 'Error de conexión', 'error');
                console.error('Error de red en la solicitud');
            };
            
            xhr.send(JSON.stringify({}));
        }
    });
}

// Función para rechazar solicitud
function rechazarSolicitud(id) {
    if (!id) {
        console.error('ID de solicitud inválido');
        return;
    }
    
    console.log('Rechazando solicitud:', id);
    Swal.fire({
        title: '¿Rechazar solicitud?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, rechazar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const xhr = new XMLHttpRequest();
            xhr.open('POST', `/api/solicitudes/${id}/rechazar`, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            Swal.fire('¡Rechazada!', 'La solicitud ha sido rechazada correctamente.', 'success');
                            cargarSolicitudes();
                        } else {
                            Swal.fire('Error', response.message || 'Error al rechazar la solicitud', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Error al procesar la respuesta', 'error');
                        console.error('Error al parsear JSON:', error);
                    }
                } else {
                    Swal.fire('Error', 'Error del servidor: ' + xhr.status, 'error');
                    console.error('Error en la solicitud HTTP:', xhr.status);
                }
            };
            
            xhr.onerror = function() {
                Swal.fire('Error', 'Error de conexión', 'error');
                console.error('Error de red en la solicitud');
            };
            
            xhr.send(JSON.stringify({}));
        }
    });
}

// Función para mostrar detalles
function mostrarDetalle(id) {
    console.log('Mostrando detalles de solicitud:', id);
    Swal.fire({
        title: 'Detalles de la Solicitud',
        text: `Información de la solicitud ID: ${id}`,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
}

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando solicitudes...');
    
    // Cargar solicitudes inmediatamente
    cargarSolicitudes();
    
    // Actualizar cada 30 segundos
    setInterval(cargarSolicitudes, 30000);
    
    // Configurar eventos del modal
    const cerrarModalBtn = document.getElementById('cerrarModal');
    if (cerrarModalBtn) {
        cerrarModalBtn.addEventListener('click', cerrarModal);
    }
    
    const modalVisualizador = document.getElementById('modalVisualizador');
    if (modalVisualizador) {
        modalVisualizador.addEventListener('click', function(e) {
            if (e.target === this) {
                cerrarModal();
            }
        });
    }
    
    // Cerrar modal con ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modalVisualizador && modalVisualizador.style.display === 'block') {
            cerrarModal();
        }
    });
});