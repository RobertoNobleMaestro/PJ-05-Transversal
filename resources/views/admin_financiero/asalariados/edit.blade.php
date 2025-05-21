@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Asalariado</h1>
            <p class="text-muted">Modificar información de salario y parking</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('asalariados.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #9F17BD; color: white;">
                    <h5 class="mb-0">Información del empleado</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if ($usuario->foto_perfil)
                            <img src="{{ asset('storage/' . $usuario->foto_perfil) }}" alt="Foto de perfil" class="img-fluid rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        @else
                            <div class="profile-placeholder rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 120px; height: 120px; background-color: #9F17BD;">
                                <i class="fas fa-user fa-3x text-white"></i>
                            </div>
                        @endif
                        <h5>{{ $usuario->nombre }}</h5>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Rol:</span>
                            <span class="badge {{ $usuario->role->nombre == 'gestor' ? 'bg-primary' : ($usuario->role->nombre == 'mecanico' ? 'bg-warning text-dark' : 'bg-success') }}">
                                {{ $usuario->role->formatted_name }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Email:</span>
                            <span>{{ $usuario->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">DNI:</span>
                            <span>{{ $usuario->dni }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="fw-bold">Sede:</span>
                            <span>{{ $sede->nombre }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header" style="background-color: #9F17BD; color: white;">
                    <h5 class="mb-0">Datos salariales y asignación</h5>
                </div>
                <div class="card-body">
                    <div id="ajaxResponseMessages"></div>
                    <form id="editAsalariadoForm" method="POST" action="javascript:void(0)" class="p-3">
                        @csrf
                        <input type="hidden" name="asalariado_id" value="{{ $asalariado->id }}">

                        <div class="row mb-3">
                            <label for="salario" class="col-md-4 col-form-label">Salario mensual (€)</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input id="salario" type="number" step="0.01" min="0" class="form-control @error('salario') is-invalid @enderror" name="salario" value="{{ old('salario', $asalariado->salario) }}" required autofocus>
                                    <span class="input-group-text">€</span>
                                    @error('salario')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">Ingrese el salario bruto mensual</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="dia_cobro" class="col-md-4 col-form-label">Día de cobro</label>
                            <div class="col-md-8">
                                <input id="dia_cobro" type="number" min="1" max="31" class="form-control @error('dia_cobro') is-invalid @enderror" name="dia_cobro" value="{{ old('dia_cobro', $asalariado->dia_cobro) }}" required>
                                <small class="form-text text-muted">Día del mes en que se realiza el pago (1-31)</small>
                                @error('dia_cobro')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="parking_id" class="col-md-4 col-form-label">Parking asignado</label>
                            <div class="col-md-8">
                                <select id="parking_id" class="form-select @error('parking_id') is-invalid @enderror" name="parking_id" required>
                                    @foreach ($parkings as $parking)
                                        <option value="{{ $parking->id }}" {{ (old('parking_id', $asalariado->parking_id) == $parking->id) ? 'selected' : '' }}>
                                            {{ $parking->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Parking donde trabaja el empleado (solo dentro de su sede)</small>
                                @error('parking_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-4">
                                <a href="{{ route('asalariados.index') }}" class="btn btn-outline-secondary me-2">
                                    Cancelar
                                </a>
                                <button type="submit" id="saveAsalariadoBtn" class="btn text-white" style="background-color: #9F17BD;">
                                    <i class="fas fa-save me-1"></i> <span id="btnText">Guardar cambios</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #9F17BD;
        box-shadow: 0 0 0 0.25rem rgba(159, 23, 189, 0.25);
    }
    .alert-ajax {
        padding: 10px 15px;
        border-radius: 5px;
        margin-bottom: 15px;
        display: none;
    }
    .alert-ajax-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .alert-ajax-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    .is-loading {
        position: relative;
        color: transparent !important;
    }
    .is-loading::after {
        content: '';
        position: absolute;
        width: 1em;
        height: 1em;
        border: 2px solid #fff;
        border-left-color: transparent;
        border-radius: 50%;
        top: calc(50% - 0.5em);
        left: calc(50% - 0.5em);
        animation: spinAround 0.5s infinite linear;
    }
    @keyframes spinAround {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editAsalariadoForm');
        const saveBtn = document.getElementById('saveAsalariadoBtn');
        const btnText = document.getElementById('btnText');
        const responseMessages = document.getElementById('ajaxResponseMessages');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Mostrar estado de carga
            saveBtn.classList.add('is-loading');
            btnText.textContent = 'Guardando...';
            
            // Obtener datos del formulario
            const asalariadoId = form.querySelector('input[name="asalariado_id"]').value;
            const formData = new FormData(form);
            
            // Realizar petición AJAX
            fetch(`/asalariados/${asalariadoId}/update-ajax`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                // Eliminar estado de carga
                saveBtn.classList.remove('is-loading');
                btnText.textContent = 'Guardar cambios';
                
                // Limpiar mensajes previos
                responseMessages.innerHTML = '';
                
                if (data.success) {
                    // Mostrar mensaje de éxito
                    const successAlert = document.createElement('div');
                    successAlert.className = 'alert alert-success alert-dismissible fade show';
                    successAlert.innerHTML = `
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    responseMessages.appendChild(successAlert);
                    
                    // Actualizar datos en la vista sin recargar
                    // Podríamos actualizar elementos visuales si fuera necesario
                } else {
                    // Mostrar mensaje de error
                    const errorAlert = document.createElement('div');
                    errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                    errorAlert.innerHTML = `
                        ${data.message || 'Ha ocurrido un error'}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    `;
                    responseMessages.appendChild(errorAlert);
                    
                    // Si hay errores de validación, mostrarlos
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                
                                // Buscar o crear el elemento de feedback
                                let feedback = input.parentNode.querySelector('.invalid-feedback');
                                if (!feedback) {
                                    feedback = document.createElement('div');
                                    feedback.className = 'invalid-feedback';
                                    input.parentNode.appendChild(feedback);
                                }
                                
                                feedback.innerHTML = `<strong>${data.errors[field][0]}</strong>`;
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                saveBtn.classList.remove('is-loading');
                btnText.textContent = 'Guardar cambios';
                
                // Mostrar mensaje de error genérico
                responseMessages.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show">
                        Ha ocurrido un error al procesar la solicitud
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
            });
        });
        
        // Limpiar errores al cambiar inputs
        form.querySelectorAll('input, select').forEach(element => {
            element.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                const feedback = this.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.innerHTML = '';
                }
            });
        });
    });
</script>
@endsection
