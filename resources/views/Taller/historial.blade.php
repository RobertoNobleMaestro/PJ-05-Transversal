@extends('layouts.admin')

@section('title', 'Historial de Mantenimientos')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/taller-historial.css') }}">
<!-- SweetAlert2 -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet">
<style>
    /* Modal personalizado para que combine con la página */
    .modal-content {
        border-radius: 16px;
        border: 2px solid #6f42c1;
        box-shadow: 0 8px 32px rgba(111,66,193,0.09);
        background: #fff;
    }
    .modal-header {
        background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%);
        color: #fff;
        border-top-left-radius: 14px;
        border-top-right-radius: 14px;
        border-bottom: none;
    }
    .modal-title {
        font-weight: bold;
        font-size: 1.3rem;
        letter-spacing: 0.5px;
    }
    .modal-footer {
        border-top: none;
        background: #faf7ff;
        border-bottom-left-radius: 14px;
        border-bottom-right-radius: 14px;
    }
    .modal-body label {
        color: #6f42c1;
        font-weight: 500;
    }
    .modal-body input, .modal-body select {
        border-radius: 8px;
        border: 1.5px solid #e0d7f7;
        transition: border-color 0.2s;
    }
    .modal-body input:focus, .modal-body select:focus {
        border-color: #6f42c1;
        box-shadow: 0 0 0 2px #e0d7f7;
    }
    .btn-primary {
        background: linear-gradient(90deg, #6f42c1 0%, #a084e8 100%);
        border: none;
        border-radius: 8px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: background 0.2s;
    }
    .btn-primary:hover {
        background: linear-gradient(90deg, #a084e8 0%, #6f42c1 100%);
    }
    .btn-secondary {
        border-radius: 8px;
        background: #f3f0fa;
        color: #6f42c1;
        border: 1.5px solid #e0d7f7;
        font-weight: 600;
    }
    .btn-secondary:hover {
        background: #e0d7f7;
        color: #6f42c1;
    }
    /* Errores de validación en el modal */
    #errores-validacion-edicion {
        border-radius: 8px;
        border: 1.5px solid #f8d7da;
        background: #fff0f3;
        color: #c82333;
        font-size: 0.98rem;
    }
    /* Dos columnas en el modal de edición */
    #modalEditarMantenimiento .modal-body .row {
        display: flex;
        flex-wrap: wrap;
    }
    #modalEditarMantenimiento .modal-body .col-md-6 {
        flex: 0 0 50%;
        max-width: 50%;
        padding-right: 12px;
        padding-left: 12px;
    }
    #edit-piezas-list {
        max-height: 320px;
        overflow-y: auto;
        border: 1px solid #e0d7f7;
        border-radius: 8px;
        padding: 8px 6px 8px 8px;
        background: #faf7ff;
    }
    #edit-piezas-list .d-flex {
        margin-bottom: 6px;
    }
</style>
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
            <div class="mb-3">
                <div class="d-flex flex-wrap align-items-end gap-2">
                    <div id="filtros-estado" class="btn-group me-2 mb-2" role="group" aria-label="Filtros de estado">
                        <button type="button" class="btn btn-outline-purple active" data-estado="todos">Todos</button>
                        <button type="button" class="btn btn-outline-purple" data-estado="pendiente">Pendiente</button>
                        <button type="button" class="btn btn-outline-purple" data-estado="completado">Completado</button>
                        <button type="button" class="btn btn-outline-purple" data-estado="cancelado">Cancelado</button>
                    </div>
                    <div id="filtros-tipo" class="btn-group me-2 mb-2" role="group" aria-label="Filtros de tipo">
                        <button type="button" class="btn btn-outline-purple active" data-tipo="">Todos</button>
                        <button type="button" class="btn btn-outline-purple" data-tipo="mantenimiento">Mantenimiento</button>
                        <button type="button" class="btn btn-outline-purple" data-tipo="averia">Avería</button>
                    </div>
                    <div class="mb-2 d-flex align-items-stretch" style="height:38px;">
                        <button id="btn-limpiar-filtros" class="btn btn-outline-purple d-flex align-items-center justify-content-center" title="Limpiar filtros" style="height:100%;width:38px;padding:0;">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
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
                <div id="paginacion-mantenimientos" class="custom-pagination mt-3 d-flex justify-content-center"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tablaBody = document.querySelector('#tablaMantenimientos tbody');
    const filtrosEstado = document.getElementById('filtros-estado');
    const filtrosTipo = document.getElementById('filtros-tipo');
    const btnLimpiarFiltros = document.getElementById('btn-limpiar-filtros');
    let estadoSeleccionado = 'todos';
    let tipoSeleccionado = '';
    let paginaActual = 1;
    let totalPaginas = 1;

    function cargarMantenimientos(estado = 'todos', tipo = '', pagina = 1) {
        const params = new URLSearchParams({
            estado: estado,
            tipo: tipo,
            page: pagina
        });
        fetch(`/taller/getMantenimientos?${params.toString()}`, {
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
                    tablaBody.innerHTML = `<tr><td colspan="6" class="text-center">No hay mantenimientos para mostrar.</td></tr>`;
                    document.getElementById('paginacion-mantenimientos').innerHTML = '';
                    return;
                }
                data.mantenimientos.forEach(m => {
                    if (tipo && m.motivo_reserva !== tipo) {
                        return;
                    }
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
                // Paginación
                paginaActual = data.pagina_actual || 1;
                totalPaginas = data.total_paginas || 1;
                renderizarPaginacion();
            } else {
                tablaBody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar mantenimientos.</td></tr>`;
                document.getElementById('paginacion-mantenimientos').innerHTML = '';
            }
        })
        .catch(err => {
            tablaBody.innerHTML = `<tr><td colspan="6" class="text-center">Error al cargar mantenimientos.</td></tr>`;
            document.getElementById('paginacion-mantenimientos').innerHTML = '';
            console.error(err);
        });
    }

    function renderizarPaginacion() {
        const paginacion = document.getElementById('paginacion-mantenimientos');
        let html = '';
        if (totalPaginas > 1) {
            html += `<ul class="pagination custom-pagination-list mb-0">`;
            html += `<li class="page-item${paginaActual === 1 ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual - 1}">&laquo;</a></li>`;
            for (let i = 1; i <= totalPaginas; i++) {
                html += `<li class="page-item${i === paginaActual ? ' active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
            html += `<li class="page-item${paginaActual === totalPaginas ? ' disabled' : ''}"><a class="page-link" href="#" data-page="${paginaActual + 1}">&raquo;</a></li>`;
            html += `</ul>`;
        }
        paginacion.innerHTML = html;
        // Eventos
        paginacion.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const page = parseInt(this.getAttribute('data-page'));
                if (!isNaN(page) && page >= 1 && page <= totalPaginas && page !== paginaActual) {
                    cargarMantenimientos(estadoSeleccionado, tipoSeleccionado, page);
                }
            });
        });
    }

    // Inicial
    cargarMantenimientos();

    filtrosEstado.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            filtrosEstado.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            estadoSeleccionado = this.getAttribute('data-estado');
            cargarMantenimientos(estadoSeleccionado, tipoSeleccionado);
        });
    });
    filtrosTipo.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            filtrosTipo.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            tipoSeleccionado = this.getAttribute('data-tipo');
            cargarMantenimientos(estadoSeleccionado, tipoSeleccionado);
        });
    });
    btnLimpiarFiltros.addEventListener('click', function(e) {
        e.preventDefault();
        estadoSeleccionado = 'todos';
        tipoSeleccionado = '';
        filtrosEstado.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        filtrosEstado.querySelector('button[data-estado="todos"]').classList.add('active');
        filtrosTipo.querySelectorAll('button').forEach(b => b.classList.remove('active'));
        filtrosTipo.querySelector('button[data-tipo=""]').classList.add('active');
        cargarMantenimientos();
    });
});

// Función para eliminar mantenimiento
function eliminarMantenimiento(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#6f42c1',
        cancelButtonColor: '#aaa',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/taller/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'Mantenimiento eliminado correctamente.',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(function() {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', 'No se pudo eliminar el mantenimiento.', 'error');
                }
            })
            // .catch(error => {
            //     console.error('Error al eliminar:', error);
            //     Swal.fire('Error', 'Ocurrió un error.', 'error');
            // });
        }
    });
}

// --- MODAL DE EDICIÓN ---
let datosVehiculos = [];
let datosTalleres = [];

function abrirModalEditar(id) {
    fetch(`/taller/getMantenimiento/${id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const m = data.mantenimiento;
            document.getElementById('edit_id').value = m.id;
            document.getElementById('edit_vehiculo_id').value = m.vehiculo_id;
            document.getElementById('edit_taller_id').value = m.taller_id;
            document.getElementById('edit_fecha_programada').value = m.fecha_programada;
            document.getElementById('edit_hora_programada').value = m.hora_programada;
            document.getElementById('edit_estado').value = m.estado;
            document.getElementById('edit_motivo_reserva').value = m.motivo_reserva || 'mantenimiento';

            // Mostrar/ocultar y seleccionar pieza si corresponde
            if (m.motivo_reserva === 'averia') {
                document.getElementById('edit-pieza-container').style.display = '';
                // Limpiar selección previa
                document.querySelectorAll('.edit-pieza-checkbox').forEach(cb => {
                    cb.checked = false;
                    cb.closest('.d-flex').querySelector('.edit-cantidad-pieza').value = '';
                    cb.closest('.d-flex').querySelector('.edit-cantidad-pieza').disabled = true;
                });
                // Marcar seleccionadas las piezas recibidas y poner cantidad
                if (Array.isArray(m.piezas)) {
                    m.piezas.forEach(pz => {
                        const cb = document.getElementById('pieza_' + pz.id);
                        if (cb) {
                            cb.checked = true;
                            const inputCantidad = cb.closest('.d-flex').querySelector('.edit-cantidad-pieza');
                            inputCantidad.disabled = false;
                            inputCantidad.value = pz.cantidad;
                        }
                    });
                }
            } else {
                document.getElementById('edit-pieza-container').style.display = 'none';
                document.querySelectorAll('.edit-pieza-checkbox').forEach(cb => {
                    cb.checked = false;
                    cb.closest('.d-flex').querySelector('.edit-cantidad-pieza').value = '';
                    cb.closest('.d-flex').querySelector('.edit-cantidad-pieza').disabled = true;
                });
            }

            var modal = new bootstrap.Modal(document.getElementById('modalEditarMantenimiento'));
            modal.show();
        } else {
            Swal.fire('Error', 'No se pudo cargar el mantenimiento.', 'error');
        }
    });
}

// Mostrar/ocultar campo de pieza según motivo de reserva
document.getElementById('edit_motivo_reserva').addEventListener('change', function() {
    if (this.value === 'averia') {
        document.getElementById('edit-pieza-container').style.display = '';
        document.getElementById('edit_pieza_id').required = true;
    } else {
        document.getElementById('edit-pieza-container').style.display = 'none';
        document.getElementById('edit_pieza_id').required = false;
        document.getElementById('edit_pieza_id').value = '';
    }
});

// Enviar formulario de edición por AJAX
function mostrarErroresValidacion(errors) {
    let contenedor = document.getElementById('errores-validacion-edicion');
    if (!contenedor) {
        contenedor = document.createElement('div');
        contenedor.id = 'errores-validacion-edicion';
        contenedor.className = 'alert alert-danger mt-2';
        document.getElementById('formEditarMantenimiento').prepend(contenedor);
    }
    let html = '<ul class="mb-0">';
    for (const campo in errors) {
        errors[campo].forEach(msg => {
            html += `<li>${msg}</li>`;
        });
    }
    html += '</ul>';
    contenedor.innerHTML = html;
}

function limpiarErroresValidacion() {
    const contenedor = document.getElementById('errores-validacion-edicion');
    if (contenedor) contenedor.remove();
}

function enviarFormularioEdicion(event) {
    event.preventDefault();
    limpiarErroresValidacion();
    const id = document.getElementById('edit_id').value;
    const form = document.getElementById('formEditarMantenimiento');
    const formData = new FormData(form);
    fetch(`/taller/${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PUT'
        },
        body: formData
    })
    .then(async response => {
        if (response.status >= 400) {
            // Error real de red o servidor
            return;
        }
        try {
            const data = await response.json();
            if (data.success) {
                var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarMantenimiento'));
                modal.hide();
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'Mantenimiento actualizado correctamente.',
                    confirmButtonText: 'Aceptar'
                }).then(function() {
                    location.reload();
                });
            } else if (data.errors) {
                mostrarErroresValidacion(data.errors);
            }
        } catch (e) {
            // Si el status es 200 pero no es JSON, asumimos éxito
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarMantenimiento'));
            modal.hide();
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Mantenimiento actualizado correctamente.',
                confirmButtonText: 'Aceptar'
            }).then(function() {
                location.reload();
            });
        }
    })
    .catch(error => {
        // Opcional: console.error('Error de red:', error);
    });
}

// Habilitar/deshabilitar input de cantidad según selección de pieza
$(document).on('change', '.edit-pieza-checkbox', function() {
    const inputCantidad = $(this).closest('.d-flex').find('.edit-cantidad-pieza');
    if ($(this).is(':checked')) {
        inputCantidad.prop('disabled', false).val(1);
    } else {
        inputCantidad.prop('disabled', true).val('');
    }
});

</script>

<!-- MODAL DE EDICIÓN -->
<div class="modal fade" id="modalEditarMantenimiento" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalEditarLabel">Editar Mantenimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <form id="formEditarMantenimiento" onsubmit="enviarFormularioEdicion(event)">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <input type="hidden" id="edit_id" name="id">
              <div class="mb-3">
                <label for="edit_vehiculo_id" class="form-label">Vehículo</label>
                <select id="edit_vehiculo_id" name="vehiculo_id" class="form-select" required>
                  @foreach($vehiculos as $vehiculo)
                    <option value="{{ $vehiculo->id_vehiculos }}">{{ $vehiculo->modelo }} - {{ $vehiculo->placa }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label for="edit_taller_id" class="form-label">Taller</label>
                <select id="edit_taller_id" name="taller_id" class="form-select" required>
                  @foreach($talleres as $taller)
                    <option value="{{ $taller->id }}">{{ $taller->nombre }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-3">
                <label for="edit_fecha_programada" class="form-label">Fecha</label>
                <input type="date" id="edit_fecha_programada" name="fecha_programada" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="edit_hora_programada" class="form-label">Hora</label>
                <input type="time" id="edit_hora_programada" name="hora_programada" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="edit_motivo_reserva" class="form-label">Motivo de la reserva</label>
                <select id="edit_motivo_reserva" name="motivo_reserva" class="form-select" required>
                  <option value="mantenimiento">Mantenimiento</option>
                  <option value="averia">Avería</option>
                </select>
              </div>
              <div class="mb-3">
                <label for="edit_estado" class="form-label">Estado</label>
                <select id="edit_estado" name="estado" class="form-select" required>
                  <option value="pendiente">Pendiente</option>
                  <option value="completado">Completado</option>
                  <option value="cancelado">Cancelado</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3" id="edit-pieza-container" style="display:none;">
                <label for="edit_pieza_id" class="form-label">Pieza afectada</label>
                <div id="edit-piezas-list">
                  @foreach($piezas as $pieza)
                  <div class="d-flex align-items-center mb-1">
                    <input type="checkbox" class="form-check-input me-2 edit-pieza-checkbox" name="pieza_id[]" value="{{ $pieza->id }}" id="pieza_{{ $pieza->id }}">
                    <label for="pieza_{{ $pieza->id }}" class="me-2">{{ $pieza->nombre }}</label>
                    <input type="number" min="1" max="99" name="cantidad_pieza[{{ $pieza->id }}]" class="form-control form-control-sm edit-cantidad-pieza" style="width:70px;" placeholder="Cantidad" disabled>
                  </div>
                  @endforeach
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-sm btn-agendar-mantenimiento" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary btn-sm btn-agendar-mantenimiento">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

{{-- comentario para poder hacer commit --}}