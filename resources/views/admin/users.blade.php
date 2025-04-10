@extends('layouts.admin')

@section('title', 'CRUD de Usuarios')

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
            <li><a href="{{ route('admin.users') }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('admin.vehiculos') }}"><i class="fas fa-car"></i> Vehículos</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <h1>Gestión de Usuarios</h1>
        <div id="loading-users" class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p>Cargando usuarios...</p>
        </div>
        <div id="users-table-container" style="display: none;">
            <table class="table table-striped" id="users-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Los datos se cargarán aquí mediante AJAX -->
                </tbody>
            </table>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Añadir Usuario</a>
    </div>
</div>
@endsection

<script>
// Función global para cargar usuarios
function loadUsers() {
    fetch('{{ route("admin.users.data") }}', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error al cargar los usuarios');
        }
        return response.json();
    })
    .then(data => {
        // Ocultar el indicador de carga
        document.getElementById('loading-users').style.display = 'none';
        // Mostrar la tabla
        document.getElementById('users-table-container').style.display = 'block';
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#users-table tbody');
        tableBody.innerHTML = '';
        
        // Rellenar la tabla con los datos
        data.users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.id_usuario}</td>
                <td>${user.nombre}</td>
                <td>${user.email}</td>
                <td>${user.nombre_rol || 'Sin rol asignado'}</td>
                <td>
                    <button class="btn btn-warning btn-sm" onclick="window.location.href='/admin/users/${user.id_usuario}/edit'">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="deleteUser(${user.id_usuario})">Eliminar</button>
                </td>
            `;
            tableBody.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-users').innerHTML = `<div class="alert alert-danger">Error al cargar usuarios: ${error.message}</div>`;
    });
}

// Función para eliminar usuario
function deleteUser(userId) {
    if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
        // Crear un formulario temporal para enviar mediante POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
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
        const loadingElement = document.getElementById('loading-users');
        const tableContainer = document.getElementById('users-table-container');
        
        // Usar XMLHttpRequest para tener mejor control
        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                alert('Usuario eliminado correctamente');
                // Mostrar indicador de carga
                loadingElement.style.display = 'block';
                tableContainer.style.display = 'none';
                // Recargar los datos
                loadUsers();
            } else {
                console.error('Error:', xhr.statusText);
                alert('Error: No se pudo eliminar el usuario');
            }
        };
        
        xhr.onerror = function() {
            console.error('Request error');
            alert('Error de conexión: No se pudo eliminar el usuario');
        };
        
        xhr.send(new FormData(form));
        document.body.removeChild(form);
    }
}

// Cargar usuarios cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
});
</script>
