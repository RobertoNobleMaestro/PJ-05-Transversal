@extends('layouts.admin')

@section('title', 'CRUD de Lugares')

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
    <!-- Overlay para menu00fa mu00f3vil -->
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
            <h1 class="admin-title">Gestión de Lugares</h1>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">                
                <input type="text" class="search-input" placeholder="Buscar lugar..." id="searchLugar">
            </div>
            <a href="{{ route('admin.lugares.create') }}" class="add-user-btn">Añadir Lugar</a>
        </div>
        
        <div id="loading-lugares" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando lugares...</p>
        </div>
        <div id="lugares-table-container" style="display: none;">
            <table class="crud-table" id="lugares-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Latitud</th>
                        <th>Longitud</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargaru00e1n aquu00ed mediante AJAX -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    cargarLugares();
    
    // Buscar en la tabla
    document.getElementById('searchLugar').addEventListener('input', function(e) {
        const valorBusqueda = e.target.value.toLowerCase();
        const tabla = document.getElementById('lugares-table');
        const filas = tabla.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            const nombre = fila.querySelector('td:nth-child(2)').textContent.toLowerCase();
            const direccion = fila.querySelector('td:nth-child(3)').textContent.toLowerCase();
            
            if (nombre.includes(valorBusqueda) || direccion.includes(valorBusqueda)) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
    });
});

function cargarLugares() {
    const loadingElement = document.getElementById('loading-lugares');
    const tableContainer = document.getElementById('lugares-table-container');
    
    loadingElement.style.display = 'block';
    tableContainer.style.display = 'none';
    
    fetch('{{ route("admin.lugares.data") }}')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#lugares-table tbody');
            tableBody.innerHTML = '';
            
            data.lugares.forEach(lugar => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${lugar.id_lugar}</td>
                    <td>${lugar.nombre}</td>
                    <td>${lugar.direccion}</td>
                    <td>${lugar.latitud}</td>
                    <td>${lugar.longitud}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" onclick="window.location.href='/admin/lugares/${lugar.id_lugar}/edit'">Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteLugar(${lugar.id_lugar}, '${lugar.nombre}')">Eliminar</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
            
            loadingElement.style.display = 'none';
            tableContainer.style.display = 'block';
        })
        .catch(error => {
            console.error('Error al cargar los lugares:', error);
            loadingElement.innerHTML = `<p class="text-danger">Error al cargar los datos: ${error.message}</p>`;
        });
}

function deleteLugar(id, nombre) {
    if (confirm(`¿Estás seguro de que deseas eliminar el lugar "${nombre}"?`)) {
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
                alert('Error: No se pudo encontrar el token CSRF');
                return;
            }
        }
        
        fetch(`/admin/lugares/${id}`, {
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
                alert(data.message);
                cargarLugares();
            } else {
                alert(data.message || 'Error al eliminar el lugar');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud');
        });
    }
}
</script>
