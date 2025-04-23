// Objeto para mantener los filtros activos
let activeFilters = {};

/**
 * Aplica los filtros para la búsqueda de reservas
 */
function applyFilters() {
    // Recoger los valores de los filtros
    const usuario = document.getElementById('filterUsuario').value.trim();
    const lugar = document.getElementById('filterLugar').value;
    const estado = document.getElementById('filterEstado').value;
    const fecha = document.getElementById('filterFecha').value;
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        usuario: usuario,
        lugar: lugar,
        estado: estado,
        fecha: fecha
    };
    
    // Cargar reservas con los filtros aplicados
    loadReservas();
}

/**
 * Limpia todos los filtros aplicados
 */
function clearFilters() {
    document.getElementById('filterUsuario').value = '';
    document.getElementById('filterLugar').value = '';
    document.getElementById('filterEstado').value = '';
    document.getElementById('filterFecha').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar reservas sin filtros
    loadReservas();
}

/**
 * Carga las reservas desde la API y las muestra en la tabla
 */
function loadReservas() {
    // Mostrar el indicador de carga
    document.getElementById('loading-reservas').style.display = 'block';
    document.getElementById('reservas-table-container').style.display = 'none';
    
    // Obtener la URL base de los datos
    const baseUrl = document.getElementById('reservas-table-container').dataset.url;
    
    // Construir la URL con los parámetros de filtro
    let url = new URL(baseUrl, window.location.origin);
    
    // Agregar todos los filtros activos a la URL
    Object.keys(activeFilters).forEach(key => {
        if (activeFilters[key]) {
            url.searchParams.append(key, activeFilters[key]);
        }
    });
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar las reservas');
        }
        return response.json();
    })
    .then(data => {
        // Ocultar el indicador de carga
        document.getElementById('loading-reservas').style.display = 'none';
        // Mostrar la tabla
        document.getElementById('reservas-table-container').style.display = 'block';
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#reservas-table tbody');
        tableBody.innerHTML = '';
        
        // Rellenar la tabla con los datos
        if (data.reservas.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="8" class="text-center">No se encontraron reservas con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            data.reservas.forEach(reserva => {
                // Formatear la fecha
                const fechaReserva = new Date(reserva.fecha_reserva);
                const fechaFormateada = fechaReserva.toLocaleDateString('es-ES');
                
                // Formatear el precio
                const precioFormateado = new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(reserva.total_precio);
                
                // Crear badge según el estado
                const estadoBadge = `<span class="badge badge-${reserva.estado}">${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}</span>`;
                
                // Crear lista de vehículos
                let vehiculosHTML = '<ul class="vehiculos-list">';
                reserva.vehiculos_info.forEach(vehiculo => {
                    vehiculosHTML += `<li>${vehiculo.marca} ${vehiculo.modelo}<br>
                        <small>Desde: ${new Date(vehiculo.fecha_ini).toLocaleDateString('es-ES')}</small><br>
                        <small>Hasta: ${new Date(vehiculo.fecha_final).toLocaleDateString('es-ES')}</small>
                    </li>`;
                });
                vehiculosHTML += '</ul>';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${reserva.id_reservas}</td>
                    <td>${reserva.nombre_usuario || 'N/A'}</td>
                    <td>${fechaFormateada}</td>
                    <td>${reserva.nombre_lugar || 'N/A'}</td>
                    <td>${estadoBadge}</td>
                    <td>${precioFormateado}</td>
                    <td>${vehiculosHTML}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="/admin/reservas/${reserva.id_reservas}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteReserva(${reserva.id_reservas}, '${reserva.id_reservas}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-reservas').innerHTML = `<div class="alert alert-danger">Error al cargar reservas: ${error.message}</div>`;
    });
}

/**
 * Elimina una reserva
 * @param {number} id ID de la reserva a eliminar
 * @param {string} idString ID de la reserva (como string) para mostrar en confirmación
 */
function deleteReserva(id, idString) {
    Swal.fire({
        title: `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Eliminar Reserva</span>`,
        html: `<p class="lead">¿Estás seguro de que deseas eliminar la reserva #${idString}?</p><p class="text-muted">Esta acción no se puede deshacer.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar cargando
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
                text: 'Eliminando reserva',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            // Obtener el token CSRF de manera segura
            let csrfToken = '';
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            
            if (metaToken) {
                csrfToken = metaToken.getAttribute('content');
            } else {
                // Si no se encuentra el meta tag, buscar en los formularios existentes
                const hiddenInput = document.querySelector('input[name="_token"]');
                if (hiddenInput) {
                    csrfToken = hiddenInput.value;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: '<p class="lead">No se pudo encontrar el token CSRF</p>',
                        confirmButtonColor: '#9F17BD'
                    });
                    return;
                }
            }
            
            fetch(`/admin/reservas/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-success"><i class="fas fa-check-circle"></i> Completado</span>',
                        html: `<p class="lead">${data.message}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                    loadReservas();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar la reserva'}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: '<p class="lead">Error al procesar la solicitud</p>',
                    confirmButtonColor: '#9F17BD'
                });
            });
        }
    });
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga de reservas
    loadReservas();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('filterUsuario').addEventListener('input', applyFilters);
    document.getElementById('filterLugar').addEventListener('change', applyFilters);
    document.getElementById('filterEstado').addEventListener('change', applyFilters);
    document.getElementById('filterFecha').addEventListener('change', applyFilters);
});
