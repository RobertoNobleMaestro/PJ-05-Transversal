@extends('layouts.admin')

@section('title', 'CRUD de Vehículos')

@section('content')
<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-color: #9F17BD; /* Tono lila específico */
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
        padding: 0.75rem;
        border-radius: 6px;
        transition: background-color 0.3s;
    }
    
    .sidebar-menu a:hover {
        background-color: rgba(255,255,255,0.1);
    }
    
    .sidebar-menu i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    /* Contenido principal */
    .admin-main {
        flex-grow: 1;
        padding: 2rem;
        overflow-y: auto;
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
            <h1 class="admin-title">Gestión de Vehículos</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
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
            <table class="table table-striped" id="vehiculos-table">
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
        <a href="{{ route('admin.vehiculos.create') }}" class="btn btn-primary">Añadir Vehículo</a>
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
                <td>${vehiculo.anio}</td>
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
        // Crear un formulario temporal para enviar mediante POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/vehiculos/${vehiculoId}`;
        form.style.display = 'none';
        
        // Agregar token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfInput);
        
        // Agregar método DELETE (method spoofing)
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        // Agregar formulario al DOM y enviarlo
        document.body.appendChild(form);
        
        // Antes de enviar, guardar referencia a los elementos que necesitaremos después
        const loadingElement = document.getElementById('loading-vehiculos');
        const tableContainer = document.getElementById('vehiculos-table-container');
        
        // Usar XMLHttpRequest para tener mejor control
        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                alert('Vehículo eliminado correctamente');
                // Mostrar indicador de carga
                loadingElement.style.display = 'block';
                tableContainer.style.display = 'none';
                // Recargar los datos
                loadVehiculos();
            } else {
                console.error('Error:', xhr.statusText);
                alert('Error: No se pudo eliminar el vehículo');
            }
        };
        
        xhr.onerror = function() {
            console.error('Request error');
            alert('Error de conexión: No se pudo eliminar el vehículo');
        };
        
        xhr.send(new FormData(form));
        document.body.removeChild(form);
    }
}

// Cargar vehículos cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    loadVehiculos();
});
</script>
