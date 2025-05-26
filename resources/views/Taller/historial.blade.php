@extends('layouts.admin')

@section('title', 'Historial de Mantenimientos')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/taller-historial.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">

@endpush

@section('content')
<div class="admin-container">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('Taller.index') }}" class="{{ request()->routeIs('Taller.index*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Gestión del Taller</a></li>
            <li><a href="{{ route('Taller.historial') }}" class="{{ request()->routeIs('Taller.historial*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Historial Mantenimiento</a></li>
        </ul>
    </div>

    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Historial de Mantenimientos</h1>
            <a href="{{ route('gestor.index') }}" class="btn-outline-purple">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>

        <div class="container mt-4">
            <div class="mb-3 d-flex align-items-center gap-2">
                <div id="filtros-estado" class="btn-group" role="group" aria-label="Filtros de estado">
                    <button type="button" class="btn btn-outline-purple active" data-estado="todos">Todos</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="pendiente">Pendiente</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="completado">Completado</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="cancelado">Cancelado</button>
                </div>
                <div id="filtros-tipo" class="btn-group ms-3" role="group" aria-label="Filtros de tipo">
                    <button type="button" class="btn btn-outline-purple active" data-tipo="todos">Todos</button>
                    <button type="button" class="btn btn-outline-purple" data-tipo="mantenimiento">Mantenimiento</button>
                    <button type="button" class="btn btn-outline-purple" data-tipo="averia">Avería</button>
                </div>
                <button id="btn-limpiar-filtros" type="button" class="btn btn-outline-purple ms-3" title="Limpiar filtros">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <div id="vehiculos-table-container">
                <table class="crud-table" id="tablaMantenimientos">
                    <thead>
                        <tr>
                            <th>Vehículo</th>
                            <th>Taller</th>
                            <th>Fecha Completa</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Datos cargados dinámicamente -->
                    </tbody>
                </table>
                <div class="pagination-container">
                    <ul class="pagination" id="pagination-mantenimientos"></ul>
                </div>
            </div>
            <div id="paginacion-mantenimientos" class="mt-3"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
<script>
// --- Definición global ---
function renderPagination(pagination, estado, motivo) {
    const pagContainer = document.getElementById('pagination-mantenimientos');
    pagContainer.innerHTML = '';
    if (!pagination || pagination.last_page <= 1) return;
    // Previous
    const prev = document.createElement('li');
    prev.className = 'page-item' + (pagination.current_page === 1 ? ' disabled' : '');
    prev.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page - 1}">&laquo;</a>`;
    pagContainer.appendChild(prev);
    // Pages
    for (let i = 1; i <= pagination.last_page; i++) {
        const li = document.createElement('li');
        li.className = 'page-item' + (i === pagination.current_page ? ' active' : '');
        li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
        pagContainer.appendChild(li);
    }
    // Next
    const next = document.createElement('li');
    next.className = 'page-item' + (pagination.current_page === pagination.last_page ? ' disabled' : '');
    next.innerHTML = `<a class="page-link" href="#" data-page="${pagination.current_page + 1}">&raquo;</a>`;
    pagContainer.appendChild(next);
    // Event listeners
    pagContainer.querySelectorAll('.page-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = parseInt(this.getAttribute('data-page'));
            if (!isNaN(page) && page >= 1 && page <= pagination.last_page && page !== pagination.current_page) {
                cargarMantenimientos(estado, motivo, page);
            }
        });
    });
}

function cargarMantenimientos(estado = 'todos', motivo = '', page = 1) {
    const tablaBody = document.querySelector('#tablaMantenimientos tbody');
    const filtrosEstado = document.getElementById('filtros-estado');
    const filtrosTipo = document.getElementById('filtros-tipo');
    const btnLimpiarFiltros = document.getElementById('btn-limpiar-filtros');
    let estadoSeleccionado = 'todos';
    let tipoSeleccionado = 'todos';
    let paginaActual = 1;

    function cargarMantenimientos(estado = 'todos', page = 1, tipo = 'todos') {
        estadoSeleccionado = estado;
        tipoSeleccionado = tipo;
        paginaActual = page;
        let url = `/taller/getMantenimientos?estado=${estado}&page=${page}&per_page=4`;
        if (tipo && tipo !== 'todos') {
            url += `&tipo=${tipo}`;
        }
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                tablaBody.innerHTML = '';
                if(data.mantenimientos.length === 0) {
                    tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">No hay mantenimientos para mostrar.</td></tr>`;
                    document.getElementById('paginacion-mantenimientos').innerHTML = '';
                    return;
                }
                data.mantenimientos.forEach(m => {
                    tablaBody.innerHTML += `
                        <tr>
                            <td>${m.vehiculo}</td>
                            <td>${m.taller}</td>
                            <td>${m.fechaCompleta}</td>
                            <td>${m.motivo_reserva ? (m.motivo_reserva === 'averia' ? 'Avería' : 'Mantenimiento') : ''}
${m.motivo_reserva === 'averia' && m.motivo_averia ? `<br><span class='text-muted small'>${m.motivo_averia}</span>` : ''}</td>
                            <td><span class="badge bg-${m.colorEstado} text-capitalize">${m.estado}</span></td>
                            <td>
                                <button class="btn-outline-purple" title="Editar" onclick="abrirModalEditar(${m.id})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-outline-purple" onclick="eliminarMantenimiento(${m.id})" title="Eliminar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                                <a href="/taller/factura/${m.id}" class="btn-outline-purple" title="Descargar factura" target="_blank">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    `;
                });
                renderPaginacion(data.pagination);
            } else {
                tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">Error al cargar mantenimientos.</td></tr>`;
                document.getElementById('paginacion-mantenimientos').innerHTML = '';
            }
        })
        .catch(err => {
            tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">Error al cargar mantenimientos.</td></tr>`;
            document.getElementById('paginacion-mantenimientos').innerHTML = '';
            console.error(err);
        });
    }

    function renderPaginacion(pagination) {
        const paginacionDiv = document.getElementById('paginacion-mantenimientos');
        if (pagination.last_page <= 1) {
            paginacionDiv.innerHTML = '';
            return;
        }
        let html = `<nav aria-label="Paginación"><ul id="paginacion-morada">`;
        // Botón anterior
        html += `<li${pagination.current_page === 1 ? ' data-disabled="true"' : ''}>
            <button ${pagination.current_page === 1 ? 'tabindex="-1" aria-disabled="true"' : ''} onclick="window.cargarPaginaMantenimientos(${pagination.current_page - 1})">&laquo;</button>
        </li>`;
        // Números de página
        for (let i = 1; i <= pagination.last_page; i++) {
            html += `<li${i === pagination.current_page ? ' data-active="true"' : ''}>
                <button onclick="window.cargarPaginaMantenimientos(${i})">${i}</button>
            </li>`;
        }
        // Botón siguiente
        html += `<li${pagination.current_page === pagination.last_page ? ' data-disabled="true"' : ''}>
            <button ${pagination.current_page === pagination.last_page ? 'tabindex="-1" aria-disabled="true"' : ''} onclick="window.cargarPaginaMantenimientos(${pagination.current_page + 1})">&raquo;</button>
        </li>`;
        html += `</ul></nav>`;
        paginacionDiv.innerHTML = html;
    }

    // Exponer función global para los botones de paginación
    window.cargarPaginaMantenimientos = function(page) {
        if (page < 1) return;
        cargarMantenimientos(estadoSeleccionado, page, tipoSeleccionado);
    }

    // Filtros de estado
    filtrosEstado.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const estado = event.target.dataset.estado;
            if (estado !== estadoSeleccionado) {
                // Quitar 'active' de todos los botones
                Array.from(filtrosEstado.children).forEach(btn => btn.classList.remove('active'));
                // Poner 'active' al botón actual
                event.target.classList.add('active');
                estadoSeleccionado = estado;
                cargarMantenimientos(estado, 1, tipoSeleccionado); // Reiniciar a página 1 y mantener tipo
            }
        }
    });

    // Filtros de tipo (mantenimiento/avería)
    filtrosTipo.addEventListener('click', function(event) {
        if (event.target.tagName === 'BUTTON') {
            const tipo = event.target.dataset.tipo;
            if (tipo !== tipoSeleccionado) {
                // Quitar 'active' de todos los botones
                Array.from(filtrosTipo.children).forEach(btn => btn.classList.remove('active'));
                // Poner 'active' al botón actual
                event.target.classList.add('active');
                tipoSeleccionado = tipo;
                cargarMantenimientos(estadoSeleccionado, 1, tipo); // Reiniciar a página 1 y mantener estado
            }
        }
    });

    // Botón para limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        // Resetear ambos filtros a 'todos'
        estadoSeleccionado = 'todos';
        tipoSeleccionado = 'todos';
        // Quitar 'active' de todos los botones y ponerlo en 'todos'
        Array.from(filtrosEstado.children).forEach(btn => btn.classList.remove('active'));
        filtrosEstado.querySelector('[data-estado="todos"]').classList.add('active');
        Array.from(filtrosTipo.children).forEach(btn => btn.classList.remove('active'));
        filtrosTipo.querySelector('[data-tipo="todos"]').classList.add('active');
        cargarMantenimientos('todos', 1, 'todos');
    });

    // Inicializar
    cargarMantenimientos();
});
</script>
@endsection
