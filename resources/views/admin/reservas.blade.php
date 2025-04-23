@extends('layouts.admin')

@section('title', 'CRUD de Reservas')

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
    
    .add-user-btn {
        background-color: black;
        color: white;
        border: none;
        padding: 0.5rem 1.5rem;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .add-user-btn:hover {
        background-color: #333;
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
    
    .action-buttons {
        display: flex;
        gap: 0.5rem;
    }
    
    .btn-edit {
        color: #2b6cb0;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        text-decoration: none;
        transition: background 0.2s;
    }
    
    .btn-delete {
        color: #c53030;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        text-decoration: none;
        transition: background 0.2s;
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
            <h1 class="admin-title">Gestión de Reservas</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
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
                
                <!-- Filtro por fecha -->
                <input type="date" class="filter-control" id="filterFecha">
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
            <a href="{{ route('admin.reservas.create') }}" class="add-user-btn">Añadir Reserva</a>
        </div>
        
        <div id="loading-reservas" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando reservas...</p>
        </div>
        <div id="reservas-table-container" style="display: none;">
            <table class="crud-table" id="reservas-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th>Lugar</th>
                        <th>Estado</th>
                        <th>Total</th>
                        <th>Vehículos</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
// Variables globales para los filtros
let activeFilters = {};

// Función para aplicar los filtros
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

// Función para limpiar los filtros
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

// Función global para cargar reservas
function loadReservas() {
    // Mostrar el indicador de carga
    document.getElementById('loading-reservas').style.display = 'block';
    document.getElementById('reservas-table-container').style.display = 'none';
    
    // Construir la URL con los parámetros de filtro
    let url = new URL('{{ route("admin.reservas.data") }}', window.location.origin);
    
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

// Inicializar cuando el DOM está listo
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

// Función para eliminar reserva
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
</script>
