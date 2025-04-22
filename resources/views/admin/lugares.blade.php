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
            <li><a href="{{ route('admin.reservas') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
            <li><a href="{{ route('admin.historial') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}"><i class="fas fa-history"></i> Historial</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Gestión de Lugares</h1>
            <a href="{{ route('admin.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver al Panel
            </a>
        </div>
        
        <div class="filter-section">
            <div class="filter-group">
                <!-- Filtro por nombre -->
                <input type="text" class="filter-control" placeholder="Buscar por nombre..." id="filterNombre">
                
                <!-- Filtro por dirección -->
                <input type="text" class="filter-control" placeholder="Buscar por dirección..." id="filterDireccion">
                
                <button id="clearFilters" class="btn btn-outline-secondary">Limpiar</button>
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
// Variables globales para los filtros
let activeFilters = {};

// Función para aplicar los filtros
function applyFilters() {
    // Recoger los valores de los filtros
    const nombre = document.getElementById('filterNombre').value.trim();
    const direccion = document.getElementById('filterDireccion').value.trim();
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        nombre: nombre,
        direccion: direccion
    };
    
    // Cargar lugares con los filtros aplicados
    loadLugares();
}

// Función para limpiar los filtros
function clearFilters() {
    document.getElementById('filterNombre').value = '';
    document.getElementById('filterDireccion').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar lugares sin filtros
    loadLugares();
}

// Función global para cargar lugares
function loadLugares() {
    // Mostrar el indicador de carga
    document.getElementById('loading-lugares').style.display = 'block';
    document.getElementById('lugares-table-container').style.display = 'none';
    
    // Construir la URL con los parámetros de filtro
    let url = new URL('{{ route("admin.lugares.data") }}', window.location.origin);
    
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
            throw new Error('Error al cargar los lugares');
        }
        return response.json();
    })
    .then(data => {
        // Ocultar el indicador de carga
        document.getElementById('loading-lugares').style.display = 'none';
        // Mostrar la tabla
        document.getElementById('lugares-table-container').style.display = 'block';
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#lugares-table tbody');
        tableBody.innerHTML = '';
        
        // Rellenar la tabla con los datos
        if (data.lugares.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="6" class="text-center">No se encontraron lugares con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            data.lugares.forEach(lugar => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${lugar.id_lugar}</td>
                    <td>${lugar.nombre}</td>
                    <td>${lugar.direccion}</td>
                    <td>${lugar.latitud}</td>
                    <td>${lugar.longitud}</td>
                    <td>
                        <a href="/admin/lugares/${lugar.id_lugar}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteLugar(${lugar.id_lugar}, '${lugar.nombre}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-lugares').innerHTML = `<div class="alert alert-danger">Error al cargar lugares: ${error.message}</div>`;
    });
}

// Limpiar todos los filtros
function clearFilters() {
    document.getElementById('filterNombre').value = '';
    document.getElementById('filterDireccion').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar lugares sin filtros
    loadLugares();
}

// Función para aplicar los filtros
function applyFilters() {
    // Recoger los valores de los filtros
    const nombre = document.getElementById('filterNombre').value.trim();
    const direccion = document.getElementById('filterDireccion').value.trim();
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        nombre: nombre,
        direccion: direccion
    };
    
    // Cargar lugares con los filtros aplicados
    loadLugares();
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga de lugares
    loadLugares();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('filterNombre').addEventListener('input', applyFilters);
    document.getElementById('filterDireccion').addEventListener('input', applyFilters);
});

// Esta función ha sido reemplazada por loadLugares

function deleteLugar(id, nombre) {
    // Cargar lugares disponibles para la reubicaciu00f3n
    fetch('{{ route("admin.lugares.data") }}')
        .then(response => response.json())
        .then(data => {
            // Filtrar para excluir el lugar actual
            const lugaresDisponibles = data.lugares.filter(lugar => lugar.id_lugar != id);
            
            // Generar opciones para el select
            let opcionesHTML = '';
            lugaresDisponibles.forEach(lugar => {
                opcionesHTML += `<option value="${lugar.id_lugar}">${lugar.nombre} - ${lugar.direccion}</option>`;
            });
            
            // Mostrar Sweet Alert con opciones
            Swal.fire({
                title: `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Eliminar ${nombre}</span>`,
                html: `
                    <div class="custom-modal-content">
                        <p class="lead mb-4">Selecciona cu00f3mo quieres proceder con los vehu00edculos asociados:</p>
                        
                        <div class="option-card mb-3">
                            <div class="form-check d-flex align-items-center p-3 border rounded" style="background-color: #feecec; transition: all 0.3s ease;">
                                <input type="radio" id="opcion-eliminar" name="accion" value="eliminar" class="form-check-input mt-0" checked>
                                <label for="opcion-eliminar" class="form-check-label ms-2 w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="option-icon rounded-circle p-2 me-3" style="background-color: #dc3545; color: white;">
                                            <i class="fas fa-trash-alt"></i>
                                        </div>
                                        <div>
                                            <strong>Eliminar todo</strong>
                                            <div class="text-muted small">Eliminar el lugar y todos sus vehu00edculos asociados</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        
                        <div class="option-card mb-3">
                            <div class="form-check d-flex align-items-center p-3 border rounded" style="background-color: #f8f9fa; transition: all 0.3s ease;">
                                <input type="radio" id="opcion-reubicar" name="accion" value="reubicar" class="form-check-input mt-0">
                                <label for="opcion-reubicar" class="form-check-label ms-2 w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="option-icon rounded-circle p-2 me-3" style="background-color: #007bff; color: white;">
                                            <i class="fas fa-exchange-alt"></i>
                                        </div>
                                        <div>
                                            <strong>Reubicar vehu00edculos</strong>
                                            <div class="text-muted small">Mover los vehu00edculos a otro lugar existente</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="mt-2 ps-4">
                                <select id="lugar-destino" class="form-select" ${lugaresDisponibles.length === 0 ? 'disabled' : ''} style="border-color: #007bff; opacity: 0.7;">
                                    ${lugaresDisponibles.length > 0 ? opcionesHTML : '<option>No hay lugares disponibles</option>'}
                                </select>
                            </div>
                        </div>
                        
                        <div class="option-card">
                            <div class="form-check d-flex align-items-center p-3 border rounded" style="background-color: #f8f9fa; transition: all 0.3s ease;">
                                <input type="radio" id="opcion-nuevo" name="accion" value="nuevo" class="form-check-input mt-0">
                                <label for="opcion-nuevo" class="form-check-label ms-2 w-100">
                                    <div class="d-flex align-items-center">
                                        <div class="option-icon rounded-circle p-2 me-3" style="background-color: #28a745; color: white;">
                                            <i class="fas fa-plus-circle"></i>
                                        </div>
                                        <div>
                                            <strong>Crear nuevo lugar</strong>
                                            <div class="text-muted small">Crear un nuevo lugar para los vehu00edculos</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="nuevo-lugar-form mt-2 ps-4" style="opacity: 0.7;">
                                <div class="mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-map-marker-alt text-success"></i></span>
                                        <input type="text" id="nuevo-lugar-nombre" class="form-control" placeholder="Nombre del lugar" disabled style="border-color: #28a745;">
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-road text-success"></i></span>
                                        <input type="text" id="nuevo-lugar-direccion" class="form-control" placeholder="Direcciu00f3n" disabled style="border-color: #28a745;">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-map-pin text-success"></i></span>
                                            <input type="number" id="nuevo-lugar-latitud" class="form-control" placeholder="Latitud" disabled style="border-color: #28a745;">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-map-pin text-success"></i></span>
                                            <input type="number" id="nuevo-lugar-longitud" class="form-control" placeholder="Longitud" disabled style="border-color: #28a745;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Continuar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#9F17BD',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-lg',
                    cancelButton: 'btn btn-lg'
                },
                width: '600px',
                focusConfirm: false,
                didOpen: () => {
                    // Agregar estilo CSS adicional
                    const style = document.createElement('style');
                    style.textContent = `
                        .option-card:hover .form-check {
                            background-color: #f0f0f0 !important;
                            box-shadow: 0 0 8px rgba(0,0,0,0.1);
                        }
                        .form-check-input:checked + label .form-check {
                            border-color: #9F17BD !important;
                        }
                        #opcion-eliminar:checked ~ .option-card:first-child .form-check {
                            background-color: #feecec !important;
                            border-color: #dc3545 !important;
                        }
                        #opcion-reubicar:checked ~ .option-card:nth-child(2) .form-check {
                            background-color: #eaf5ff !important;
                            border-color: #007bff !important;
                        }
                        #opcion-nuevo:checked ~ .option-card:last-child .form-check {
                            background-color: #eaffef !important;
                            border-color: #28a745 !important;
                        }
                    `;
                    document.head.appendChild(style);
                    
                    // Obtener referencias a todos los elementos
                    const opcionEliminar = document.getElementById('opcion-eliminar');
                    const opcionReubicar = document.getElementById('opcion-reubicar');
                    const opcionNuevo = document.getElementById('opcion-nuevo');
                    const selectDestino = document.getElementById('lugar-destino');
                    const nuevoNombre = document.getElementById('nuevo-lugar-nombre');
                    const nuevaDireccion = document.getElementById('nuevo-lugar-direccion');
                    const nuevaLatitud = document.getElementById('nuevo-lugar-latitud');
                    const nuevaLongitud = document.getElementById('nuevo-lugar-longitud');
                    const nuevoLugarForm = document.querySelector('.nuevo-lugar-form');
                    
                    // Función para actualizar los estados de los campos
                    function updateFieldState() {
                        console.log('Actualizando campos, opción seleccionada:', document.querySelector('input[name="accion"]:checked').value);
                        
                        if (opcionEliminar.checked) {
                            // Opción eliminar seleccionada
                            selectDestino.disabled = true;
                            nuevoNombre.disabled = true;
                            nuevaDireccion.disabled = true;
                            nuevaLatitud.disabled = true;
                            nuevaLongitud.disabled = true;
                            nuevoLugarForm.style.opacity = '0.7';
                            selectDestino.parentElement.style.opacity = '0.7';
                            
                        } else if (opcionReubicar.checked) {
                            // Opción reubicar seleccionada
                            selectDestino.disabled = false;
                            nuevoNombre.disabled = true;
                            nuevaDireccion.disabled = true;
                            nuevaLatitud.disabled = true;
                            nuevaLongitud.disabled = true;
                            nuevoLugarForm.style.opacity = '0.7';
                            selectDestino.parentElement.style.opacity = '1';
                            
                        } else if (opcionNuevo.checked) {
                            // Opción nuevo lugar seleccionada
                            selectDestino.disabled = true;
                            nuevoNombre.disabled = false;
                            nuevaDireccion.disabled = false;
                            nuevaLatitud.disabled = false;
                            nuevaLongitud.disabled = false;
                            nuevoLugarForm.style.opacity = '1';
                            selectDestino.parentElement.style.opacity = '0.7';
                        }
                    }
                    
                    // Asignar eventos a los radio buttons
                    opcionEliminar.addEventListener('change', updateFieldState);
                    opcionReubicar.addEventListener('change', updateFieldState);
                    opcionNuevo.addEventListener('change', updateFieldState);
                    
                    // Agregar eventos de clic a las tarjetas
                    document.querySelectorAll('.option-card').forEach(card => {
                        card.addEventListener('click', function(e) {
                            // No activar si se hace clic en input o select
                            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || 
                                e.target.tagName === 'OPTION' || e.target.classList.contains('form-control')) {
                                return;
                            }
                            
                            const radio = this.querySelector('input[type="radio"]');
                            if (radio) {
                                radio.checked = true;
                                updateFieldState();
                            }
                        });
                    });
                    
                    // Ejecutar una vez al inicio para configurar el estado inicial
                    updateFieldState();
                    
                    // Asegurarse de que la opción seleccionada tenga los estilos correctos
                    setTimeout(() => {
                        if (opcionEliminar.checked) {
                            document.querySelector('.option-card:first-child .form-check').style.backgroundColor = '#feecec';
                            document.querySelector('.option-card:first-child .form-check').style.borderColor = '#dc3545';
                        } else if (opcionReubicar.checked) {
                            document.querySelector('.option-card:nth-child(2) .form-check').style.backgroundColor = '#eaf5ff';
                            document.querySelector('.option-card:nth-child(2) .form-check').style.borderColor = '#007bff';
                        } else if (opcionNuevo.checked) {
                            document.querySelector('.option-card:last-child .form-check').style.backgroundColor = '#eaffef';
                            document.querySelector('.option-card:last-child .form-check').style.borderColor = '#28a745';
                        }
                    }, 100);
                }

            }).then((result) => {
                if (result.isConfirmed) {
                    // Obtener la opciu00f3n seleccionada
                    const accion = document.querySelector('input[name="accion"]:checked').value;
                    let payload = { accion };
                    
                    if (accion === 'reubicar') {
                        const lugarDestinoId = document.getElementById('lugar-destino').value;
                        payload.lugar_destino_id = lugarDestinoId;
                        
                    } else if (accion === 'nuevo') {
                        payload.nuevo_lugar = {
                            nombre: document.getElementById('nuevo-lugar-nombre').value,
                            direccion: document.getElementById('nuevo-lugar-direccion').value,
                            latitud: document.getElementById('nuevo-lugar-latitud').value,
                            longitud: document.getElementById('nuevo-lugar-longitud').value
                        };
                        
                        // Validar campos del nuevo lugar
                        if (!payload.nuevo_lugar.nombre || !payload.nuevo_lugar.direccion || 
                            !payload.nuevo_lugar.latitud || !payload.nuevo_lugar.longitud) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Campos incompletos',
                                text: 'Todos los campos del nuevo lugar son requeridos',
                                confirmButtonColor: '#9F17BD'
                            });
                            return;
                        }
                    }
                    
                    // Mostrar cargando
                    Swal.fire({
                        title: '<i class="fas fa-spin fa-spinner"></i> Procesando...',
                        text: 'Realizando operaciu00f3n',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        background: '#f8f9fa',
                        customClass: {
                            title: 'text-primary',
                            content: 'text-secondary'
                        }
                    });
                    
                    // Obtener el token CSRF
                    let csrfToken = '';
                    const metaToken = document.querySelector('meta[name="csrf-token"]');
                    
                    if (metaToken) {
                        csrfToken = metaToken.getAttribute('content');
                    } else {
                        const hiddenInput = document.querySelector('input[name="_token"]');
                        if (hiddenInput) {
                            csrfToken = hiddenInput.value;
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo encontrar el token CSRF',
                                confirmButtonColor: '#9F17BD'
                            });
                            return;
                        }
                    }
                    
                    // Enviar peticiu00f3n al servidor
                    fetch(`/admin/lugares/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
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
                            cargarLugares();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                                html: `<p class="lead">${data.message || 'Error al procesar la operaciu00f3n'}</p>`,
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
        })
        .catch(error => {
            console.error('Error al cargar lugares:', error);
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: '<p class="lead">No se pudieron cargar los lugares disponibles</p>',
                confirmButtonColor: '#9F17BD'
            });
        });
}

</script>
