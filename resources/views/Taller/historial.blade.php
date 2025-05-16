@extends('layouts.admin')

@section('title', 'Historial de Mantenimientos')

@section('content')
<div class="admin-container">
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('taller.index') }}" class="{{ request()->routeIs('taller.index*') ? 'active' : '' }}">
                <i class="fas fa-tools"></i> Gestión del Taller</a></li>
            <li><a href="{{ route('taller.historial') }}" class="{{ request()->routeIs('taller.historial*') ? 'active' : '' }}">
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
                <label for="filtroEstado" class="form-label">Filtrar por Estado:</label>
                <select id="filtroEstado" class="form-select" style="max-width: 300px;">
                    <option value="todos" selected>Todos</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="completado">Completado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>

            <style>
                #tablaMantenimientos th,
                #tablaMantenimientos td {
                    text-align: center;
                    vertical-align: middle;
                }
            </style>

            <div id="vehiculos-table-container">
                <table class="crud-table" id="tablaMantenimientos">
                    <thead>
                        <tr>
                            <th>Vehículo</th>
                            <th>Taller</th>
                            <th>Fecha Completa</th>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tablaBody = document.querySelector('#tablaMantenimientos tbody');
    const filtroEstado = document.getElementById('filtroEstado');

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
                            <td><span class="badge bg-${m.colorEstado} text-capitalize">${m.estado}</span></td>
                            <td>
                                <a href="/taller/${m.id}/edit" class="btn-outline-purple" title="Editar" >
                                    <i class="fas fa-edit"></i>
                                </a>
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
                tablaBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.message}</td></tr>`;
            }
        })
        .catch(err => {
            tablaBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error al cargar datos.</td></tr>`;
            console.error(err);
        });
    }

    cargarMantenimientos();

    filtroEstado.addEventListener('change', () => {
        cargarMantenimientos(filtroEstado.value);
    });
});

// Función para eliminar mantenimiento
function eliminarMantenimiento(id) {
    if (!confirm('¿Estás seguro de que deseas eliminar este mantenimiento?')) return;

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
            alert('Mantenimiento eliminado correctamente.');
            document.getElementById('filtroEstado').dispatchEvent(new Event('change'));
        } else {
            alert('Error al eliminar el mantenimiento.');
        }
    })
    .catch(error => {
        console.error('Error al eliminar:', error);
        alert('Ocurrió un error.');
    });
}
</script>
@endsection

