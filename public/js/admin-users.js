/**
 * GESTIÓN DE USUARIOS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para gestionar usuarios
 * desde el panel de administración, incluyendo listado, filtrado, y eliminación.
 * El administrador puede buscar usuarios por nombre o filtrar por rol.
 */

// Variables globales para los filtros
let activeFilters = {};

/**
 * loadUsers() - Carga los usuarios desde la API y los muestra en la tabla
 * 
 * Esta función realiza una petición AJAX al servidor para obtener los usuarios
 * (aplicando los filtros si existen) y los muestra en la tabla del panel de administración.
 * Incluye la información básica de cada usuario y opciones para editar o eliminar.
 */
function loadUsers() {
    // Mostrar el indicador de carga
    document.getElementById('loading-users').style.display = 'block';
    document.getElementById('users-table-container').style.display = 'none';
    
    // Construir la URL con los parámetros de filtro
    let url = new URL(dataUrl, window.location.origin);
    
    // Agregar todos los filtros activos a la URL
    Object.keys(activeFilters).forEach(key => {
        if (activeFilters[key]) {
            url.searchParams.append(key, activeFilters[key]);
        }
    });
    
    // Realizar petición AJAX para obtener los usuarios
    fetch(url, {
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
        if (data.users.length === 0) {
            // Mostrar mensaje si no hay usuarios con los filtros aplicados
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="5" class="text-center">No se encontraron usuarios con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            // Recorrer cada usuario y crear su fila en la tabla
            data.users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id_usuario}</td>
                    <td>${user.nombre}</td>
                    <td>${user.email}</td>
                    <td>${user.nombre_rol || 'Sin rol asignado'}</td>
                    <td>
                        <a href="/admin/users/${user.id_usuario}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id_usuario}, '${user.nombre}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        // Manejar errores en la carga de usuarios
        console.error('Error:', error);
        document.getElementById('loading-users').innerHTML = `<div class="alert alert-danger">Error al cargar usuarios: ${error.message}</div>`;
    });
}

/**
 * applyFilters() - Aplica los filtros para la búsqueda de usuarios
 * 
 * Esta función recoge los valores de los diferentes campos de filtro
 * (nombre y rol) y actualiza la lista de usuarios mostrando solo aquellos
 * que cumplen con los criterios seleccionados.
 */
function applyFilters() {
    // Recoger los valores de los filtros
    const nombre = document.getElementById('searchUser').value.trim();
    const role = document.getElementById('filterRole').value;
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        nombre: nombre,
        role: role
    };
    
    // Cargar usuarios con los filtros aplicados
    loadUsers();
}

/**
 * clearFilters() - Limpia todos los filtros aplicados y muestra todos los usuarios
 * 
 * Esta función resetea todos los campos de filtro a sus valores predeterminados
 * y vuelve a cargar la lista completa de usuarios sin filtros aplicados.
 */
function clearFilters() {
    document.getElementById('searchUser').value = '';
    document.getElementById('filterRole').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar usuarios sin filtros
    loadUsers();
}

/**
 * deleteUser(id, nombre) - Elimina un usuario del sistema
 * 
 * @param {number} id - ID del usuario a eliminar
 * @param {string} nombre - Nombre del usuario para mostrar en la confirmación
 * 
 * Esta función muestra un diálogo de confirmación y, si el administrador confirma,
 * realiza una petición DELETE al servidor para eliminar el usuario.
 * Después de eliminar el usuario, actualiza la tabla para reflejar el cambio.
 */
function deleteUser(id, nombre) {
    // Mostrar diálogo de confirmación con detalles del usuario
    Swal.fire({
        title: `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Eliminar Usuario</span>`,
        html: `<p class="lead">¿Estás seguro de que deseas eliminar al usuario "${nombre}"?</p><p class="text-muted">Esta acción no se puede deshacer.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar indicador de carga durante el proceso
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
                text: 'Eliminando usuario',
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
            
            // Realizar petición DELETE al servidor
            fetch(`/admin/users/${id}`, {
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
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-success"><i class="fas fa-check-circle"></i> Completado</span>',
                        html: `<p class="lead">${data.message}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                    // Recargar la tabla para mostrar los cambios
                    loadUsers();
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar el usuario'}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                }
            })
            .catch(error => {
                // Manejar errores de conexión o del servidor
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

// Variable para almacenar la URL de datos
let dataUrl;

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos para el filtrado de usuarios y carga
 * la lista inicial de usuarios cuando la página está lista.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Obtener la URL de datos desde el atributo data-url
    dataUrl = document.getElementById('users-table-container').getAttribute('data-url');
    
    if (!dataUrl) {
        console.error('No se encontró la URL de datos. Asegúrate de establecer el atributo data-url en el contenedor de la tabla.');
        return;
    }
    
    // Cargar usuarios al iniciar
    loadUsers();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listener para el botón de aplicar filtros
    document.getElementById('applyFilters').addEventListener('click', applyFilters);
    
    // Event listener para buscar al presionar Enter en el campo de búsqueda
    document.getElementById('searchUser').addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            applyFilters();
        }
    });
});
