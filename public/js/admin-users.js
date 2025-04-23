// Variables globales para los filtros
let activeFilters = {};

// Función global para cargar usuarios
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
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="5" class="text-center">No se encontraron usuarios con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
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
        console.error('Error:', error);
        document.getElementById('loading-users').innerHTML = `<div class="alert alert-danger">Error al cargar usuarios: ${error.message}</div>`;
    });
}

// Aplicar los filtros cuando se hace clic en el botón o se presiona Enter
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

// Limpiar todos los filtros
function clearFilters() {
    document.getElementById('searchUser').value = '';
    document.getElementById('filterRole').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar usuarios sin filtros
    loadUsers();
}

// Función para eliminar usuario
function deleteUser(id, nombre) {
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
            // Mostrar cargando
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
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-success"><i class="fas fa-check-circle"></i> Completado</span>',
                        html: `<p class="lead">${data.message}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                    loadUsers();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar el usuario'}</p>`,
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

// Variable para almacenar la URL de datos
let dataUrl;

// Inicializar cuando el DOM esté listo
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
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('searchUser').addEventListener('input', applyFilters);
    document.getElementById('filterRole').addEventListener('change', applyFilters);
});
