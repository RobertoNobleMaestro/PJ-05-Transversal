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
            <a href="{{ route('gestor.index') }}" class="btn btn-outline-secondary">
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
                    text-align: center;       /* Centrar horizontalmente */
                    vertical-align: middle;   /* Centrar verticalmente */
                }
            </style>

            <div id="vehiculos-table-container">
                <table class="crud-table" id="tablaMantenimientos">
                    <thead>
                        <tr>
                            <th>Vehículo</th>
                            <th>Taller</th>
                            <th>Fecha</th>
                            <th>Fecha Completa</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Aquí se cargarán los datos dinámicamente -->
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
                            <td>${m.fecha}</td>
                            <td>${m.fechaCompleta}</td>
                            <td><span class="badge bg-${m.colorEstado} text-capitalize">${m.estado}</span></td>
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
</script>
@endsection
