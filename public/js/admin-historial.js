// Objeto para mantener los filtros activos
let activeFilters = {};

/**
 * Aplica los filtros para la búsqueda de reservas en el historial
 */
function applyFilters() {
    // Recoger los valores de los filtros
    const usuario = document.getElementById('filterUsuario').value.trim();
    const lugar = document.getElementById('filterLugar').value;
    const estado = document.getElementById('filterEstado').value;
    const fechaDesde = document.getElementById('filterFechaDesde').value;
    const fechaHasta = document.getElementById('filterFechaHasta').value;
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        usuario: usuario,
        lugar: lugar,
        estado: estado,
        fecha_desde: fechaDesde,
        fecha_hasta: fechaHasta
    };
    
    // Cargar historial con los filtros aplicados
    loadHistorial();
}

/**
 * Limpia todos los filtros aplicados
 */
function clearFilters() {
    document.getElementById('filterUsuario').value = '';
    document.getElementById('filterLugar').value = '';
    document.getElementById('filterEstado').value = '';
    document.getElementById('filterFechaDesde').value = '';
    document.getElementById('filterFechaHasta').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar historial sin filtros
    loadHistorial();
}

/**
 * Actualiza las estadísticas mostradas
 * @param {Object} stats Objeto con estadísticas
 */
function updateStats(stats) {
    document.getElementById('total-reservas').textContent = stats.total_reservas;
    document.getElementById('reservas-completadas').textContent = stats.reservas_completadas;
    document.getElementById('reservas-pendientes').textContent = stats.reservas_pendientes;
    document.getElementById('reservas-canceladas').textContent = stats.reservas_canceladas;
    document.getElementById('ingreso-total').textContent = formatCurrency(stats.ingreso_total);
}

/**
 * Formatea un valor numérico a formato de moneda
 * @param {number} value Valor a formatear
 * @returns {string} Valor formateado como moneda
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

/**
 * Carga el historial de reservas desde la API y lo muestra en la tabla
 */
function loadHistorial() {
    // Mostrar el indicador de carga
    document.getElementById('loading-historial').style.display = 'block';
    document.getElementById('historial-table-container').style.display = 'none';
    
    // Obtener la URL base de los datos
    const baseUrl = document.getElementById('historial-table-container').dataset.url;
    
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
            throw new Error('Error al cargar el historial');
        }
        return response.json();
    })
    .then(data => {
        // Ocultar el indicador de carga
        document.getElementById('loading-historial').style.display = 'none';
        // Mostrar la tabla
        document.getElementById('historial-table-container').style.display = 'block';
        
        // Actualizar las estadísticas
        updateStats(data.stats);
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#historial-table tbody');
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
                const precioFormateado = formatCurrency(reserva.total_precio);
                
                // Crear badge según el estado
                const estadoBadge = `<span class="badge badge-${reserva.estado}">${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}</span>`;
                
                // Crear lista de vehículos
                let vehiculosHTML = '<ul class="vehiculos-list">';
                reserva.vehiculos_info.forEach(vehiculo => {
                    vehiculosHTML += `<li>${vehiculo.marca} ${vehiculo.modelo}</li>`;
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
                        <button class="btn btn-sm btn-outline-info" onclick="showDetails(${reserva.id_reservas})" title="Ver detalles">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-historial').innerHTML = `<div class="alert alert-danger">Error al cargar historial: ${error.message}</div>`;
    });
}

/**
 * Muestra los detalles de una reserva específica
 * @param {number} id ID de la reserva
 */
function showDetails(id) {
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando detalles...',
        text: `Obteniendo información de la reserva #${id}`,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            fetch(`/admin/reservas/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Formatear la información para mostrar en modal
                let vehiculosHtml = '';
                data.reserva.vehiculos_info.forEach(vehiculo => {
                    vehiculosHtml += `
                        <div class="mb-3 border-bottom pb-2">
                            <p class="mb-1"><strong>Vehículo:</strong> ${vehiculo.marca} ${vehiculo.modelo}</p>
                            <p class="mb-1"><strong>Desde:</strong> ${new Date(vehiculo.fecha_ini).toLocaleDateString('es-ES')}</p>
                            <p class="mb-1"><strong>Hasta:</strong> ${new Date(vehiculo.fecha_final).toLocaleDateString('es-ES')}</p>
                            <p class="mb-0"><strong>Precio:</strong> ${formatCurrency(vehiculo.precio_unitario)}</p>
                        </div>
                    `;
                });
                
                // Mostrar modal con detalles
                Swal.fire({
                    title: `<span class="text-primary"><i class="fas fa-info-circle"></i> Detalles de Reserva #${id}</span>`,
                    html: `
                        <div class="text-start">
                            <p><strong>Usuario:</strong> ${data.reserva.nombre_usuario}</p>
                            <p><strong>Fecha de Reserva:</strong> ${new Date(data.reserva.fecha_reserva).toLocaleDateString('es-ES')}</p>
                            <p><strong>Lugar:</strong> ${data.reserva.nombre_lugar}</p>
                            <p><strong>Estado:</strong> <span class="badge badge-${data.reserva.estado}">${data.reserva.estado.charAt(0).toUpperCase() + data.reserva.estado.slice(1)}</span></p>
                            <p><strong>Precio Total:</strong> ${formatCurrency(data.reserva.total_precio)}</p>
                            
                            <h5 class="mt-4 mb-3">Vehículos</h5>
                            ${vehiculosHtml}
                        </div>
                    `,
                    confirmButtonColor: '#9F17BD',
                    confirmButtonText: 'Cerrar'
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error al cargar detalles',
                    text: error.message,
                    confirmButtonColor: '#9F17BD'
                });
            });
        }
    });
}

// Inicialización cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga del historial
    loadHistorial();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('filterUsuario').addEventListener('input', applyFilters);
    document.getElementById('filterLugar').addEventListener('change', applyFilters);
    document.getElementById('filterEstado').addEventListener('change', applyFilters);
    document.getElementById('filterFechaDesde').addEventListener('change', applyFilters);
    document.getElementById('filterFechaHasta').addEventListener('change', applyFilters);
});
