/**
 * HISTORIAL DE RESERVAS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar el historial
 * de reservas desde el panel de administración, incluyendo filtrado avanzado,
 * visualización de estadísticas y consulta de detalles específicos.
 * Proporciona una visión completa del estado del negocio y las operaciones realizadas.
 */

// Objeto para mantener los filtros activos
let activeFilters = {};

/**
 * applyFilters() - Aplica los filtros para la búsqueda de reservas en el historial
 * 
 * Esta función recoge los valores de los diferentes campos de filtro
 * (usuario, lugar, estado, rango de fechas) y actualiza la lista del historial
 * mostrando solo aquellas reservas que cumplen con los criterios seleccionados.
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
 * clearFilters() - Limpia todos los filtros aplicados y muestra todas las reservas
 * 
 * Esta función resetea todos los campos de filtro a sus valores predeterminados
 * y vuelve a cargar el historial completo de reservas sin filtros aplicados.
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
 * updateStats(stats) - Actualiza las estadísticas mostradas en el panel
 * 
 * @param {Object} stats - Objeto con las estadísticas a mostrar
 * 
 * Esta función actualiza los contadores y valores en el panel de estadísticas
 * mostrando información como el total de reservas, reservas por estado e ingresos.
 */
function updateStats(stats) {
    document.getElementById('total-reservas').textContent = stats.total_reservas;
    document.getElementById('reservas-completadas').textContent = stats.reservas_completadas;
    document.getElementById('reservas-confirmadas').textContent = stats.reservas_confirmadas;
    document.getElementById('reservas-pendientes').textContent = stats.reservas_pendientes;
    document.getElementById('reservas-canceladas').textContent = stats.reservas_canceladas;
    const ingresoTotalEl = document.getElementById('ingreso-total');
    const card = ingresoTotalEl.closest('.stat-card');

    // Actualiza con animación de conteo (si lo mantienes)
    animateCountUp(ingresoTotalEl, 0, stats.ingreso_total);

    // Aplica animación visual al card
    card.classList.add('highlight');
    setTimeout(() => card.classList.remove('highlight'), 1000);


}

/**
 * formatCurrency(value) - Formatea un valor numérico a formato de moneda
 * 
 * @param {number} value - Valor a formatear
 * @returns {string} - Valor formateado como moneda (EUR)
 * 
 * Esta función utiliza la API Intl.NumberFormat para dar formato de moneda
 * a un valor numérico, siguiendo el formato español (€).
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

/**
 * loadHistorial() - Carga el historial de reservas desde la API y lo muestra en la tabla
 * 
 * Esta función realiza una petición AJAX al servidor para obtener el historial de reservas
 * (aplicando los filtros si existen) y lo muestra en la tabla del panel de administración.
 * También actualiza el panel de estadísticas con los datos recibidos.
 */
function loadHistorial() {
    // Mostrar el indicador de carga
    document.getElementById('loading-historial').style.display = 'block';
    document.getElementById('historial-table-container').style.display = 'none';

    // Preparar los parámetros para la consulta AJAX
    const params = new URLSearchParams();

    // Asegurarnos de que los parámetros coincidan exactamente con lo que espera el controlador
    if (activeFilters.usuario) params.append('usuario', activeFilters.usuario);
    if (activeFilters.lugar) params.append('lugar', activeFilters.lugar);
    if (activeFilters.estado) params.append('estado', activeFilters.estado);
    if (activeFilters.fecha_desde) params.append('fechaDesde', activeFilters.fecha_desde);
    if (activeFilters.fecha_hasta) params.append('fechaHasta', activeFilters.fecha_hasta);

    // Obtener la URL base de los datos (admin.historial.data)
    let baseUrl = document.getElementById('historial-table-container').dataset.url;
    // Asegurar que tenemos una URL válida, si no usar la ruta por defecto
    if (!baseUrl) {
        baseUrl = '/admin/historial/data';
    }

    // Construir la URL con los parámetros de filtro
    let url = new URL(baseUrl, window.location.origin);

    // Añadir los parámetros de filtro a la URL
    url.search = params.toString();

    // Obtener el token CSRF
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    console.log('URL del historial:', url.toString());
    console.log('Token CSRF:', csrfToken);

    // Realizar la petición AJAX con el token CSRF incluido
    fetch(url.toString(), {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        },
        credentials: 'same-origin' // Importante para mantener la sesión
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            // Ocultar el indicador de carga
            document.getElementById('loading-historial').style.display = 'none';
            document.getElementById('historial-table-container').style.display = 'block';

            // Actualizar tabla con los datos recibidos
            const tableBody = document.querySelector('#historial-table tbody');
            tableBody.innerHTML = ''; // Limpiar la tabla primero

            console.log('Datos recibidos:', data); // Debug para ver la respuesta

            // Actualizar estadísticas si existen
            if (data.stats) {
                document.getElementById('total-reservas').textContent = data.stats.total || 0;
                document.getElementById('reservas-completadas').textContent = data.stats.completadas || 0;
                document.getElementById('reservas-pendientes').textContent = data.stats.pendientes || 0;
                document.getElementById('reservas-canceladas').textContent = data.stats.canceladas || 0;
                document.getElementById('ingreso-total').textContent = formatCurrency(data.stats.ingresos || 0);
            }

            // Verificar si hay datos de reservas
            if (data.reservas && data.reservas.length > 0) {
                // Añadir cada reserva a la tabla
                data.reservas.forEach(reserva => {
                    const row = document.createElement('tr');

                    // Formatear la fecha para mostrar
                    const fecha = new Date(reserva.fecha_reserva).toLocaleDateString('es-ES');

                    // Definir clase CSS según el estado
                    let estadoClass = '';
                    switch (reserva.estado) {
                        case 'pendiente':
                            estadoClass = 'text-warning';
                            break;
                        case 'confirmada':
                            estadoClass = 'text-success';
                            break;
                        case 'cancelada':
                            estadoClass = 'text-danger';
                            break;
                        case 'completada':
                            estadoClass = 'text-info';
                            break;
                        default:
                            estadoClass = '';
                    }

                    // Depurar los datos de usuario y vehículos
                    console.log('Datos de reserva:', reserva.id_reserva, reserva);

                    // Extraer o preparar el nombre del usuario de manera segura
                    let nombreUsuario = 'N/A';
                    if (reserva.nombre_usuario && reserva.nombre_usuario !== 'undefined') {
                        nombreUsuario = reserva.nombre_usuario;
                    } else if (reserva.usuario && reserva.usuario.nombre) {
                        nombreUsuario = reserva.usuario.nombre;
                    }

                    // Preparar la información de vehículos
                    let vehiculosHtml = 'N/A';
                    if (reserva.vehiculos_info && Array.isArray(reserva.vehiculos_info) && reserva.vehiculos_info.length > 0) {
                        vehiculosHtml = `<ul class="vehiculos-list">`;
                        reserva.vehiculos_info.forEach(vehiculo => {
                            if (vehiculo && vehiculo.marca && vehiculo.modelo) {
                                vehiculosHtml += `<li>${vehiculo.marca} ${vehiculo.modelo}</li>`;
                            }
                        });
                        vehiculosHtml += `</ul>`;
                    }

                    // Construir la fila con los datos
                    row.innerHTML = `
                    <td>${nombreUsuario}</td>
                    <td class="text-center">${fecha}</td>
                    <td>${reserva.nombre_lugar || 'N/A'}</td>
                    <td class="text-center">
                        <span class="badge badge-${reserva.estado}">
                            ${reserva.estado.charAt(0).toUpperCase() + reserva.estado.slice(1)}
                        </span>
                    </td>
                    <td class="text-center">${formatCurrency(reserva.total_precio)}</td>
                    <td>${vehiculosHtml}</td>
                `;

                    tableBody.appendChild(row);
                });

                // Actualizar el panel de estadísticas con los datos del servidor
                if (data.stats) {
                    updateStats({
                        total_reservas: data.stats.total || 0,
                        reservas_completadas: data.stats.completadas || 0,
                        reservas_confirmadas: data.stats.confirmadas || 0,
                        reservas_pendientes: data.stats.pendientes || 0,
                        reservas_canceladas: data.stats.canceladas || 0,
                        ingreso_total: data.stats.ingresos || 0
                    });
                }
            } else {
                // Si no hay reservas, mostrar mensaje
                const row = document.createElement('tr');
                row.innerHTML = `
                <td colspan="7" class="text-center py-3">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No se encontraron reservas con los filtros seleccionados.
                    </div>
                </td>
            `;
                tableBody.appendChild(row);
            }
        })
        .catch(error => {
            // Manejar errores
            console.error('Error en la solicitud AJAX:', error);
            document.getElementById('loading-historial').style.display = 'block';
            document.getElementById('loading-historial').innerHTML = `<div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Error al cargar historial: ${error.message}
        </div>`;
            document.getElementById('historial-table-container').style.display = 'none';
        });
}

/**
 * showDetails(id) - Muestra los detalles completos de una reserva específica
 * 
 * @param {number} id - ID de la reserva a mostrar en detalle
 * 
 * Esta función muestra un modal con todos los detalles de una reserva específica,
 * incluyendo información del cliente, vehículos, fechas, precios y estado de pago.
 * Permite consultar información detallada sin necesidad de navegar a otra página.
 */
function showDetails(id) {
    // Mostrar indicador de carga mientras se obtienen los detalles
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Cargando detalles...',
        text: `Obteniendo información de la reserva #${id}`,
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            // Obtener el token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            console.log('Solicitando detalles de reserva ID:', id);

            // Realizar petición AJAX para obtener los detalles de la reserva
            fetch(`/admin/reservas/${id}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                credentials: 'same-origin' // Importante para mantener la sesión
            })
                .then(response => response.json())
                .then(data => {
                    // Formatear la información de vehículos para mostrar en modal
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

                    // Mostrar modal con todos los detalles organizados por secciones
                    Swal.fire({
                        title: `<span class="text-primary"><i class="fas fa-info-circle"></i> Detalles de Reserva #${id}</span>`,
                        html: `
                        <div class="container-fluid p-0">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user text-primary"></i> Información del Cliente</h5>
                                    <p><strong>Cliente:</strong> ${data.reserva.nombre_usuario || 'N/A'}</p>
                                    <p><strong>Email:</strong> ${data.reserva.email_usuario || 'N/A'}</p>
                                    <p><strong>Teléfono:</strong> ${data.reserva.telefono_usuario || 'N/A'}</p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-file-invoice-dollar text-primary"></i> Información de Pago</h5>
                                    <p><strong>Estado:</strong> <span class="badge badge-${data.reserva.estado}">${data.reserva.estado.charAt(0).toUpperCase() + data.reserva.estado.slice(1)}</span></p>
                                    <p><strong>Fecha de Reserva:</strong> ${new Date(data.reserva.fecha_reserva).toLocaleDateString('es-ES')}</p>
                                    <p><strong>Total:</strong> ${formatCurrency(data.reserva.total_precio)}</p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 text-start">
                                    <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-car text-primary"></i> Vehículos Reservados</h5>
                                    ${vehiculosHtml}
                                </div>
                            </div>
                            ${data.reserva.comentario ?
                                `<div class="row mt-3">
                                    <div class="col-12 text-start">
                                        <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-comment text-primary"></i> Comentarios</h5>
                                        <p>${data.reserva.comentario}</p>
                                    </div>
                                </div>` : ''}
                        </div>
                    `,
                        width: '800px',
                        showCloseButton: true,
                        confirmButtonText: '<i class="fas fa-check"></i> Cerrar',
                        confirmButtonColor: '#9F17BD'
                    });
                })
                .catch(error => {
                    // Manejar errores al cargar los detalles
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: '<p class="lead">No se pudieron cargar los detalles de la reserva.</p>',
                        confirmButtonColor: '#9F17BD'
                    });
                });
        }
    });
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos para el filtrado del historial y carga
 * los datos iniciales cuando la página está lista.
 */
document.addEventListener('DOMContentLoaded', function () {
    // Inicializar la carga del historial
    loadHistorial();

    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);

    // Eliminado el botón de aplicar filtros - ahora usamos filtrado en tiempo real
    // Aplicar filtros automáticamente cuando cambia cualquier valor
    const filterInputs = [
        document.getElementById('filterUsuario'),
        document.getElementById('filterLugar'),
        document.getElementById('filterEstado')
    ];

    // Agregar event listeners para todos los inputs de filtro
    filterInputs.forEach(input => {
        if (input) {
            input.addEventListener('input', function () {
                applyFilters();
            });
        }
    });

    // Configurar datepickers con opciones estándar y filtrado automático al cambiar
    const datepickers = document.querySelectorAll('.datepicker');
    if (datepickers.length > 0 && typeof flatpickr === 'function') {
        datepickers.forEach(picker => {
            flatpickr(picker, {
                dateFormat: "Y-m-d",
                locale: "es",
                maxDate: "today",
                onChange: function () {
                    applyFilters();
                }
            });
        });
    }



});
// Función para la animación de contar dinero
function animateCountUp(element, start, end, duration = 1500) {
    const range = end - start;
    let startTime = null;

    function step(timestamp) {
        if (!startTime) startTime = timestamp;
        const progress = timestamp - startTime;
        const progressRatio = Math.min(progress / duration, 1);
        const current = start + range * progressRatio;

        element.textContent = formatCurrency(current);

        if (progress < duration) {
            requestAnimationFrame(step);
        } else {
            element.textContent = formatCurrency(end); // Asegura que termina con el valor exacto
        }
    }

    requestAnimationFrame(step);
}
