@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Asalariados dados de baja</h1>
            <p class="text-muted">Administra los asalariados que han sido dados de baja y podrían ser reactivados</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.asalariados.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Volver a asalariados activos
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header bg-danger text-white py-3">
            <h6 class="m-0 font-weight-bold">Lista de asalariados inactivos</h6>
        </div>
        <div class="card-body">
            @if($asalariados->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Lugar</th>
                                <th>Salario</th>
                                <th>Fecha de baja</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asalariados as $asalariado)
                                <tr>
                                    <td>{{ $asalariado->usuario->nombre ?? '' }} {{ $asalariado->usuario->apellidos ?? '' }}</td>
                                    <td>{{ $asalariado->usuario->email }}</td>
                                    <td>
                                        @php
                                            $rolName = 'Sin rol';
                                            $rolClass = 'bg-secondary';
                                            
                                            // Intentar obtener el rol desde role (singular)
                                            if (isset($asalariado->usuario->role) && isset($asalariado->usuario->role->nombre_rol)) {
                                                $rolName = $asalariado->usuario->role->nombre_rol;
                                                
                                                // Asignar clase según rol
                                                switch(strtolower($rolName)) {
                                                    case 'gestor':
                                                        $rolClass = 'bg-primary';
                                                        break;
                                                    case 'mecanico':
                                                        $rolClass = 'bg-warning text-dark';
                                                        break;
                                                    case 'admin_financiero':
                                                        $rolName = 'Admin Financiero';
                                                        $rolClass = 'bg-success';
                                                        break;
                                                    case 'chofer':
                                                        $rolClass = 'bg-info';
                                                        break;
                                                    default:
                                                        $rolClass = 'bg-secondary';
                                                }
                                            }
                                            // Si no, intentar con el ID del rol
                                            elseif (isset($asalariado->usuario->id_roles)) {
                                                $roleId = $asalariado->usuario->id_roles;
                                                switch($roleId) {
                                                    case 1: 
                                                        $rolName = 'Admin'; 
                                                        $rolClass = 'bg-dark';
                                                        break;
                                                    case 2: 
                                                        $rolName = 'Cliente'; 
                                                        $rolClass = 'bg-info';
                                                        break;
                                                    case 3: 
                                                        $rolName = 'Gestor'; 
                                                        $rolClass = 'bg-primary';
                                                        break;
                                                    case 4: 
                                                        $rolName = 'Mecánico'; 
                                                        $rolClass = 'bg-warning text-dark';
                                                        break;
                                                    case 5: 
                                                        $rolName = 'Admin Financiero'; 
                                                        $rolClass = 'bg-success';
                                                        break;
                                                    case 6: 
                                                        $rolName = 'Chofer'; 
                                                        $rolClass = 'bg-info';
                                                        break;
                                                    default: 
                                                        $rolName = 'Sin rol';
                                                        $rolClass = 'bg-secondary';
                                                }
                                            }
                                        @endphp
                                        <span class="badge {{ $rolClass }}">{{ $rolName }}</span>
                                    </td>
                                    <td>{{ $asalariado->sede->nombre ?? $asalariado->parking->nombre ?? 'No asignado' }}</td>
                                    <td>{{ number_format($asalariado->salario, 0, ',', '.') }} €</td>
                                    <td>{{ $asalariado->updated_at->format('d/m/Y') }}</td>
                                    <td>
                                        <button 
                                            class="btn btn-sm btn-success reactivar-btn" 
                                            data-id="{{ $asalariado->id }}"
                                            data-nombre="{{ $asalariado->usuario->nombre ?? '' }} {{ $asalariado->usuario->apellidos ?? '' }}"
                                        >
                                            <i class="fas fa-user-check"></i> Reactivar
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-user-slash fa-3x mb-3"></i>
                    <p>No hay asalariados dados de baja actualmente.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de confirmación para reactivar asalariado -->
<div class="modal fade" id="reactivarModal" tabindex="-1" aria-labelledby="reactivarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reactivarModalLabel">Confirmar reactivación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas reactivar a <strong id="nombreAsalariado"></strong>?</p>
                <p>Al reactivar un asalariado:</p>
                <ul>
                    <li>Se establecerá la fecha de contratación al día de hoy</li>
                    <li>El asalariado volverá a la nómina activa</li>
                    <li>El salario proporcional será calculado automáticamente</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="confirmarReactivar">
                    <i class="fas fa-user-check me-1"></i> Confirmar reactivación
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let asalariadoId;
        
        // Mostrar modal de confirmación para reactivar
        $('.reactivar-btn').on('click', function() {
            asalariadoId = $(this).data('id');
            const nombre = $(this).data('nombre');
            $('#nombreAsalariado').text(nombre);
            $('#reactivarModal').modal('show');
        });
        
        // Confirmar reactivación
        $('#confirmarReactivar').on('click', function() {
            $.ajax({
                url: `/admin-financiero/asalariados/${asalariadoId}/reactivar`,
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#reactivarModal').modal('hide');
                    // Mostrar mensaje de éxito
                    Swal.fire({
                        title: '¡Reactivado!',
                        text: 'El asalariado ha sido reactivado correctamente.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    }).then(() => {
                        // Recargar la página para mostrar los cambios
                        location.reload();
                    });
                },
                error: function(xhr) {
                    $('#reactivarModal').modal('hide');
                    // Mostrar mensaje de error
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON?.error || 'Ocurrió un error al reactivar el asalariado',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });
    });
</script>
@endsection
