/**
 * GESTIÓN DE VEHÍCULOS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene todas las funciones necesarias para administrar el listado de vehículos.
 * Incluye funcionalidades para filtrar, cargar y eliminar vehículos desde la interfaz de administración.
 */

// Variables globales para los filtros y paginación
let activeFilters = {};
let currentPage = 1;
let itemsPerPage = 10;
let totalPages = 1;
let totalItems = 0;

/**
 * applyFilters() - Aplica los filtros seleccionados por el administrador
 * 
 * Esta función captura los valores de todos los campos de filtro del formulario
 * y actualiza el objeto activeFilters. Luego llama a loadVehiculos() para 
 * mostrar los resultados filtrados.
 */
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

/**
 * clearFilters() - Restablece todos los filtros a sus valores predeterminados
 * 
 * Esta función limpia todos los campos de filtro y reinicia el objeto activeFilters.
 * Luego recarga el listado completo de vehículos sin aplicar ningún filtro.
 */
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

/**
 * loadVehiculos() - Carga la lista de vehículos desde el servidor
 * 
 * Esta función realiza una petición AJAX al servidor para obtener los vehículos,
 * aplicando los filtros que estén activos. Muestra un indicador de carga mientras
 * se completa la petición y luego actualiza la tabla con los resultados.
 */
function loadVehiculos() {
    console.log('Cargando vehículos, página ' + currentPage + '...');
    // Mostrar el indicador de carga
    const loadingElement = document.getElementById('loading-vehiculos');
    const tableContainer = document.getElementById('vehiculos-table-container');
    
    loadingElement.style.display = 'block';
    loadingElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p>Cargando vehículos...</p>';
    tableContainer.style.display = 'none';
    
    // Obtener el CSRF token para realizar peticiones seguras
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    
    // Depuración - mostrar los filtros activos
    console.log('Filtros activos:', activeFilters);
    
    // Construir la URL con los parámetros de filtro
    let url = new URL(dataUrl, window.location.origin);
    
    // Agregar todos los filtros activos a la URL
    // Asegurarnos de que los parámetros de filtro tengan los nombres correctos que espera el controlador
    if (activeFilters.marca) url.searchParams.append('marca', activeFilters.marca);
    if (activeFilters.tipo) url.searchParams.append('tipo', activeFilters.tipo);
    if (activeFilters.lugar) url.searchParams.append('lugar', activeFilters.lugar);
    if (activeFilters.anio) url.searchParams.append('anio', activeFilters.anio);
    if (activeFilters.valoracion) url.searchParams.append('valoracion', activeFilters.valoracion);
    
    // Agregar parámetros de paginación
    url.searchParams.append('page', currentPage);
    url.searchParams.append('per_page', itemsPerPage);
    
    console.log('URL de la petición:', url.toString());
    
    // Realizar petición AJAX al servidor
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        console.log('Estado de la respuesta:', response.status);
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        // Depurar para ver qué datos recibimos
        console.log('Datos recibidos de la API:', data);
        
        // Ocultar el indicador de carga
        loadingElement.style.display = 'none';
        
        // Mostrar la tabla
        tableContainer.style.display = 'block';
        
        // Limpiar la tabla
        const tableBody = document.querySelector('#vehiculos-table tbody');
        tableBody.innerHTML = '';
        
        // Verificar si hay vehículos
        if (data.vehiculos.length === 0) {
            // Mostrar mensaje si no hay vehículos
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="9" class="text-center">
                    <p>No se encontraron vehículos que coincidan con los criterios de búsqueda.</p>
                </td>
            `;
            tableBody.appendChild(emptyRow);
            
            // Actualizar la información de paginación con 0 resultados
            updatePaginationControls({
                total: 0,
                per_page: itemsPerPage,
                current_page: 1,
                last_page: 1
            });
            return;
        }
        
        // Recorrer la lista de vehículos y crear filas en la tabla
        data.vehiculos.forEach(vehiculo => {
            const row = document.createElement('tr');
            
            // Determinar el color de texto según la disponibilidad
            // Formatear el precio para mostrar
            const precio = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(vehiculo.precio);
            
            // Crear la fila con los datos del vehículo
            row.innerHTML = `
                <td>${vehiculo.id_vehiculos}</td>
                <td>${vehiculo.marca}</td>
                <td>${vehiculo.modelo}</td>
                <td>${vehiculo.año}</td>
                <td>${vehiculo.kilometraje} km</td>
                <td>${vehiculo.nombre_lugar || 'No asignado'}</td>
                <td>${vehiculo.nombre_tipo || 'No asignado'}</td>
                <td>
                    <div class="btn-group">
                        <a href="/gestor/vehiculos/${vehiculo.id_vehiculos}/edit" class="btn btn-sm btn-primary" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger" onclick="deleteVehiculo(${vehiculo.id_vehiculos}, '${vehiculo.marca} ${vehiculo.modelo}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            
            tableBody.appendChild(row);
        });
        
        // Actualizar los controles de paginación
        if (data.pagination) {
            updatePaginationControls(data.pagination);
        }
    })
    .catch(error => {
        // Manejar errores en la petición AJAX
        console.error('Error en la petición:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: `<p class="lead">Error al cargar vehículos: ${error.message}</p>`,
            confirmButtonColor: '#9F17BD'
        });
        loadingElement.style.display = 'block';
        loadingElement.innerHTML = `<div class="alert alert-danger">Error al cargar vehículos: ${error.message}</div>`;
    });
}

/**
 * deleteVehiculo(id, nombre) - Elimina un vehículo del sistema
 * 
 * @param {number} id - ID del vehículo a eliminar
 * @param {string} nombre - Nombre del vehículo (marca + modelo) para mostrar en la confirmación
 * 
 * Esta función muestra un diálogo de confirmación antes de eliminar el vehículo.
 * Si el usuario confirma, realiza una petición DELETE al servidor y muestra el
 * resultado de la operación.
 */
function deleteVehiculo(id, nombre) {
    // Mostrar diálogo de confirmación usando SweetAlert2
    Swal.fire({
        title: `<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> Eliminar Vehículo</span>`,
        html: `<p class="lead">¿Estás seguro de que deseas eliminar el vehículo "${nombre}"?</p><p class="text-muted">Esta acción no se puede deshacer.</p>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-trash-alt"></i> Eliminar',
        cancelButtonText: '<i class="fas fa-times"></i> Cancelar',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostrar indicador de carga durante el proceso de eliminación
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
                text: 'Eliminando vehículo',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            // Obtener el token CSRF de manera segura
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Enviar petición DELETE al servidor
            fetch(`/gestor/vehiculos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Manejar la respuesta del servidor
                if (data.status === 'success') {
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '<span class="text-success"><i class="fas fa-check-circle"></i> Completado</span>',
                        html: `<p class="lead">${data.message}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                    
                    // Recargar la lista de vehículos
                    loadVehiculos();
                } else {
                    // Mostrar mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar el vehículo'}</p>`,
                        confirmButtonColor: '#9F17BD'
                    });
                }
            })
            .catch(error => {
                // Manejar errores en la petición
                console.error('Error al eliminar vehículo:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                    html: `<p class="lead">Error al eliminar el vehículo: ${error.message}</p>`,
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
 * Este bloque configura los eventos iniciales y prepara la interfaz
 * cuando la página se carga por completo.
 */
/**
 * updatePaginationControls(pagination) - Actualiza los controles de paginación
 * 
 * @param {Object} pagination - Información de paginación del servidor
 * 
 * Esta función actualiza los botones y textos de paginación
 * para reflejar el estado actual de la paginación.
 */
function updatePaginationControls(pagination) {
    // Guardar valores en variables globales
    totalItems = pagination.total;
    totalPages = pagination.last_page;
    currentPage = pagination.current_page;
    
    // Actualizar el resumen de paginación
    const from = pagination.from || 0;
    const to = pagination.to || 0;
    document.getElementById('pagination-summary').textContent = 
        `Mostrando ${from}-${to} de ${totalItems} vehículos`;
    
    // Actualizar el indicador de página
    document.getElementById('page-indicator').textContent = 
        `Página ${currentPage} de ${totalPages}`;
    
    // Habilitar/deshabilitar botones de navegación
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
    
    prevButton.disabled = currentPage <= 1;
    nextButton.disabled = currentPage >= totalPages;
}

/**
 * goToPage(page) - Navega a una página específica
 * 
 * @param {number} page - Número de página a mostrar
 * 
 * Esta función actualiza la página actual y recarga los datos.
 */
function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    loadVehiculos();
}

document.addEventListener('DOMContentLoaded', function() {
    // Obtener la URL de datos del atributo data-url del contenedor
    const vehiculosContainer = document.getElementById('vehiculos-table-container');
    if (vehiculosContainer) {
        dataUrl = vehiculosContainer.dataset.url;
        console.log('URL de datos configurada:', dataUrl);
        
        // Cargar vehículos inicialmente
        loadVehiculos();
        
        // Configurar eventos para los filtros
        // Configurar filtros automáticos para cada campo
        document.getElementById('filterMarca').addEventListener('input', function() {
            currentPage = 1; // Volver a la primera página al filtrar
            applyFilters();
        });
        
        document.getElementById('filterTipo').addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });
        
        document.getElementById('filterLugar').addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });
        
        document.getElementById('filterAnio').addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });
        
        document.getElementById('filterValoracion').addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });
        
        document.getElementById('clearFilters').addEventListener('click', function(e) {
            e.preventDefault();  // Prevenir el comportamiento predeterminado
            currentPage = 1;     // Resetear a la primera página
            clearFilters();      // Limpiar filtros
        });
        
        // Configurar eventos para la paginación
        document.getElementById('prev-page').addEventListener('click', function() {
            if (currentPage > 1) {
                goToPage(currentPage - 1);
            }
        });
        
        document.getElementById('next-page').addEventListener('click', function() {
            if (currentPage < totalPages) {
                goToPage(currentPage + 1);
            }
        });
        
        // Configurar el selector de items por página
        document.getElementById('items-per-page').addEventListener('change', function() {
            itemsPerPage = parseInt(this.value);
            currentPage = 1; // Volver a la primera página
            loadVehiculos();
        });
    } else {
        console.error('No se encontró el contenedor de vehículos');
    }
});
