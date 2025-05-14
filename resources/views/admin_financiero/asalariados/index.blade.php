@extends('layouts.admin_financiero')

@section('content')
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
                        <select id="filterRol" class="form-select form-select-sm">
                            <option value="">Todos los roles</option>
                            <option value="gestor">Gestor</option>
                            <option value="mecanico">Mecánico</option>
                            <option value="admin_financiero">Admin Financiero</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <select id="filterParking" class="form-select form-select-sm">
                            <option value="">Todos los parkings</option>
                            @foreach($asalariados as $asalariado)
                                <option value="{{ $asalariado['parking'] }}">{{ $asalariado['parking'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button id="clearFilters" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i> Limpiar filtros</button>
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
                        @forelse ($asalariados as $asalariado)
                        <tr data-rol="{{ $asalariado['rol'] }}" data-parking="{{ $asalariado['parking'] }}">
                            <td>{{ $asalariado['nombre'] }}</td>
                            <td>
                                <span class="badge 
                                    @if($asalariado['rol'] == 'gestor') bg-primary 
                                    @elseif($asalariado['rol'] == 'mecanico') bg-warning text-dark
                                    @elseif($asalariado['rol'] == 'admin_financiero') bg-success
                                    @endif">
                                    {{ ucfirst($asalariado['rol']) }}
                                </span>
                            </td>
                            <td>{{ number_format($asalariado['salario'], 2, ',', '.') }} €</td>
                            <td>{{ $asalariado['dia_cobro'] }}</td>
                            <td>{{ $asalariado['parking'] }}</td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('asalariados.show', $asalariado['id']) }}" class="btn btn-sm" style="background-color: #9F17BD; color: white;">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('asalariados.edit', $asalariado['id']) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No hay asalariados registrados en esta sede</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card text-white" style="background-color: #9F17BD;">
                <div class="card-body">
                    <h5 class="card-title">Total Asalariados</h5>
                    <p class="card-text display-4">{{ count($asalariados) }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Gasto Mensual Total</h5>
                    @php
                    $totalSalarios = array_reduce($asalariados, function($carry, $item) {
                        return $carry + $item['salario'];
                    }, 0);
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
                    $avgSalario = count($asalariados) > 0 ? $totalSalarios / count($asalariados) : 0;
                    @endphp
                    <p class="card-text display-4">{{ number_format($avgSalario, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Filtros en tiempo real para la tabla de asalariados
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const filterRol = document.getElementById('filterRol');
        const filterParking = document.getElementById('filterParking');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const tableBody = document.getElementById('asalariadosTableBody');
        const rows = tableBody.querySelectorAll('tr');

        // Función para aplicar filtros
        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const selectedRol = filterRol.value.toLowerCase();
            const selectedParking = filterParking.value.toLowerCase();

            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const rol = row.getAttribute('data-rol').toLowerCase();
                const parking = row.getAttribute('data-parking').toLowerCase();

                const matchesSearch = name.includes(searchTerm);
                const matchesRol = selectedRol === '' || rol === selectedRol;
                const matchesParking = selectedParking === '' || parking === selectedParking;

                if (matchesSearch && matchesRol && matchesParking) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Eventos para aplicar filtros automáticamente
        searchInput.addEventListener('input', applyFilters);
        filterRol.addEventListener('change', applyFilters);
        filterParking.addEventListener('change', applyFilters);

        // Limpiar filtros
        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterRol.value = '';
            filterParking.value = '';
            applyFilters();
        });

        // Eliminar duplicados en el selector de parking
        const parkingOptions = {};
        Array.from(filterParking.options).forEach(option => {
            if (option.value !== '') {
                if (parkingOptions[option.value]) {
                    option.remove();
                } else {
                    parkingOptions[option.value] = true;
                }
            }
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
