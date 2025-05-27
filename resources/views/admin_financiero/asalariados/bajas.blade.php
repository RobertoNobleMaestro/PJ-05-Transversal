@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Asalariados dados de baja</h1>
            <p class="text-muted">Estos asalariados no están activos actualmente</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.asalariados.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Volver a Asalariados
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
                <h5 class="mb-0">Listado de Asalariados dados de baja</h5>
                <input type="text" id="searchInput" class="form-control form-control-sm w-25" placeholder="Buscar asalariado...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover crud-table">
                    <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Rol</th>
                            <th>Último salario</th>
                            <th>Fecha de baja</th>
                            <th>Días trabajados</th>
                            <th>Lugar</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($asalariados) > 0)
                            @foreach($asalariados as $asalariado)
                                <tr>
                                    <td>{{ $asalariado->usuario->nombre }} {{ $asalariado->usuario->apellidos }}</td>
                                    <td>
                                        @php
                                            $rolClass = '';
                                            $rolName = $asalariado->usuario->role->nombre_rol;
                                            switch(strtolower($rolName)) {
                                                case 'gestor':
                                                    $rolClass = 'bg-primary';
                                                    break;
                                                case 'mecanico':
                                                    $rolClass = 'bg-warning text-dark';
                                                    break;
                                                case 'admin_financiero':
                                                    $rolClass = 'bg-success';
                                                    break;
                                                case 'chofer':
                                                    $rolClass = 'bg-info';
                                                    break;
                                                default:
                                                    $rolClass = 'bg-secondary';
                                            }
                                        @endphp
                                        <span class="badge {{ $rolClass }}">
                                            {{ $rolName === 'admin_financiero' ? 'Admin Financiero' : ucfirst($rolName) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($asalariado->salario, 2, ',', '.') }} €</td>
                                    <td>{{ $asalariado->updated_at->format('d/m/Y') }}</td>
                                    <td>{{ $asalariado->dias_trabajados }}</td>
                                    <td>{{ $asalariado->sede ? $asalariado->sede->nombre : 'Sin sede' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <form action="{{ route('admin.asalariados.alta', $asalariado->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Dar de alta" onclick="return confirm('¿Está seguro que desea dar de alta a este asalariado? La fecha de contratación se actualizará a hoy.')">
                                                    <i class="fas fa-user-plus"></i> Dar de alta
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No hay asalariados dados de baja</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Buscador sencillo en la tabla
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('tbody tr');
        
        searchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection
