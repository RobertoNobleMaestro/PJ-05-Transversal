let activeFilters = {};
let currentPage = 1;
let itemsPerPage = 10;
let totalPages = 1;
let totalItems = 0;

function applyFilters() {
    // Recoger los valores de los filtros
    const tipo = document.getElementById('filterTipo').value;
    const marca = document.getElementById('filterMarca').value.trim();
    const anio = document.getElementById('filterAnio').value;
    // const valoracion = document.getElementById('filterValoracion').value; // Comentado
    const parking = document.getElementById('filterParking').value;
    
    // Actualizar el objeto de filtros activos
    activeFilters = {
        tipo: tipo,
        marca: marca,
        anio: anio,
        // valoracion: valoracion, // Comentado
        parking_id: parking
    };
    
    // Cargar vehículos con los filtros aplicados
    loadVehiculos();
}
function clearFilters() {
    document.getElementById('filterTipo').value = '';
    document.getElementById('filterMarca').value = '';
    document.getElementById('filterAnio').value = '';
    // document.getElementById('filterValoracion').value = ''; // Comentado
    
    // Reiniciar el objeto de filtros activos
    activeFilters = {};
    
    // Cargar vehículos sin filtros
    loadVehiculos();
}
function loadVehiculos() {
    // Mostrar el indicador de carga
    const loadingElement = document.getElementById('loading-vehiculos');
    const tableContainer = document.getElementById('vehiculos-table-container');
    
    loadingElement.style.display = 'block';
    loadingElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p>Cargando vehículos...</p>';
    tableContainer.style.display = 'none';
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        
    let url = new URL(dataUrl, window.location.origin);
    if (activeFilters.marca) url.searchParams.append('marca', activeFilters.marca);
    if (activeFilters.tipo) url.searchParams.append('tipo', activeFilters.tipo);
    if (activeFilters.anio) url.searchParams.append('anio', activeFilters.anio);
    // if (activeFilters.valoracion) url.searchParams.append('valoracion', activeFilters.valoracion); // Comentado
    if (activeFilters.parking_id) url.searchParams.append('parking_id', activeFilters.parking_id);
    
    url.searchParams.append('page', currentPage);
    url.searchParams.append('per_page', itemsPerPage);

    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        loadingElement.style.display = 'none';
        
        tableContainer.style.display = 'block';
        
        const tableBody = document.querySelector('#vehiculos-table tbody');
        tableBody.innerHTML = '';
        
        if (data.vehiculos === 0) {
            const emptyRow = document.createElement('tr');
            emptyRow.innerHTML = `
                <td colspan="9" class="text-center">
                    <p>No se encontraron vehículos que coincidan con los criterios de búsqueda.</p>
                </td>
            `;
            tableBody.appendChild(emptyRow);
            
            updatePaginationControls({
                total: 0,
                per_page: itemsPerPage,
                current_page: 1,
                last_page: 1
            });
            return;
        }
        
        data.vehiculos.forEach(vehiculo => {
            console.log(vehiculo);
            const row = document.createElement('tr');
            const precio = new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(vehiculo.precio);
            row.innerHTML = `
                <td>
                    ${vehiculo.imagen 
                        ? `<img src="${vehiculo.imagen}" alt="Imagen" style="width:80px; height:60px; object-fit:cover; border-radius:4px; border:2px solid #6d117e;">`
                        : '<span class="text-muted">Sin imagen</span>'}
                </td>
                <td>${vehiculo.marca}</td>
                <td>${vehiculo.modelo}</td>
                <td>${vehiculo.año}</td>
                <td>${vehiculo.kilometraje} km</td>
                <td>${vehiculo.nombre_tipo || 'No asignado'}</td>
                <td>${vehiculo.nombre_parking || 'No asignado'}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-info" 
                                title="Ver características" 
                                onclick="verCaracteristicasVehiculo(${vehiculo.id_vehiculos})">
                            <i class="fas fa-list"></i>
                        </button>
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

function verVehiculo(idVehiculo) {
    const reservasModal = new bootstrap.Modal(document.getElementById('reservasModal'));
    reservasModal.show();

    const reservastabla = document.getElementById('reservasTableBody');
    reservastabla.innerHTML = '<tr><td colspan="5" class="text-center">Cargando reservas...</td></tr>';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(`/gestor/vehiculos/${idVehiculo}/crudreservas`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        reservastabla.innerHTML = '';

        const reservas = data.reservas;

        if (!reservas || reservas.length === 0) {
            reservastabla.innerHTML = '<tr><td colspan="5" class="text-center">No hay reservas para este vehículo.</td></tr>';
            return;
        }

        reservas.forEach(reserva => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${reserva.id_reserva}</td>
                <td>${new Date(reserva.fecha_inicio).toLocaleDateString()}</td>
                <td>${new Date(reserva.fecha_fin).toLocaleDateString()}</td>
                <td>${reserva.cliente_nombre}</td>
                <td>${reserva.estado}</td>
            `;
            reservastabla.appendChild(row);
        });
    })
    .catch(error => {
        console.error('Error al cargar las reservas:', error);
        reservastabla.innerHTML = '<tr><td colspan="5" class="text-center">Error al cargar las reservas.</td></tr>';
    });
}

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

let dataUrl;
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
        
        document.getElementById('filterAnio').addEventListener('change', function() {
            currentPage = 1;
            applyFilters();
        });
        
        // document.getElementById('filterValoracion').addEventListener('change', function() {
        //     currentPage = 1;
        //     applyFilters();
        // });
        
        document.getElementById('filterParking').addEventListener('change', function() {
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

window.verCaracteristicasVehiculo = function(id) {
    fetch(`/gestor/vehiculos/${id}/caracteristicas`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const c = data.caracteristicas;
                let html = `
                    <ul class="list-group">
                        <li class="list-group-item"><b>Techo:</b> ${c.techo == 1 ? 'Sí' : 'No'}</li>
                        <li class="list-group-item"><b>Transmisión:</b> ${c.transmision}</li>
                        <li class="list-group-item"><b>Nº Puertas:</b> ${c.num_puertas}</li>
                        <li class="list-group-item"><b>Etiqueta Medioambiental:</b> ${c.etiqueta_medioambiental}</li>
                        <li class="list-group-item"><b>Aire acondicionado:</b> ${c.aire_acondicionado == 1 ? 'Sí' : 'No'}</li>
                        <li class="list-group-item"><b>Capacidad maletero:</b> ${c.capacidad_maletero} L</li>
                    </ul>
                `;
                document.getElementById('caracteristicasBody').innerHTML = html;
                new bootstrap.Modal(document.getElementById('caracteristicasModal')).show();
            } else {
                document.getElementById('caracteristicasBody').innerHTML = '<div class="alert alert-danger">No se encontraron características.</div>';
                new bootstrap.Modal(document.getElementById('caracteristicasModal')).show();
            }
        })
        .catch(() => {
            document.getElementById('caracteristicasBody').innerHTML = '<div class="alert alert-danger">Error al cargar las características.</div>';
            new bootstrap.Modal(document.getElementById('caracteristicasModal')).show();
        });
}
