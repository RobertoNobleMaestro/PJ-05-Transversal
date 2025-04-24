/**
 * GESTIÓN DE RESERVAS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar las reservas
 * desde el panel de administración, incluyendo listado, filtrado, y eliminación.
 * Es un componente central para la gestión del ciclo de vida de las reservas.
 */

// Objeto para mantener los filtros activos
let activeFilters = {};

/**
 * applyFilters() - Aplica los filtros para la búsqueda de reservas
 * 
 * Esta función recoge los valores de los diferentes campos de filtro
 * (usuario, lugar, estado, fecha) y actualiza la lista de reservas
 * mostrando solo aquellas que cumplen con los criterios seleccionados.
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
 * clearFilters() - Limpia todos los filtros aplicados y muestra todas las reservas
 * 
 * Esta función resetea todos los campos de filtro a sus valores predeterminados
 * y vuelve a cargar la lista completa de reservas sin filtros aplicados.
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
 * loadReservas() - Carga las reservas desde la API y las muestra en la tabla
 * 
 * Esta función realiza una petición AJAX al servidor para obtener las reservas
 * (aplicando los filtros si existen) y las muestra en la tabla del panel de administración.
 * Incluye el formateo de fechas, precios y estados para una mejor visualización.
 */
function loadReservas() {
    // Mostrar el indicador de carga
    document.getElementById('loading-reservas').style.display = 'block';
    document.getElementById('reservas-table-container').style.display = 'none';
    
    // Preparar los parámetros para la consulta AJAX
    const params = new URLSearchParams();
    
    // Añadir filtros activos a los parámetros
    if (activeFilters.usuario) params.append('usuario', activeFilters.usuario);
    if (activeFilters.lugar) params.append('lugar', activeFilters.lugar);
    if (activeFilters.estado) params.append('estado', activeFilters.estado);
    if (activeFilters.fecha) params.append('fecha', activeFilters.fecha);
    
    // Obtener la URL base de los datos
    const baseUrl = document.getElementById('reservas-table-container').dataset.url;
    
    // Construir la URL con los parámetros de filtro
    let url = new URL(baseUrl, window.location.origin);
    url.search = params.toString();
    
    // Obtener el token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // Realizar petición AJAX para obtener las reservas
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken || ''
        }
    })
    .then(async response => {
        if (!response.ok) {
            // Intentar leer el mensaje de error detallado del cuerpo de la respuesta
            const errorText = await response.text();
            console.error(`Error HTTP: ${response.status}`, errorText);
            
            // Intentar analizar como JSON para obtener el mensaje de error
            try {
                const errorJson = JSON.parse(errorText);
                throw new Error(errorJson.error || `Error HTTP: ${response.status}`);
            } catch (parseError) {
                // Si no es JSON, usar el texto de error como está
                throw new Error(`Error al cargar reservas: ${errorText || `Error HTTP: ${response.status}`}`);
            }
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
            // Mostrar mensaje si no hay reservas
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="8" class="text-center">No se encontraron reservas con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            // Recorrer cada reserva y crear su fila en la tabla
            data.reservas.forEach(reserva => {
                // Formatear la fecha
                const fechaReserva = new Date(reserva.fecha_reserva);
                const fechaFormateada = fechaReserva.toLocaleDateString('es-ES');
                
                // Formatear el precio
                const precioFormateado = new Intl.NumberFormat('es-ES', {
                    style: 'currency',
                    currency: 'EUR'
                }).format(reserva.total_precio);
                
                // Crear badge según el estado (pendiente, confirmada, completada)
                const estadoBadge = `<span class="badge badge-${reserva.estado}">${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}</span>`;
                
                // Crear lista de vehículos asociados a la reserva
                let vehiculosHTML = '<ul class="vehiculos-list">';
                reserva.vehiculos_info.forEach(vehiculo => {
                    vehiculosHTML += `<li>${vehiculo.marca} ${vehiculo.modelo}<br>
                        <small>Desde: ${new Date(vehiculo.fecha_ini).toLocaleDateString('es-ES')}</small><br>
                        <small>Hasta: ${new Date(vehiculo.fecha_final).toLocaleDateString('es-ES')}</small>
                    </li>`;
                });
                vehiculosHTML += '</ul>';
                
                // Crear la fila de la tabla con toda la información
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
        // Manejar errores
        console.error('Error:', error);
        document.getElementById('loading-reservas').innerHTML = `<div class="alert alert-danger">Error al cargar reservas: ${error.message}</div>`;
    });
}

/**
 * deleteReserva(id, idString) - Elimina una reserva del sistema
 * 
 * @param {number} id - ID de la reserva a eliminar
 * @param {string} idString - ID de la reserva (como string) para mostrar en la confirmación
 * 
 * Esta función muestra un diálogo de confirmación y, si el usuario confirma,
 * realiza una petición DELETE al servidor para eliminar la reserva.
 * Después de eliminar la reserva, actualiza la tabla para reflejar el cambio.
 */
function deleteReserva(id, idString) {
    // Mostrar diálogo de confirmación
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
            // Mostrar indicador de carga durante el proceso
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
            
            // Realizar petición DELETE al servidor
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
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                        html: `<p class="lead">${data.message || 'Reserva eliminada exitosamente'}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                    
                    // Recargar la tabla para mostrar los cambios
                    loadReservas();
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">Error al eliminar reserva: ${data.message || 'Error desconocido'}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
                    confirmButtonColor: '#9F17BD'
                });
            });
        }
    });
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos para el filtrado de reservas y carga
 * la lista inicial de reservas cuando la página está lista.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga de reservas
    loadReservas();
    
    // Conservamos solo el botón para limpiar filtros
    document.getElementById('clearFiltersBtn').addEventListener('click', function() {
        clearFilters();
    });
    
    // Implementar filtrado automático en tiempo real
    const filterInputs = [
        document.getElementById('filterUsuario'),
        document.getElementById('filterLugar'),
        document.getElementById('filterEstado')
    ];
    
    // Agregar event listeners para todos los inputs de filtro
    filterInputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function() {
                applyFilters();
            });
        }
    });
    
    // Manejar el caso especial del datepicker
    const fechaInput = document.getElementById('filterFecha');
    if (fechaInput) {
        fechaInput.addEventListener('change', function() {
            applyFilters();
        });
    }
});
