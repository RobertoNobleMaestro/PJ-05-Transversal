// Variables globales para los filtros
let activeFilters = {};

// Variables globales para la paginación
let currentPage = 1;
let perPage = 10;

// Función de carga de usuarios mediante AJAX para después mostrarlos 
function loadUsers() {
    document.getElementById('loading-users').style.display = 'block';
    document.getElementById('users-table-container').style.display = 'none';

    // Definición de la URL
    let url = new URL(dataUrl, window.location.origin);

    // Bloque de control para añadir valores a los filtros
    if (activeFilters.nombre) url.searchParams.append('nombre', activeFilters.nombre);
    if (activeFilters.role) url.searchParams.append('role', activeFilters.role);
    if (activeFilters.parking_id) url.searchParams.append('parking_id', activeFilters.parking_id);
    if (activeFilters.email) url.searchParams.append('email', activeFilters.email);

    // Si las condiciones se cumplen se añaden a la paginación
    url.searchParams.append('page', currentPage);
    url.searchParams.append('perPage', perPage);
    console.log(url);
    // Inicio de la petición
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    // Si no hay una respuesta da error
    .then(response => {
        if (!response.ok) throw new Error('Error al cargar los usuarios');
        return response.json();
    })
    // Si hay respuesta comienza el proceso para mostrar los usuarios
    .then(data => {
        document.getElementById('loading-users').style.display = 'none';
        document.getElementById('users-table-container').style.display = 'block';

        // Definición de la tabla (para rellenar mediante los datos de la consulta hecha en el controlador)
        const tableBody = document.querySelector('#users-table tbody');
        tableBody.innerHTML = '';

        // Primera letra en mayusculas
        const capitalizeFirstLetter = text => {
            if (!text) return 'Sin rol asignado';
            // Reemplaza _ por espacio y capitaliza la primera letra de cada palabra
            return text
                .replace(/_/g, ' ')
                .split(' ')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(' ');
        };

        // Bloque de control para comprobar si hay usuarios
        // En caso de que salgan vacíos, mostrar mensaje de error conforme no se han encontrado
        if (data.users.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="5" class="text-center">No se encontraron usuarios</td>`;
            tableBody.appendChild(row);
        } else {
            // Si hay resultados se muestra la tabla con todos los campos que se quieran mostrar
            // Se puede añadir cualquier campo ya que en la consulta se hace un * para una escalabilidad a la hora de añadir campos aquí
            data.users.forEach(user => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${user.id_usuario}</td>
                    <td>${user.nombre}</td>
                    <td>${user.email}</td>
                    <td>${user.dni}</td>
                    <td>${capitalizeFirstLetter(user.nombre_rol)}</td>
                    <td>${user.parking_nombre || user.parking_id}</td>
                    <td>
                        <a href="/gestor/users/${user.id_usuario}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteUser(${user.id_usuario}, '${user.nombre}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }

        // Renderizar paginación mediante AJAX(fetch)
        renderPagination(data.pagination);
    })
    // Captura del error de la petición
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('loading-users').innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
    });
}

const perPageSelect = document.getElementById('perPageSelect');
if (perPageSelect) {
    perPageSelect.addEventListener('change', function () {
        perPage = parseInt(this.value);
        currentPage = 1;
        loadUsers();
    });
}

// Función para aplicar los filtros sumativamente tanto por rol como por nombre 
function applyFilters() {
    // Recoger los valores de los filtros
    const nombre = document.getElementById('searchUser').value.trim();
    const role = document.getElementById('filterRole').value;
    const parking = document.getElementById('filterParking').value;
    const email = document.getElementById('searchEmail').value.trim();
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        nombre: nombre,
        role: role,
        parking_id: parking,
        email: email
    };
    
    // Cargar usuarios con los filtros aplicados
    loadUsers();
}

// Función para limpiar filtros y volver a mostrar los usuarios
function clearFilters() {
    document.getElementById('searchUser').value = '';
    document.getElementById('filterRole').value = '';
    document.getElementById('filterParking').value = '';
    document.getElementById('searchEmail').value = '';
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar usuarios sin filtros
    loadUsers();
}

// Función para eliminar usuarios mediante ajax
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
            fetch(`/gestor/users/${id}`, {
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


// Función para la carga de usuarios al iniciar
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
    
    // Implementar filtros automáticos en tiempo real (sin necesidad de botón "Aplicar")
    document.getElementById('searchUser').addEventListener('input', function() {
        applyFilters();
    });
    
    document.getElementById('filterRole').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('filterParking').addEventListener('change', function() {
        applyFilters();
    });

    document.getElementById('searchEmail').addEventListener('input', function() {
        applyFilters();
    });
});

// Función para renderizar la paginación utilizando como parámetro los datos de la función de AJAX
function renderPagination(pagination) {
    const summary = document.getElementById('pagination-summary');
    const indicator = document.getElementById('page-indicator');
    const prevBtn = document.getElementById('prev-page');
    const nextBtn = document.getElementById('next-page');

    const total = pagination.total;
    const from = (pagination.current_page - 1) * pagination.per_page + 1;
    const to = Math.min(from + pagination.per_page - 1, total); // ← este es el fix

    // Si no hay resultados
    if (total === 0) {
        summary.textContent = `Mostrando 0 de 0 usuarios`;
    } else {
        summary.textContent = `Mostrando ${from} a ${to} de ${total} usuarios`;
    }

    indicator.textContent = `Página ${pagination.current_page} de ${pagination.last_page}`;

    prevBtn.disabled = pagination.current_page === 1;
    nextBtn.disabled = pagination.current_page === pagination.last_page;

    prevBtn.onclick = () => {
        if (currentPage > 1) {
            currentPage--;
            loadUsers();
        }
    };

    nextBtn.onclick = () => {
        if (currentPage < pagination.last_page) {
            currentPage++;
            loadUsers();
        }
    };
}



