@extends('layouts.admin_financiero')

@section('content')
@php
    // Ensure $asalariados exists and is properly initialized to prevent count() errors
    if (!isset($asalariados)) {
        $asalariados = [];
    }
@endphp
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Asalariados</h1>
            <p class="text-muted">Administrador Financiero</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.asalariados.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nuevo Asalariado
            </a>
            <a href="{{ route('admin.financiero.asalariados.inactivos') }}" class="btn btn-secondary ms-2">
                <i class="fas fa-user-slash"></i> Usuarios dados de baja
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header" style="background-color: #9F17BD; color: white;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Listado de Asalariados</h5>
                <input type="text" id="searchInput" class="form-control form-control-sm w-25" placeholder="Buscar asalariado...">
            </div>
        </div>
        <div class="card-body">
            <div class="filter-section mb-3">
                <div class="row">
                    <div class="col-md-3 mb-2">
                        <label for="filterRol" class="filter-label">Rol</label>
                        <select id="filterRol" class="form-select form-select-sm">
                            <option value="">Todos los roles</option>
                            @foreach($roles as $rol)
                                <option value="{{ $rol }}">{{ ucfirst($rol) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label for="filterLugar" class="filter-label">Lugar</label>
                        <select id="filterLugar" class="form-select form-select-sm">
                            <option value="">Todos los lugares</option>
                            @foreach($lugares as $lugar)
                                <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button id="clearFilters" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i> Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-hover crud-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Salario</th>
                            <th>Fecha de contratación</th>
                            <th>Días trabajados</th>
                            <th>Lugar</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="asalariadosTableBody">
                        <!-- Los datos se cargarán vía AJAX -->
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <select id="perPageSelect" class="form-select form-select-sm">
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                        <option value="100">100 por página</option>
                    </select>
                </div>
                <div>
                    <span id="showing-entries">Mostrando 0 de 0 registros</span>
                </div>
                <div>
                    <nav aria-label="Paginación de asalariados">
                        <ul class="pagination pagination-sm" id="pagination">
                            <!-- La paginación se cargará vía AJAX -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white" style="background-color: #9F17BD;">
                <div class="card-body">
                    <h5 class="card-title">Total Asalariados</h5>
                    <p class="card-text display-4" id="total-asalariados">Cargando...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Gasto Mensual Total</h5>
                    <p class="card-text display-4" id="total-salarios">Cargando...</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Salario Promedio</h5>
                    <p class="card-text display-4" id="avg-salario">Cargando...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación para dar de baja -->
<div class="modal fade" id="desactivarModal" tabindex="-1" aria-labelledby="desactivarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="desactivarModalLabel">Programar baja de asalariado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas programar la baja de <strong id="nombreAsalariado"></strong>?</p>
                <p class="text-danger fw-bold">La baja se programará para el día 1 del mes siguiente.</p>
                <p>Al programar la baja de un asalariado:</p>
                <ul>
                    <li>El asalariado seguirá activo hasta el día 1 del mes siguiente</li>
                    <li>Se conservarán los días trabajados acumulados</li>
                    <li>Se programará automáticamente el cálculo del salario proporcional</li>
                    <li>El asalariado NO aparecerá en la lista de inactivos hasta la fecha programada</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-warning text-dark" id="confirmarDesactivar">
                    <i class="fas fa-calendar-alt me-1"></i> Programar baja
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Variables y elementos del DOM
        const tableBody = document.getElementById('asalariadosTableBody');
        const searchInput = document.getElementById('searchInput');
        const filterRol = document.getElementById('filterRol');
        const filterLugar = document.getElementById('filterLugar');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const perPageSelect = document.getElementById('perPageSelect');
        const pagination = document.getElementById('pagination');
        const showingEntries = document.getElementById('showing-entries');
        
        // Variables para la paginación y filtros
        let currentPage = 1;
        let perPage = 10;
        let totalAsalariados = 0;
        let totalPages = 0;
        
        // Inicializar la carga de datos
        loadAsalariados();
        
        // Función principal para cargar los asalariados
        function loadAsalariados() {
            // Mostrar indicador de carga
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Cargando asalariados...</td></tr>';
            
            // Construir URL con parámetros
            let url = '{{ route("admin.asalariados.data") }}?page=' + currentPage + '&perPage=' + perPage;
            
            // Añadir filtros si existen
            if (searchInput.value.trim()) {
                url += '&nombre=' + encodeURIComponent(searchInput.value.trim());
            }
            
            if (filterRol.value) {
                url += '&rol=' + encodeURIComponent(filterRol.value);
            }
            
            if (filterLugar.value) {
                url += '&lugar=' + encodeURIComponent(filterLugar.value);
            }
            
            // Mostrar un mensaje de carga
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin me-2"></i>Cargando asalariados...</td></tr>';
            
            // Establecer un tiempo máximo para la petición
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos
            
            // Realizar la petición AJAX con fetch, pero sin usar el filtro de rol por ahora
            // Eliminar el parámetro rol para evitar problemas
            let urlSinRol = url;
            if (filterRol.value) {
                urlSinRol = url.replace(`&rol=${encodeURIComponent(filterRol.value)}`, '');
            }
            
            // Registrar la URL que estamos usando
            console.log('Realizando petición a:', urlSinRol);
            
            fetch(urlSinRol, { signal: controller.signal })
                .then(response => {
                    clearTimeout(timeoutId);
                    
                    if (!response.ok) {
                        return response.text().then(text => {
                            console.error('Error en respuesta del servidor:', response.status, text);
                            throw new Error(`Error del servidor (${response.status}): ${text || 'No hay detalles disponibles'}`);
                        });
                    }
                    
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);
                    
                    // Procesar y mostrar datos
                    if (data && data.asalariados) {
                        renderAsalariados(data.asalariados);
                        
                        // Asegurarnos de que data.pagination exista
                        if (data.pagination) {
                            updatePagination(data.pagination);
                            
                            // Actualizar mostrador de entradas
                            const start = data.asalariados.length > 0 ? (currentPage - 1) * perPage + 1 : 0;
                            const end = Math.min(start + data.asalariados.length - 1, data.pagination.total);
                            showingEntries.textContent = `Mostrando ${start} a ${end} de ${data.pagination.total} registros`;
                            
                            totalAsalariados = data.pagination.total;
                            totalPages = data.pagination.last_page;
                        } else {
                            // Si no hay datos de paginación
                            pagination.innerHTML = '';
                            showingEntries.textContent = 'Mostrando 0 de 0 registros';
                            totalAsalariados = 0;
                            totalPages = 0;
                        }
                    } else {
                        // Si no hay datos de asalariados
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay datos disponibles</td></tr>';
                        pagination.innerHTML = '';
                        showingEntries.textContent = 'Mostrando 0 de 0 registros';
                    }
                    
                    // Actualizar estadísticas si existen
                    if (data && data.summary) {
                        updateStatsCards(data);
                    }
                })
                .catch(error => {
                    clearTimeout(timeoutId);
                    console.error('Error en la petición:', error);
                    
                    // Mensaje de error mejorado
                    let mensajeError = error.message || 'Error desconocido al cargar los datos';
                    
                    // Si es un error de timeout
                    if (error.name === 'AbortError') {
                        mensajeError = 'La petición ha tardado demasiado tiempo. Por favor, inténtalo de nuevo.';
                    }
                    
                    // Mostrar un mensaje de error amigable
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="6" class="text-center text-danger p-4">
                                <div>
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                    <h5>Error al cargar los datos</h5>
                                    <p>${mensajeError}</p>
                                    <button class="btn btn-sm btn-outline-secondary mt-2" onclick="loadAsalariados()">
                                        <i class="fas fa-sync-alt me-1"></i> Reintentar
                                    </button>
                                </div>
                            </td>
                        </tr>`;
                    
                    // Reiniciar la paginación y contadores
                    pagination.innerHTML = '';
                    showingEntries.textContent = 'Error al cargar los datos';
                    
                    // Reiniciar las tarjetas de estadísticas
                    document.getElementById('total-asalariados').textContent = '-';
                    document.getElementById('total-salarios').textContent = '-';
                    document.getElementById('avg-salario').textContent = '-';
                });
        }
        
        // Función para renderizar los asalariados en la tabla
        function renderAsalariados(asalariados) {
            // Limpiar la tabla
            tableBody.innerHTML = '';
            
            if (asalariados.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No hay asalariados que coincidan con los filtros</td></tr>';
                return;
            }
            
            // Recorrer asalariados y crear filas
            asalariados.forEach(asalariado => {
                // Determinar la clase para la badge según el rol
                let rolClass = 'bg-secondary';
                let rolName = 'sin rol';
                
                try {
                    // Primero intentamos el acceso con role (singular)
                    if (asalariado.usuario && asalariado.usuario.role && asalariado.usuario.role.nombre_rol) {
                        rolName = asalariado.usuario.role.nombre_rol.toLowerCase();
                    } 
                    // Si no existe, intentamos con id_roles directamente
                    else if (asalariado.usuario && asalariado.usuario.id_roles) {
                        const roleId = asalariado.usuario.id_roles;
                        // Mapeamos IDs de roles a nombres
                        switch(roleId) {
                            case 1: rolName = 'admin'; break;
                            case 2: rolName = 'cliente'; break;
                            case 3: rolName = 'gestor'; break;
                            case 4: rolName = 'mecanico'; break;
                            case 5: rolName = 'admin_financiero'; break;
                            case 6: rolName = 'chofer'; break;
                            default: rolName = 'sin rol';
                        }
                    }
                    
                    switch(rolName) {
                        case 'gestor':
                            rolClass = 'bg-primary';
                            break;
                        case 'mecanico':
                            rolClass = 'bg-warning text-dark';
                            break;
                        case 'admin_financiero':
                            rolClass = 'bg-success';
                            break;
                        case 'chofer':
                            rolClass = 'bg-info';
                            break;
                        default:
                            rolClass = 'bg-secondary';
                    }
                } catch (error) {
                    console.error('Error al procesar el rol del asalariado:', error);
                }
                
                // Formatear la fecha de contratación
                const hiredate = new Date(asalariado.hiredate);
                const formattedDate = hiredate.toLocaleDateString('es-ES');
                
                // Determinar nombre y sede de manera segura
                let nombreCompleto = 'Usuario no disponible';
                let nombreRol = rolName; // Usamos el mismo valor que usamos para el color
                let lugarNombre = 'Sin lugar asignado';
                
                try {
                    if (asalariado.usuario) {
                        const nombre = asalariado.usuario.nombre || '';
                        const apellidos = asalariado.usuario.apellidos || '';
                        nombreCompleto = nombre + (apellidos ? ' ' + apellidos : '');
                        
                        if (nombreCompleto.trim() === '') {
                            nombreCompleto = 'Usuario ID: ' + asalariado.id_usuario;
                        }
                    }
                    
                    if (asalariado.usuario && asalariado.usuario.role && asalariado.usuario.role.nombre_rol) {
                        nombreRol = asalariado.usuario.role.nombre_rol;
                    }
                    
                    if (asalariado.sede && asalariado.sede.nombre) {
                        lugarNombre = asalariado.sede.nombre;
                    }
                } catch (error) {
                    console.error('Error al procesar datos del asalariado:', error);
                }
                
                // Crear la fila
                const row = document.createElement('tr');
                // Verificar si tiene una baja programada
                let bajaProgramadaHtml = '';
                if (asalariado.estado_baja_programada === 'pendiente' && asalariado.fecha_baja_programada) {
                    const fechaBajaProgramada = new Date(asalariado.fecha_baja_programada);
                    const fechaFormateada = fechaBajaProgramada.toLocaleDateString('es-ES');
                    bajaProgramadaHtml = `<span class="badge bg-warning text-dark">Baja programada: ${fechaFormateada}</span>`;
                }
                
                row.innerHTML = `
                    <td>${nombreCompleto}</td>
                    <td><span class="badge ${rolClass}">${rolName === 'admin_financiero' ? 'Admin Financiero' : rolName === 'sin rol' ? 'Sin rol' : rolName.charAt(0).toUpperCase() + rolName.slice(1)}</span></td>
                    <td>${formatCurrency(asalariado.salario)}</td>
                    <td>${formattedDate}</td>
                    <td>${asalariado.dias_trabajados !== null ? asalariado.dias_trabajados : 0}</td>
                    <td>${lugarNombre}</td>
                    <td>
                        ${bajaProgramadaHtml}
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ url('/admin-financiero/asalariados') }}/${asalariado.id}/detalle" class="btn btn-sm" style="background-color: #9F17BD; color: white;" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ url('/admin-financiero/asalariados') }}/${asalariado.id}/editar" class="btn btn-sm btn-primary" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ url('/admin-financiero/asalariados') }}/${asalariado.id}/nomina" class="btn btn-sm btn-success" title="Generar nómina">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger desactivar-btn" title="Dar de baja" data-id="${asalariado.id}" data-nombre="${nombreCompleto}">
                                <i class="fas fa-user-slash"></i>
                            </button>
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        }
        
        // Función para actualizar la paginación
        function updatePagination(paginationData) {
            pagination.innerHTML = '';
            
            // No mostrar paginación si solo hay una página
            if (paginationData.last_page <= 1) {
                return;
            }
            
            // Botón Anterior
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${paginationData.current_page === 1 ? 'disabled' : ''}`;
            prevLi.innerHTML = `<a class="page-link" href="#" data-page="${paginationData.current_page - 1}">Anterior</a>`;
            pagination.appendChild(prevLi);
            
            // Determinar qué páginas mostrar
            let startPage = Math.max(1, paginationData.current_page - 2);
            let endPage = Math.min(paginationData.last_page, startPage + 4);
            
            // Ajustar startPage si necesario
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }
            
            // Páginas individuales
            for (let i = startPage; i <= endPage; i++) {
                const pageLi = document.createElement('li');
                pageLi.className = `page-item ${i === paginationData.current_page ? 'active' : ''}`;
                pageLi.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
                pagination.appendChild(pageLi);
            }
            
            // Botón Siguiente
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${paginationData.current_page === paginationData.last_page ? 'disabled' : ''}`;
            nextLi.innerHTML = `<a class="page-link" href="#" data-page="${paginationData.current_page + 1}">Siguiente</a>`;
            pagination.appendChild(nextLi);
            
            // Evento para la paginación
            pagination.querySelectorAll('.page-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const page = parseInt(this.getAttribute('data-page'));
                    if (page >= 1 && page <= paginationData.last_page) {
                        currentPage = page;
                        loadAsalariados();
                    }
                });
            });
        }
        
        // Función para actualizar las tarjetas de estadísticas
        function updateStatsCards(data) {
            // Actualizar el contador de asalariados, salario total y promedio si existen en el DOM
            const totalAsalariadosElement = document.getElementById('total-asalariados');
            const totalSalariosElement = document.getElementById('total-salarios');
            const avgSalarioElement = document.getElementById('avg-salario');
            
            if (data && data.summary) {
                // Usar los datos de resumen proporcionados por la API
                if (totalAsalariadosElement) {
                    totalAsalariadosElement.textContent = data.summary.total || '0';
                }
                
                if (totalSalariosElement) {
                    totalSalariosElement.textContent = formatCurrency(data.summary.totalSalarios || 0);
                }
                
                if (avgSalarioElement) {
                    avgSalarioElement.textContent = formatCurrency(data.summary.avgSalario || 0);
                }
            } else if (data && data.pagination) {
                // Usar los datos de paginación como alternativa
                if (totalAsalariadosElement) {
                    totalAsalariadosElement.textContent = data.pagination.total || '0';
                }
                
                // No tenemos datos para estas tarjetas, mostrar valores por defecto
                if (totalSalariosElement) {
                    totalSalariosElement.textContent = formatCurrency(0);
                }
                
                if (avgSalarioElement) {
                    avgSalarioElement.textContent = formatCurrency(0);
                }
            } else {
                // Valores por defecto si no hay datos disponibles
                if (totalAsalariadosElement) totalAsalariadosElement.textContent = '0';
                if (totalSalariosElement) totalSalariosElement.textContent = formatCurrency(0);
                if (avgSalarioElement) avgSalarioElement.textContent = formatCurrency(0);
            }
        }
        
        // Función para formatear moneda
        function formatCurrency(value) {
            return new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);
        }
        
        // Eventos para filtros
        searchInput.addEventListener('input', debounce(function() {
            currentPage = 1; // Resetear a la primera página al filtrar
            loadAsalariados();
        }, 500));
        
        filterRol.addEventListener('change', function() {
            currentPage = 1;
            loadAsalariados();
        });
        
        filterLugar.addEventListener('change', function() {
            currentPage = 1;
            loadAsalariados();
        });
        
        // Evento para cambiar registros por página
        perPageSelect.addEventListener('change', function() {
            perPage = parseInt(this.value);
            currentPage = 1; // Resetear a primera página
            loadAsalariados();
        });
        
        // Limpiar filtros
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterRol.value = '';
            filterLugar.value = '';
            currentPage = 1;
            loadAsalariados();
        });
        
        // Función de debounce para evitar múltiples llamadas
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
        // Código para manejar la desactivación de asalariados
        let asalariadoId;
        
        // Mostrar modal de confirmación para desactivar
        $(document).on('click', '.desactivar-btn', function() {
            asalariadoId = $(this).data('id');
            const nombre = $(this).data('nombre');
            $('#nombreAsalariado').text(nombre);
            $('#desactivarModal').modal('show');
        });
        
        // Confirmar desactivación
        $('#confirmarDesactivar').on('click', function() {
            $.ajax({
                url: `/admin-financiero/asalariados/${asalariadoId}/desactivar`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#desactivarModal').modal('hide');
                    // Mostrar mensaje de éxito con la fecha programada
                    let mensaje = 'Baja programada correctamente.';
                    
                    // Si la respuesta incluye la fecha, mostrarla
                    if (response.fecha_baja_programada) {
                        mensaje = `Baja programada para el día ${response.fecha_baja_programada}. El asalariado seguirá activo hasta esa fecha.`;
                    }
                    
                    Swal.fire({
                        title: '¡Baja programada!',
                        text: mensaje,
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Recargar los asalariados
                        loadAsalariados();
                    });
                },
                error: function(xhr) {
                    $('#desactivarModal').modal('hide');
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Ocurrió un error al desactivar el asalariado',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });
    });
</script>
@endsection

@section('styles')
<style>
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    .crud-table {
        font-size: 0.9rem;
    }
    .crud-table th {
        font-weight: 600;
    }
    .filter-section {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>
@endsection
