// Variables globales para los filtros
let activeFilters = {};

// Función para aplicar los filtros automáticamente
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

// Limpiar todos los filtros
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

// Función global para cargar vehículos
function loadVehiculos() {
    console.log('Cargando vehículos...');
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
    Object.keys(activeFilters).forEach(key => {
        if (activeFilters[key]) {
            url.searchParams.append(key, activeFilters[key]);
        }
    });
    
    console.log('URL de la petición:', url.toString());
    
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
        
        // Verificar si tenemos datos o si hay error
        if (data.status === 'error') {
            loadingElement.style.display = 'block';
            loadingElement.innerHTML = `<div class="alert alert-danger">${data.message || 'Error al cargar los vehículos'}</div>`;
            return;
        }
        
        // Verificar si tenemos vehículos
        if (!data.vehiculos || data.vehiculos.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `<td colspan="9" class="text-center">No se encontraron vehículos con los filtros aplicados</td>`;
            tableBody.appendChild(row);
        } else {
            // Mostrar el número total de vehículos encontrados
            console.log(`Se encontraron ${data.vehiculos.length} vehículos`);
            
            data.vehiculos.forEach(vehiculo => {
                console.log('Procesando vehículo:', vehiculo); // Depurar cada vehículo
                
                // Verificar si el vehículo tiene la estructura esperada
                if (!vehiculo || !vehiculo.id_vehiculos) {
                    console.error('Vehículo con formato inválido:', vehiculo);
                    return; // Continuar con el siguiente vehículo
                }
                
                // Determinar el año del vehículo, manejando diferentes nombres de campo
                const anio = vehiculo.anio || vehiculo.año || 'N/A';
                
                // Escapar valores para prevenir XSS
                const marca = vehiculo.marca ? vehiculo.marca.replace(/'/g, "\'") : '';
                const modelo = vehiculo.modelo ? vehiculo.modelo.replace(/'/g, "\'") : '';
                
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${vehiculo.id_vehiculos || ''}</td>
                    <td>${marca || ''}</td>
                    <td>${modelo || ''}</td>
                    <td>${anio}</td>
                    <td>${vehiculo.kilometraje || ''}</td>
                    <td>${vehiculo.seguro_incluido ? 'Sí' : 'No'}</td>
                    <td>${vehiculo.nombre_lugar || 'No asignado'}</td>
                    <td>${vehiculo.nombre_tipo || 'No asignado'}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="/admin/vehiculos/${vehiculo.id_vehiculos}/edit" class="btn btn-sm btn-outline-primary" title="Editar"><i class="fas fa-edit"></i></a>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteVehiculo(${vehiculo.id_vehiculos}, '${marca} ${modelo}')" title="Eliminar"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        }
    })
    .catch(error => {
        console.error('Error en la petición:', error);
        loadingElement.style.display = 'block';
        loadingElement.innerHTML = `<div class="alert alert-danger">Error al cargar vehículos: ${error.message}</div>`;
    });
}

// Función para eliminar vehículo
function deleteVehiculo(id, nombre) {
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
            // Mostrar cargando
            Swal.fire({
                title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
                text: 'Eliminando vehículo',
                showConfirmButton: false,
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            // Obtener el token CSRF de manera segura
            let csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch(`/admin/vehiculos/${id}`, {
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
                    loadVehiculos();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                        html: `<p class="lead">${data.message || 'Error al eliminar el vehículo'}</p>`,
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
    console.log('DOM cargado, iniciando carga de vehículos');
    
    // Obtener la URL de datos desde el atributo data-url
    dataUrl = document.getElementById('vehiculos-table-container').getAttribute('data-url');
    
    if (!dataUrl) {
        console.error('No se encontró la URL de datos. Asegúrate de establecer el atributo data-url en el contenedor de la tabla.');
        return;
    }
    
    // Inicializar la carga de vehículos
    loadVehiculos();
    
    // Event listener para el botón de limpiar filtros
    document.getElementById('clearFilters').addEventListener('click', clearFilters);
    
    // Event listeners para aplicar filtros automáticamente al cambiar
    document.getElementById('filterMarca').addEventListener('input', applyFilters);
    document.getElementById('filterTipo').addEventListener('change', applyFilters);
    document.getElementById('filterLugar').addEventListener('change', applyFilters);
    document.getElementById('filterAnio').addEventListener('change', applyFilters);
    document.getElementById('filterValoracion').addEventListener('change', applyFilters);
});
