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
                <div id="filtros-estado" class="btn-group" role="group" aria-label="Filtros de estado">
                    <button type="button" class="btn btn-outline-purple active" data-estado="todos">Todos</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="pendiente">Pendiente</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="completado">Completado</button>
                    <button type="button" class="btn btn-outline-purple" data-estado="cancelado">Cancelado</button>
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
    let estadoSeleccionado = 'todos';

    function cargarMantenimientos(estado = 'todos') {
        fetch(`/taller/getMantenimientos?estado=${estado}`, {
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
                    return;
                }
                data.mantenimientos.forEach(m => {
                    tablaBody.innerHTML += `
                        <tr>
                            <td>${m.vehiculo}</td>
                            <td>${m.taller}</td>
                            <td>${m.fechaCompleta}</td>
                            <td>${m.motivo_reserva ? (m.motivo_reserva === 'averia' ? 'Avería' : 'Mantenimiento') : ''}</td>
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
            } else {
                tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">Error al cargar mantenimientos.</td></tr>`;
            }
        })
        .catch(err => {
            tablaBody.innerHTML = `<tr><td colspan="7" class="text-center">Error al cargar mantenimientos.</td></tr>`;
            console.error(err);
        });
    }

    cargarMantenimientos();

    filtrosEstado.querySelectorAll('button').forEach(btn => {
        btn.addEventListener('click', function() {
            // Marcar botón activo
            filtrosEstado.querySelectorAll('button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            // Filtrar
            estadoSeleccionado = this.getAttribute('data-estado');
            cargarMantenimientos(estadoSeleccionado);
        });
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
                    cargarMantenimientos(estadoSeleccionado);
                    Swal.fire({
                        icon: 'success',
                        title: '¡Eliminado!',
                        text: 'Mantenimiento eliminado correctamente.',
                        timer: 1800,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', 'No se pudo eliminar el mantenimiento.', 'error');
                }
            })
            .catch(error => {
                console.error('Error al eliminar:', error);
                Swal.fire('Error', 'Ocurrió un error.', 'error');
            });
        }
    });
}

// --- MODAL DE EDICIÓN ---
let datosVehiculos = [];
let datosTalleres = [];

function abrirModalEditar(id) {
    // Obtener datos del mantenimiento
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
            // Rellenar campos del formulario
            document.getElementById('edit_id').value = m.id;
            document.getElementById('edit_vehiculo_id').value = m.vehiculo_id;
            document.getElementById('edit_taller_id').value = m.taller_id;
            document.getElementById('edit_fecha_programada').value = m.fecha_programada;
            document.getElementById('edit_hora_programada').value = m.hora_programada;
            document.getElementById('edit_estado').value = m.estado;
            // Mostrar modal
            var modal = new bootstrap.Modal(document.getElementById('modalEditarMantenimiento'));
            modal.show();
        } else {
            Swal.fire('Error', 'No se pudo cargar el mantenimiento.', 'error');
        }
    });
}

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
                cargarMantenimientos(estadoSeleccionado);
                Swal.fire({
                    icon: 'success',
                    title: '¡Actualizado!',
                    text: 'Mantenimiento actualizado correctamente.',
                    timer: 1800,
                    showConfirmButton: false
                });
            } else if (data.errors) {
                mostrarErroresValidacion(data.errors);
            }
        } catch (e) {
            // Si el status es 200 pero no es JSON, asumimos éxito
            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEditarMantenimiento'));
            modal.hide();
            cargarMantenimientos(estadoSeleccionado);
            Swal.fire({
                icon: 'success',
                title: '¡Actualizado!',
                text: 'Mantenimiento actualizado correctamente.',
                timer: 1800,
                showConfirmButton: false
            });
        }
    })
    .catch(error => {
        // Opcional: console.error('Error de red:', error);
    });
}


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
                <label for="edit_estado" class="form-label">Estado</label>
                <select id="edit_estado" name="estado" class="form-select" required>
                    <option value="pendiente">Pendiente</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </form>
    </div>
  </div>
</div>

@endsection

