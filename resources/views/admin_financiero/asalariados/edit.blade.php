@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Asalariado</h1>
            <p class="text-muted">Modificar información de salario y parking</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.asalariados.index') }}" class="btn btn-secondary">
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


        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header" style="background-color: #9F17BD; color: white;">
                    <h5 class="mb-0">Datos salariales y asignación</h5>
                </div>
                <div class="card-body">
                    <div id="ajaxResponseMessages"></div>
                    <form id="editAsalariadoForm" method="POST" action="{{ route('admin.asalariados.update', isset($asalariado) ? $asalariado->id : 0) }}" class="p-3">
                        @csrf
                        <input type="hidden" name="asalariado_id" value="{{ isset($asalariado) ? $asalariado->id : 0 }}">

                        <div class="row mb-3">
                            <label for="salario" class="col-md-4 col-form-label">Salario mensual (€)</label>
                            <div class="col-md-8">
                                <div class="input-group">
                                    <input id="salario" type="number" step="0.01" min="0" class="form-control @error('salario') is-invalid @enderror" name="salario" value="{{ old('salario', isset($asalariado) && isset($asalariado->salario) ? $asalariado->salario : 0) }}" required autofocus>
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
                            <label for="hiredate" class="col-md-4 col-form-label">Fecha de contratación</label>
                            <div class="col-md-8">
                                <input id="hiredate" type="date" class="form-control @error('hiredate') is-invalid @enderror" name="hiredate" value="{{ old('hiredate', isset($asalariado) && isset($asalariado->hiredate) && $asalariado->hiredate ? (is_object($asalariado->hiredate) ? $asalariado->hiredate->format('Y-m-d') : (is_string($asalariado->hiredate) ? \Carbon\Carbon::parse($asalariado->hiredate)->format('Y-m-d') : now()->format('Y-m-d'))) : now()->format('Y-m-d')) }}" required>
                                <small class="form-text text-muted">Fecha en que fue contratado el empleado</small>
                                <small class="form-text text-info">Nota: Todos los asalariados cobran el día 1 de cada mes</small>
                                @error('hiredate')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="id_lugar" class="col-md-4 col-form-label">Lugar asignado</label>
                            <div class="col-md-8">
                                <select id="id_lugar" class="form-select @error('id_lugar') is-invalid @enderror" name="id_lugar" required>
                                    @if(isset($lugares) && count($lugares) > 0)
                                        @foreach($lugares as $lugar)
                                            <option value="{{ $lugar->id_lugar }}" {{ (old('id_lugar', isset($asalariado) && isset($asalariado->id_lugar) ? $asalariado->id_lugar : (isset($sede) ? $sede->id_lugar : '')) == $lugar->id_lugar) ? 'selected' : '' }}>
                                                {{ $lugar->nombre }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ isset($sede) ? $sede->id_lugar : '' }}">{{ isset($sede) && isset($sede->nombre) ? $sede->nombre : 'Sede actual' }}</option>
                                    @endif
                                </select>
                                <small class="form-text text-muted">Lugar donde trabaja el empleado</small>
                                @error('id_lugar')
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
                                    @if(isset($parkings) && count($parkings) > 0)
                                        @foreach ($parkings as $parking)
                                            <option value="{{ $parking->id }}" {{ (old('parking_id', isset($asalariado) && isset($asalariado->parking_id) ? $asalariado->parking_id : null) == $parking->id) ? 'selected' : '' }}>
                                                {{ isset($parking->nombre) ? $parking->nombre : 'Parking '.$parking->id }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="">No hay parkings disponibles</option>
                                    @endif
                                </select>
                                <small class="form-text text-muted">Parking específico donde trabaja el empleado</small>
                                @error('parking_id')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 text-end mt-4">
                                <a href="{{ route('admin.asalariados.index') }}" class="btn btn-outline-secondary me-2">
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
        
        // Vinculación de lugar con parkings
        const lugarSelect = document.getElementById('id_lugar');
        const parkingSelect = document.getElementById('parking_id');
        
        // Almacenar los parkings originales agrupados por lugar
        const parkingsPorLugar = {};
        
        @if(isset($lugares) && count($lugares) > 0)
            @foreach ($lugares as $lugar)
                parkingsPorLugar[{{ $lugar->id_lugar }}] = [
                    @if(isset($parkings))
                        @foreach ($parkings->where('id_lugar', $lugar->id_lugar) as $parking)
                            {id: {{ $parking->id }}, nombre: '{{ $parking->nombre }}'},
                        @endforeach
                    @endif
                ];
            @endforeach
        @endif
        
        // Función para actualizar los parkings según el lugar seleccionado
        function actualizarParkings() {
            const lugarId = lugarSelect.value;
            const parkingsDisponibles = parkingsPorLugar[lugarId] || [];
            
            // Limpiar opciones actuales
            parkingSelect.innerHTML = '';
            
            // Añadir nuevas opciones
            parkingsDisponibles.forEach(parking => {
                const option = document.createElement('option');
                option.value = parking.id;
                option.textContent = parking.nombre;
                parkingSelect.appendChild(option);
            });
            
            // Si no hay parkings disponibles
            if (parkingsDisponibles.length === 0) {
                const option = document.createElement('option');
                option.textContent = 'No hay parkings disponibles';
                parkingSelect.appendChild(option);
            }
        }
        
        // Actualizar parkings al cambiar el lugar
        lugarSelect.addEventListener('change', actualizarParkings);
        
        // Inicializar parkings
        actualizarParkings();
            
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
