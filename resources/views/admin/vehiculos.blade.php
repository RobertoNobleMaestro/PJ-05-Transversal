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
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Vehículos</h1>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">                
                <input type="text" class="search-input" placeholder="Buscar vehículo..." id="searchVehiculo">
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
// Función global para cargar vehículos
document.addEventListener('DOMContentLoaded', function() {
    loadVehiculos();
});
function loadVehiculos() {
    fetch('{{ route("admin.vehiculos.data") }}', {
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
                    <button class="btn btn-warning btn-sm" onclick="window.location.href='/admin/vehiculos/${vehiculo.id_vehiculos}/edit'">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteVehiculo(${vehiculo.id_vehiculos})">Eliminar</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-vehiculos').innerHTML = `<div class="alert alert-danger">Error al cargar vehículos: ${error.message}</div>`;
    });
}

// Función para eliminar vehículo
function deleteVehiculo(vehiculoId) {
    if (confirm('¿Estás seguro de que deseas eliminar este vehículo?')) {
        // Obtener el token directamente de Laravel usando {{ csrf_token() }}
        const token = '{{ csrf_token() }}';
        
        // Mostrar indicador de carga
        const loadingElement = document.getElementById('loading-vehiculos');
        const tableContainer = document.getElementById('vehiculos-table-container');
        loadingElement.style.display = 'block';
        tableContainer.style.display = 'none';
        
        // Usar Fetch API para la petición AJAX
        fetch(`/admin/vehiculos/${vehiculoId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(response.statusText);
            }
            return response.json();
        })
        .then(data => {
            // Notificar éxito
            alert('Vehículo eliminado correctamente');
            // Recargar la lista de vehículos
            loadVehiculos();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error: No se pudo eliminar el vehículo');
            loadingElement.style.display = 'none';
            tableContainer.style.display = 'block';
        });
    }
}

// Cargar vehículos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    loadVehiculos();
});
</script>
