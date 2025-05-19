@extends('layouts.admin_financiero')

@section('content')
@php
    // Debug information to find the count() error
    if (isset($debug) && $debug) {
        echo '<div class="alert alert-info">Debug Info:<br>';
        
        // Print out the variables passed to the view
        echo 'Variables passed to view:<br>';
        foreach (get_defined_vars()['__data'] as $key => $value) {
            echo "$key: " . (is_object($value) ? get_class($value) : gettype($value)) . '<br>';
        }
        
        echo '</div>';
    }
    
    // Ensure $asalariados exists and is properly initialized to prevent count() errors
    if (!isset($asalariados)) {
        $asalariados = [];
    }
@endphp
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Gestión de Asalariados</h1>
            <p class="text-muted">Sede de {{ $sede->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.resumen') }}" class="btn btn-secondary">
                <i class="fas fa-chart-bar"></i> Resumen Financiero
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
                <h5 class="mb-0">Listado de Asalariados - {{ $sede->nombre }}</h5>
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
                        <label for="filterParking" class="filter-label">Parking</label>
                        <select id="filterParking" class="form-select form-select-sm">
                            <option value="">Todos los parkings</option>
                            @foreach($parkings as $parking)
                                <option value="{{ $parking->id }}">{{ $parking->nombre }}</option>
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
                            <th>Día de cobro</th>
                            <th>Parking asignado</th>
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
                    <p class="card-text display-4">{{ $asalariados->count() }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Gasto Mensual Total</h5>
                    @php
                    $totalSalarios = $asalariados->sum('salario');
                    @endphp
                    <p class="card-text display-4">{{ number_format($totalSalarios, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Salario Promedio</h5>
                    @php
                    $avgSalario = $asalariados->count() > 0 ? $asalariados->avg('salario') : 0;
                    @endphp
                    <p class="card-text display-4">{{ number_format($avgSalario, 2, ',', '.') }} €</p>
                </div>
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
        const filterParking = document.getElementById('filterParking');
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
            let url = '{{ route("asalariados.data") }}?page=' + currentPage + '&perPage=' + perPage;
            
            // Añadir filtros si existen
            if (searchInput.value.trim()) {
                url += '&nombre=' + encodeURIComponent(searchInput.value.trim());
            }
            
            if (filterRol.value) {
                url += '&rol=' + encodeURIComponent(filterRol.value);
            }
            
            if (filterParking.value) {
                url += '&parking=' + encodeURIComponent(filterParking.value);
            }
            
            // Realizar la petición AJAX
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
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
                    if (data) {
                        updateStatsCards(data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger"><i class="fas fa-exclamation-triangle me-2"></i>${error.message}</td></tr>`;
                });
        }
        
        // Función para renderizar los asalariados en la tabla
        function renderAsalariados(asalariados) {
            // Limpiar la tabla
            tableBody.innerHTML = '';
            
            if (asalariados.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="6" class="text-center">No hay asalariados que coincidan con los filtros</td></tr>';
                return;
            }
            
            // Recorrer asalariados y crear filas
            asalariados.forEach(asalariado => {
                // Determinar la clase para la badge según el rol
                let rolClass = '';
                switch(asalariado.nombre_rol.toLowerCase()) {
                    case 'gestor':
                        rolClass = 'bg-primary';
                        break;
                    case 'mecanico':
                        rolClass = 'bg-warning text-dark';
                        break;
                    case 'admin_financiero':
                        rolClass = 'bg-success';
                        break;
                    default:
                        rolClass = 'bg-secondary';
                }
                
                // Crear la fila
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${asalariado.nombre}</td>
                    <td><span class="badge ${rolClass}">${asalariado.nombre_rol}</span></td>
                    <td>${formatCurrency(asalariado.salario)}</td>
                    <td>${asalariado.dia_cobro}</td>
                    <td>${asalariado.nombre_parking}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="/asalariados/${asalariado.id}/detalle" class="btn btn-sm" style="background-color: #9F17BD; color: white;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/asalariados/${asalariado.id}/editar" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
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
            const asalariadosCountElement = document.getElementById('asalariadosCount');
            const totalSalariosElement = document.getElementById('totalSalarios');
            const avgSalariosElement = document.getElementById('avgSalarios');
            
            if (asalariadosCountElement && data && data.total_count !== undefined) {
                asalariadosCountElement.textContent = data.total_count;
            } else if (asalariadosCountElement) {
                asalariadosCountElement.textContent = '0';
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
        
        filterParking.addEventListener('change', function() {
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
            filterParking.value = '';
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
