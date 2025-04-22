@extends('layouts.admin')

@section('title', 'Historial de Reservas')

@section('content')
<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-color: #9F17BD;
        --header-height: 60px;
    }
    
    .admin-container {
        display: flex;
        min-height: 100vh;
        background-color: #f8f9fa;
    }
    
    /* Barra lateral lila */
    .admin-sidebar {
        width: var(--sidebar-width);
        background-color: var(--sidebar-color);
        color: white;
        padding: 1.5rem 1rem;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar-title {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid rgba(255,255,255,0.2);
    }
    
    .sidebar-menu {
        list-style: none;
        padding: 0;
    }
    
    .sidebar-menu li {
        margin-bottom: 1rem;
    }
    
    .sidebar-menu a {
        color: white;
        text-decoration: none;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        padding: 0.5rem;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .sidebar-menu a:hover {
        background-color: rgba(255,255,255,0.2);
    }
    
    .sidebar-menu i {
        margin-right: 10px;
        font-size: 1.2rem;
    }
    
    .sidebar-menu .active {
        background-color: rgba(255,255,255,0.3);
        font-weight: bold;
        border-radius: 5px;
    }
    
    /* Contenido principal */
    .admin-main {
        flex: 1;
        padding: 0.5rem;
        margin-left: 0;
    }
    
    /* Header modificado */
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding: 0.5rem 1rem;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .admin-title {
        font-size: 1.5rem;
        color: #2d3748;
        font-weight: 600;
    }
    
    .admin-welcome {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #4a5568;
        font-weight: 500;
    }
    
    /* Filtros */
    .filter-section {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1rem;
        background-color: white;
        padding: 0.75rem;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .filter-group {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-control {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
    }
    
    .search-input {
        padding: 0.5rem 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        min-width: 250px;
    }
    
    /* Tarjetas de estadísticas */
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background-color: white;
        border-radius: 8px;
        padding: 1.2rem;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .stat-card .icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
        color: var(--sidebar-color);
    }
    
    .stat-card .value {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.2rem;
        color: #333;
    }
    
    .stat-card .label {
        font-size: 0.9rem;
        color: #666;
    }
    
    /* Tabla */
    .crud-table {
        width: 100%;
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        border-collapse: collapse;
    }
    
    .crud-table thead {
        background-color: #4a5568;
        color: white;
    }
    
    .crud-table th,
    .crud-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .crud-table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.875rem;
    }
    
    .crud-table tbody tr:nth-child(even) {
        background-color: #f7fafc;
    }
    
    .crud-table tbody tr:hover {
        background-color: #ebf4ff;
    }
    
    /* Estilos de píldoras para estados */
    .badge {
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        white-space: nowrap;
        border-radius: 0.25rem;
    }
    
    .badge-pendiente {
        background-color: #f59e0b;
        color: white;
    }
    
    .badge-confirmada {
        background-color: #10b981;
        color: white;
    }
    
    .badge-cancelada {
        background-color: #ef4444;
        color: white;
    }
    
    .badge-completada {
        background-color: #3b82f6;
        color: white;
    }
    
    /* Estilos para la lista de vehículos */
    .vehiculos-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .vehiculos-list li {
        padding: 0.5rem 0;
        border-bottom: 1px dashed #e2e8f0;
    }
    
    .vehiculos-list li:last-child {
        border-bottom: none;
    }
</style>

<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('admin.vehiculos') }}" class="{{ request()->routeIs('admin.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i> Vehículos</a></li>
            <li><a href="{{ route('admin.lugares') }}" class="{{ request()->routeIs('admin.lugares*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Lugares</a></li>
            <li><a href="{{ route('admin.reservas') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
            <li><a href="{{ route('admin.historial') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Historial de Reservas</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
        
        <!-- Sección de estadísticas -->
        <div class="stats-container mb-4" id="stats-container">
            <!-- Las estadísticas se cargarán aquí mediante AJAX -->
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
                <div class="value" id="total-reservas">-</div>
                <div class="label">Total Reservas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-check-circle"></i></div>
                <div class="value" id="reservas-completadas">-</div>
                <div class="label">Completadas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-clock"></i></div>
                <div class="value" id="reservas-pendientes">-</div>
                <div class="label">Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-times-circle"></i></div>
                <div class="value" id="reservas-canceladas">-</div>
                <div class="label">Canceladas</div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-euro-sign"></i></div>
                <div class="value" id="ingreso-total">-</div>
                <div class="label">Ingresos Totales</div>
            </div>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <!-- Filtro por usuario -->
                <input type="text" class="filter-control" placeholder="Usuario..." id="filterUsuario">
                
                <!-- Filtro por lugar -->
                <select class="filter-control" id="filterLugar">
                    <option value="">Todos los lugares</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por estado -->
                <select class="filter-control" id="filterEstado">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado }}">{{ ucfirst($estado) }}</option>
                    @endforeach
                </select>
                
                <!-- Filtros de fecha -->
                <div class="d-flex gap-2 align-items-center">
                    <span>Desde:</span>
                    <input type="date" class="filter-control" id="filterFechaDesde">
                    <span>Hasta:</span>
                    <input type="date" class="filter-control" id="filterFechaHasta">
                </div>
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
        </div>
        
        <div id="loading-historial" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando historial de reservas...</p>
        </div>
        <div id="historial-table-container" style="display: none;">
            <table class="crud-table" id="historial-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Vehículos</th>
                        <th>Detalles</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Variables globales para los filtros
let activeFilters = {};

// Función para aplicar los filtros automáticamente
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

// Función para limpiar los filtros
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

// Función para actualizar las estadísticas
function updateStats(stats) {
    document.getElementById('total-reservas').textContent = stats.total_reservas;
    document.getElementById('reservas-completadas').textContent = stats.reservas_completadas;
    document.getElementById('reservas-pendientes').textContent = stats.reservas_pendientes;
    document.getElementById('reservas-canceladas').textContent = stats.reservas_canceladas;
    document.getElementById('ingreso-total').textContent = formatCurrency(stats.ingreso_total);
}

// Función para formatear precio a formato de moneda
function formatCurrency(value) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

// Función global para cargar el historial de reservas
function loadHistorial() {
    // Mostrar el indicador de carga
    document.getElementById('loading-historial').style.display = 'block';
    document.getElementById('historial-table-container').style.display = 'none';
    
    // Construir la URL con los parámetros de filtro
    let url = new URL('{{ route("admin.historial.data") }}', window.location.origin);
    
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

// Función para mostrar detalles de una reserva específica
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

// Inicializar cuando el DOM está listo
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
</script>
@endsection
