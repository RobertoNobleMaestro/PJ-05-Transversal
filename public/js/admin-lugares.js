/**
 * GESTIÓN DE LUGARES - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar los lugares/sucursales
 * desde el panel de administración, incluyendo listado, filtrado, y eliminación.
 * Incluye una funcionalidad avanzada para reubicar vehículos cuando se elimina un lugar.
 */

// Objeto para mantener los filtros activos
let activeFilters = {};

/**
 * loadLugares() - Carga los lugares desde la API y los muestra en la tabla
 * 
 * Esta función realiza una petición AJAX al servidor para obtener los lugares
 * (aplicando los filtros si existen) y los muestra en la tabla del panel de administración.
 * Incluye la información de nombre, dirección, coordenadas y acciones disponibles.
 */
function loadLugares() {
    // Mostrar el indicador de carga
    document.getElementById('loading-lugares').style.display = 'block';
    document.getElementById('lugares-table-container').style.display = 'none';
    
    // Obtener la URL base de los datos
    const baseUrl = document.getElementById('lugares-table-container').dataset.url;
    
    // Construir la URL con los parámetros de filtro
    let url = new URL(baseUrl, window.location.origin);
    
    // Agregar todos los filtros activos a la URL
    Object.keys(activeFilters).forEach(key => {
        if (activeFilters[key]) {
            url.searchParams.append(key, activeFilters[key]);
        }
    });
    
    // Realizar petición AJAX para obtener los lugares
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
            // Mostrar mensaje si no hay lugares con los filtros aplicados
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="6" class="text-center">No se encontraron lugares con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            // Recorrer cada lugar y crear su fila en la tabla
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
        // Manejar errores
        console.error('Error:', error);
        document.getElementById('loading-lugares').innerHTML = `<div class="alert alert-danger">Error al cargar lugares: ${error.message}</div>`;
    });
}

/**
 * clearFilters() - Limpia todos los filtros aplicados y muestra todos los lugares
 * 
 * Esta función resetea todos los campos de filtro a sus valores predeterminados
 * y vuelve a cargar la lista completa de lugares sin filtros aplicados.
 */
function clearFilters() {
    document.getElementById('filterNombre').value = '';
    document.getElementById('filterDireccion').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar lugares sin filtros
    loadLugares();
}

/**
 * applyFilters() - Aplica los filtros para la búsqueda de lugares
 * 
 * Esta función recoge los valores de los diferentes campos de filtro
 * (nombre y dirección) y actualiza la lista de lugares mostrando solo aquellos
 * que cumplen con los criterios seleccionados.
 */
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

/**
 * deleteLugar(id, nombre) - Gestiona el proceso de eliminación de un lugar
 * 
 * @param {number} id - ID del lugar a eliminar
 * @param {string} nombre - Nombre del lugar para mostrar en la confirmación
 * 
 * Esta función presenta una interfaz interactiva que permite al administrador
 * elegir entre tres opciones cuando va a eliminar un lugar:
 * 1. Eliminar el lugar y todos los vehículos asociados
 * 2. Reubicar los vehículos a otro lugar existente
 * 3. Crear un nuevo lugar para transferir los vehículos
 * 
 * Incluye validación y gestión de las transacciones para asegurar la integridad de datos.
 */
function deleteLugar(id, nombre) {
    // Obtener la URL base de los datos
    const baseUrl = document.getElementById('lugares-table-container').dataset.url;
    
    // Cargar lugares disponibles para la reubicación
    fetch(baseUrl)
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
                        <p class="lead mb-4">Selecciona cómo quieres proceder con los vehículos asociados:</p>
                        
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
                                            <div class="text-muted small">Eliminar el lugar y todos sus vehículos asociados</div>
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
                                            <strong>Reubicar vehículos</strong>
                                            <div class="text-muted small">Mover los vehículos a otro lugar existente</div>
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
                                            <div class="text-muted small">Crear un nuevo lugar para los vehículos</div>
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
                                        <input type="text" id="nuevo-lugar-direccion" class="form-control" placeholder="Dirección" disabled style="border-color: #28a745;">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-map-pin text-success"></i></span>
                                            <input type="text" id="nuevo-lugar-latitud" class="form-control" placeholder="Latitud" disabled style="border-color: #28a745;">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <div class="input-group">
                                            <span class="input-group-text" style="background-color: #f8f9fa;"><i class="fas fa-map-pin text-success"></i></span>
                                            <input type="text" id="nuevo-lugar-longitud" class="form-control" placeholder="Longitud" disabled style="border-color: #28a745;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-muted mt-3">
                            <small><i class="fas fa-info-circle"></i> Al eliminar un lugar, debes decidir qué hacer con los vehículos asociados.</small>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-check"></i> Confirmar',
                cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
                confirmButtonColor: '#9F17BD',
                cancelButtonColor: '#6c757d',
                width: '600px',
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-secondary'
                },
                didOpen: () => {
                    // Configurar cambio de estados visuales al seleccionar opciones
                    const opcionEliminar = document.getElementById('opcion-eliminar');
                    const opcionReubicar = document.getElementById('opcion-reubicar');
                    const opcionNuevo = document.getElementById('opcion-nuevo');
                    
                    const selectDestino = document.getElementById('lugar-destino');
                    const nombreInput = document.getElementById('nuevo-lugar-nombre');
                    const direccionInput = document.getElementById('nuevo-lugar-direccion');
                    const latitudInput = document.getElementById('nuevo-lugar-latitud');
                    const longitudInput = document.getElementById('nuevo-lugar-longitud');
                    
                    const updateOpcionEliminar = () => {
                        document.querySelector(`label[for="opcion-eliminar"]`).closest('.form-check').style.backgroundColor = opcionEliminar.checked ? '#feecec' : '#f8f9fa';
                    };
                    
                    const updateOpcionReubicar = () => {
                        const container = document.querySelector(`label[for="opcion-reubicar"]`).closest('.form-check');
                        container.style.backgroundColor = opcionReubicar.checked ? '#e8f1ff' : '#f8f9fa';
                        selectDestino.disabled = !opcionReubicar.checked;
                        selectDestino.style.opacity = opcionReubicar.checked ? '1' : '0.7';
                    };
                    
                    const updateOpcionNuevo = () => {
                        const container = document.querySelector(`label[for="opcion-nuevo"]`).closest('.form-check');
                        container.style.backgroundColor = opcionNuevo.checked ? '#e8f8ee' : '#f8f9fa';
                        
                        const formContainer = document.querySelector('.nuevo-lugar-form');
                        formContainer.style.opacity = opcionNuevo.checked ? '1' : '0.7';
                        
                        nombreInput.disabled = !opcionNuevo.checked;
                        direccionInput.disabled = !opcionNuevo.checked;
                        latitudInput.disabled = !opcionNuevo.checked;
                        longitudInput.disabled = !opcionNuevo.checked;
                    };
                    
                    // Event listeners para actualizar estados
                    opcionEliminar.addEventListener('change', () => {
                        updateOpcionEliminar();
                    });
                    
                    opcionReubicar.addEventListener('change', () => {
                        updateOpcionReubicar();
                    });
                    
                    opcionNuevo.addEventListener('change', () => {
                        updateOpcionNuevo();
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Obtener la opción seleccionada
                    let accion = document.querySelector('input[name="accion"]:checked').value;
                    
                    let data = {
                        accion: accion,
                        id_lugar: id
                    };
                    
                    if (accion === 'reubicar') {
                        // Validar que se haya seleccionado un lugar de destino
                        const lugarDestino = document.getElementById('lugar-destino').value;
                        if (!lugarDestino) {
                            Swal.fire({
                                icon: 'error',
                                title: '<span class="text-danger">Error</span>',
                                html: '<p>Debes seleccionar un lugar de destino para los vehículos.</p>',
                                confirmButtonColor: '#9F17BD'
                            });
                            return;
                        }
                        
                        // Añadir el ID del lugar de destino
                        data.lugar_destino = lugarDestino;
                    } else if (accion === 'nuevo') {
                        // Obtener y validar datos del nuevo lugar
                        const nuevoNombre = document.getElementById('nuevo-lugar-nombre').value.trim();
                        const nuevaDireccion = document.getElementById('nuevo-lugar-direccion').value.trim();
                        const nuevaLatitud = document.getElementById('nuevo-lugar-latitud').value.trim();
                        const nuevaLongitud = document.getElementById('nuevo-lugar-longitud').value.trim();
                        
                        if (!nuevoNombre || !nuevaDireccion) {
                            Swal.fire({
                                icon: 'error',
                                title: '<span class="text-danger">Error</span>',
                                html: '<p>El nombre y dirección del nuevo lugar son obligatorios.</p>',
                                confirmButtonColor: '#9F17BD'
                            });
                            return;
                        }
                        
                        // Añadir datos del nuevo lugar
                        data.nuevo_lugar = {
                            nombre: nuevoNombre,
                            direccion: nuevaDireccion,
                            latitud: nuevaLatitud || null,
                            longitud: nuevaLongitud || null
                        };
                    }
                    
                    // Mostrar cargando
                    Swal.fire({
                        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
                        text: 'Ejecutando la acción seleccionada',
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
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
                                title: '<span class="text-danger">Error</span>',
                                html: '<p>No se pudo encontrar el token CSRF.</p>',
                                confirmButtonColor: '#9F17BD'
                            });
                            return;
                        }
                    }
                    
                    // Realizar la petición DELETE con los datos adicionales
                    fetch(`/admin/lugares/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(data)
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
                            // Recargar la tabla
                            loadLugares();
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                                html: `<p class="lead">${data.message || 'Error al procesar la solicitud'}</p>`,
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
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: '<p class="lead">Error al cargar los lugares disponibles</p>',
                confirmButtonColor: '#9F17BD'
            });
        });
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos para el filtrado de lugares y carga
 * la lista inicial de lugares cuando la página está lista.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar la carga de lugares
    loadLugares();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listener para el botón de aplicar filtros
    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    
    // Event listeners para aplicar filtros cuando se presiona Enter
    document.getElementById('filterNombre').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            applyFilters();
        }
    });
    
    document.getElementById('filterDireccion').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            applyFilters();
        }
    });
});
