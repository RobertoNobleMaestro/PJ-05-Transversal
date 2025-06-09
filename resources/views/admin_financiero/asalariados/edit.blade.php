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
        const initialParkingId = '{{ $asalariado->parking_id ?? '' }}'; // Store initial parking for re-selection

        function actualizarParkings(lugarId, selectedParkingId = null) {
            if (!lugarId) {
                parkingSelect.innerHTML = '<option value="">Seleccione primero un lugar</option>';
                parkingSelect.disabled = true;
                return;
            }

            // Add a loading state to parking select
            parkingSelect.innerHTML = '<option value="">Cargando parkings...</option>';
            parkingSelect.disabled = true;

            fetch(`{{ route('admin.asalariados.getParkingsBySede') }}?id_lugar=${lugarId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al cargar los parkings. Estado: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                parkingSelect.innerHTML = ''; // Clear loading/previous options
                let defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = data.length > 0 ? 'Seleccione un parking' : 'No hay parkings para este lugar';
                parkingSelect.appendChild(defaultOption);

                if (data.length > 0) {
                    data.forEach(parking => {
                        let option = document.createElement('option');
                        option.value = parking.id;
                        option.textContent = parking.nombre;
                        if (selectedParkingId && parking.id == selectedParkingId) {
                            option.selected = true;
                        }
                        parkingSelect.appendChild(option);
                    });
                    parkingSelect.disabled = false;
                } else {
                    parkingSelect.disabled = true;
                }
            })
            .catch(error => {
                console.error('Error fetching parkings:', error);
                parkingSelect.innerHTML = '<option value="">Error al cargar parkings</option>';
                parkingSelect.disabled = true;
            });
        }

        lugarSelect.addEventListener('change', function() {
            actualizarParkings(this.value);
        });

        // Initial load of parkings for the currently selected Sede/Lugar
        if (lugarSelect.value) {
            actualizarParkings(lugarSelect.value, initialParkingId);
        }

        // Manejo del envío del formulario de edición
        if (form) {
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                saveBtn.disabled = true;
                btnText.textContent = 'Guardando...';
                responseMessages.innerHTML = ''; // Limpiar mensajes previos

                // Obtener el ID del asalariado del atributo data-* del formulario o de un campo oculto
                // Asumiendo que el ID está en la action del form: /admin-financiero/asalariados/{id}/update
                const actionUrl = form.getAttribute('action');
                // O si tienes un input específico para el ID:
                // const asalariadoId = form.querySelector('input[name="asalariado_id"]').value; 

                const formData = new FormData(form);
                // formData.append('_method', 'POST'); // Laravel a veces necesita esto si la ruta es PUT/PATCH pero se usa POST

                fetch(actionUrl, { // Usa la URL de la action del formulario
                    method: 'POST', // La ruta está definida como POST para update
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json' // Asegurar que el servidor sepa que esperamos JSON
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(errData => {
                            throw { status: response.status, data: errData };
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        responseMessages.innerHTML = `<div class="alert alert-success">${data.message || 'Actualizado con éxito.'}</div>`;
                        // Opcional: redirigir o actualizar UI
                        // window.location.href = "{{ route('admin.asalariados.index') }}";
                    } else {
                        let errorMsg = data.message || 'Ocurrió un error.';
                        if (data.errors) {
                            errorMsg += '<ul>';
                            for (const field in data.errors) {
                                errorMsg += `<li>${data.errors[field].join(', ')}</li>`;
                            }
                            errorMsg += '</ul>';
                        }
                        responseMessages.innerHTML = `<div class="alert alert-danger">${errorMsg}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error en el fetch:', error);
                    let errorText = 'Error al actualizar el asalariado.';
                    if (error.status && error.data && error.data.message) {
                        errorText = error.data.message;
                        if (error.data.errors) {
                             errorText += '<ul>';
                            for (const field in error.data.errors) {
                                errorText += `<li>${error.data.errors[field].join(', ')}</li>`;
                            }
                            errorText += '</ul>';
                        }
                    } else if (error.message) {
                        errorText = error.message;
                    }
                    responseMessages.innerHTML = `<div class="alert alert-danger">${errorText}</div>`;
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    btnText.textContent = 'Guardar Cambios';
                });
            });
        }
        // Limpiar errores al cambiar inputs
        if (form) { // Ensure form is available
            form.querySelectorAll('input, select').forEach(element => {
                element.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                    const feedback = this.parentNode.querySelector('.invalid-feedback');
                    if (feedback) {
                        feedback.innerHTML = '';
                    }
                });
            });
        }
    }); // Cierre del DOMContentLoaded
</script>
@endsection
