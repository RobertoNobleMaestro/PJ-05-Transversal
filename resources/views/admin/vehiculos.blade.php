@extends('layouts.admin')

@section('title', 'CRUD de Vehículos')

@section('content')
<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-color: #9F17BD; /* Cambiado a tu tono lila específico */
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
        background-color: white; /* Fondo blanco */
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
    
    .btn-delete:hover {
        background-color: rgba(197, 48, 48, 0.1);
    }
    
    /* Estilos para las estrellas de valoración */
    .star-rating {
        display: inline-flex;
        color: #ffc107; /* Color amarillo para las estrellas */
    }
    
    .star-rating .fas.fa-star {
        color: #ffc107; /* Estrella completa */
    }
    
    .star-rating .fas.fa-star-half-alt {
        color: #ffc107; /* Estrella a la mitad */
    }
    
    .star-rating .far.fa-star {
        color: #e4e5e9; /* Estrella vacía */
    }
    
    .rating-value {
        margin-left: 5px;
        font-weight: bold;
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
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Vehículos</h1>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">                
                <!-- Filtro por marca -->
                <input type="text" class="filter-control" placeholder="Marca..." id="filterMarca">
                
                <!-- Filtro por tipo de vehiculo -->
                <select class="filter-control" id="filterTipo">
                    <option value="">Todos los tipos</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por lugar -->
                <select class="filter-control" id="filterLugar">
                    <option value="">Todos los lugares</option>
                    @foreach($lugares as $lugar)
                        <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por año -->
                <select class="filter-control" id="filterAnio">
                    <option value="">Todos los años</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}">{{ $anio }}</option>
                    @endforeach
                </select>
                
                <!-- Filtro por valoracion -->
                <select class="filter-control" id="filterValoracion">
                    <option value="">Todas las valoraciones</option>
                    @foreach($valoraciones as $val)
                        <option value="{{ $val }}">{{ $val }}+ estrellas</option>
                    @endforeach
                </select>
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
            </div>
            <a href="{{ route('admin.vehiculos.create') }}" class="add-user-btn">Añadir Vehículo</a>
        </div>
        
        <div id="loading-vehiculos" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando vehículos...</p>
        </div>
        <div id="vehiculos-table-container" style="display: none;">
            <table class="crud-table" id="vehiculos-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Marca</th>
                        <th>Modelo</th>
                        <th>Año</th>
                        <th>Kilometraje</th>
                        <th>Seguro</th>
                        <th>Lugar</th>
                        <th>Tipo</th>
                        <th>Valoración</th>
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

// Función global para cargar vehículos
function loadVehiculos() {
    // Mostrar el indicador de carga
    document.getElementById('loading-vehiculos').style.display = 'block';
    document.getElementById('vehiculos-table-container').style.display = 'none';
    
    // Construir la URL con los parámetros de filtro
    let url = new URL('{{ route("admin.vehiculos.data") }}', window.location.origin);
    
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
            throw new Error('Error al cargar los vehículos');
        }
        return response.json();
    })
    .then(data => {
        // Ocultar el indicador de carga
        document.getElementById('loading-vehiculos').style.display = 'none';
        // Mostrar la tabla
        document.getElementById('vehiculos-table-container').style.display = 'block';
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#vehiculos-table tbody');
        tableBody.innerHTML = '';
        
        // Rellenar la tabla con los datos
        if (data.vehiculos.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="6" class="text-center">No se encontraron vehículos con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            data.vehiculos.forEach(vehiculo => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${vehiculo.id_vehiculos}</td>
                    <td>${vehiculo.marca}</td>
                    <td>${vehiculo.modelo}</td>
                    <td>${vehiculo.año}</td>
                    <td>${vehiculo.kilometraje}</td>
                    <td>${vehiculo.seguro_incluido ? 'Sí' : 'No'}</td>
                    <td>${vehiculo.nombre_lugar || 'No asignado'}</td>
                    <td>${vehiculo.nombre_tipo || 'No asignado'}</td>
                    <td>
                        ${renderStarRating(vehiculo.valoracion_media)}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="/admin/vehiculos/${vehiculo.id_vehiculos}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteVehiculo(${vehiculo.id_vehiculos}, '${vehiculo.marca}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-vehiculos').innerHTML = `<div class="alert alert-danger">Error al cargar vehículos: ${error.message}</div>`;
    });
}

// Aplicar los filtros cuando se hace clic en el botón o se presiona Enter
function applyFilters() {
    // Recoger los valores de los filtros
    const tipo = document.getElementById('filterTipo').value;
    const lugar = document.getElementById('filterLugar').value;
    const marca = document.getElementById('filterMarca').value.trim();
    const anio = document.getElementById('filterAnio').value;
    const valoracion = document.getElementById('filterValoracion').value;
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        tipo: tipo,
        lugar: lugar,
        marca: marca,
        anio: anio,
        valoracion: valoracion
    };
    
    // Cargar vehículos con los filtros aplicados
    loadVehiculos();
}

// Limpiar todos los filtros
function clearFilters() {
    document.getElementById('filterTipo').value = '';
    document.getElementById('filterLugar').value = '';
    document.getElementById('filterMarca').value = '';
    document.getElementById('filterAnio').value = '';
    document.getElementById('filterValoracion').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar vehículos sin filtros
    loadVehiculos();
}

// Función para renderizar las estrellas según la puntuación
function renderStarRating(rating) {
    if (rating === null) {
        return '<span class="text-muted">Sin valoraciones</span>';
    }
    
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
    
    let stars = '';
    
    // Estrellas llenas
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star"></i>';
    }
    
    // Media estrella si es necesario
    if (halfStar) {
        stars += '<i class="fas fa-star-half-alt"></i>';
    }
    
    // Estrellas vacías
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star"></i>';
    }
    
    return `<div class="star-rating">${stars} <span class="rating-value">${rating}</span></div>`;
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga de vehículos
    loadVehiculos();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('filterMarca').addEventListener('input', applyFilters);
    document.getElementById('filterTipo').addEventListener('change', applyFilters);
    document.getElementById('filterLugar').addEventListener('change', applyFilters);
    document.getElementById('filterAnio').addEventListener('change', applyFilters);
    document.getElementById('filterValoracion').addEventListener('change', applyFilters);
});

// Función para eliminar vehículo
function deleteVehiculo(id, nombreMarca) {
    Swal.fire({
        title: `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Eliminar Vehículo</span>`,
        html: `<p class="lead">¿Estás seguro de que deseas eliminar el vehículo "${nombreMarca}"?</p><p class="text-muted">Esta acción no se puede deshacer.</p>`,
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
                text: 'Eliminando vehículo',
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
            
            fetch(`/admin/vehiculos/${id}`, {
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
                    loadVehiculos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar el vehículo'}</p>`,
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
