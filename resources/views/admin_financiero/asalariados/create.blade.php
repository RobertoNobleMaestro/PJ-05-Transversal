@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Crear Nuevo Asalariado</h1>
            <p class="text-muted">Añadir un nuevo empleado a la nómina</p>
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

    <div class="card shadow">
        <div class="card-header" style="background-color: #9F17BD; color: white;">
            <h5 class="mb-0">Información del nuevo asalariado</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.asalariados.store') }}" class="p-3">
                @csrf

                <!-- Selección de tipo de usuario eliminada: Solo se permite crear nuevos usuarios/asalariados -->

                <!-- Sección para seleccionar usuario existente eliminada -->

                <!-- Sección para crear nuevo usuario -->
                <div id="seccion_usuario_nuevo">
                    <div class="row mb-3">
                        <label for="nombre" class="col-md-4 col-form-label">Nombre</label>
                        <div class="col-md-8">
                            <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}">
                            @error('nombre')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="email" class="col-md-4 col-form-label">Email</label>
                        <div class="col-md-8">
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="dni" class="col-md-4 col-form-label">DNI</label>
                        <div class="col-md-8">
                            <input id="dni" type="text" class="form-control @error('dni') is-invalid @enderror" name="dni" value="{{ old('dni') }}">
                            @error('dni')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="telefono" class="col-md-4 col-form-label">Teléfono</label>
                        <div class="col-md-8">
                            <input id="telefono" type="text" class="form-control @error('telefono') is-invalid @enderror" name="telefono" value="{{ old('telefono') }}">
                            @error('telefono')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="fecha_nacimiento" class="col-md-4 col-form-label">Fecha de Nacimiento</label>
                        <div class="col-md-8">
                            <input id="fecha_nacimiento" type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}" required>
                            @error('fecha_nacimiento')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="direccion" class="col-md-4 col-form-label">Dirección</label>
                        <div class="col-md-8">
                            <textarea id="direccion" class="form-control @error('direccion') is-invalid @enderror" name="direccion" rows="3" required>{{ old('direccion') }}</textarea>
                            @error('direccion')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="licencia_conducir" class="col-md-4 col-form-label">Licencia de Conducir</label>
                        <div class="col-md-8">
                            <input id="licencia_conducir" type="text" class="form-control @error('licencia_conducir') is-invalid @enderror" name="licencia_conducir" value="{{ old('licencia_conducir') }}">
                            @error('licencia_conducir')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password" class="col-md-4 col-form-label">Contraseña</label>
                        <div class="col-md-8">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="password-confirm" class="col-md-4 col-form-label">Confirmar Contraseña</label>
                        <div class="col-md-8">
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label for="id_roles" class="col-md-4 col-form-label">Rol</label>
                        <div class="col-md-8">
                            <select id="id_roles" class="form-select @error('id_roles') is-invalid @enderror" name="id_roles">
                                <option value="">Seleccione un rol</option>
                                @foreach ($roles as $rol)
                                    <option value="{{ $rol->id_roles }}" {{ old('id_roles') == $rol->id_roles ? 'selected' : '' }}>
                                        {{ $rol->formatted_name }} {{-- formatted_name should correctly display the role name --}}
                                    </option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Seleccione el rol para el nuevo usuario (no puede ser cliente)</small>
                            @error('id_roles')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="salario" class="col-md-4 col-form-label">Salario mensual (€)</label>
                    <div class="col-md-8">
                        <div class="input-group">
                            <input id="salario" type="number" step="0.01" min="0" class="form-control @error('salario') is-invalid @enderror" name="salario" value="{{ old('salario') }}" required>
                            <span class="input-group-text">€</span>
                        </div>
                        <small class="form-text text-muted">Ingrese el salario bruto mensual</small>
                        @error('salario')
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
                            <option value="">Seleccione un lugar</option>
                            @foreach ($lugares as $lugar)
                                <option value="{{ $lugar->id_lugar }}" {{ old('id_lugar') == $lugar->id_lugar ? 'selected' : '' }}>
                                    {{ $lugar->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Lugar donde trabajará el empleado</small>
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
                            <option value="">Primero seleccione un lugar</option>
                        </select>
                        <small class="form-text text-muted">Parking específico donde trabajará el empleado</small>
                        @error('parking_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-8 offset-md-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> La fecha de contratación se establecerá automáticamente a la fecha actual y el estado será "alta".
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.asalariados.index') }}" class="btn btn-outline-secondary me-2">
                            Cancelar
                        </a>
                        <button type="submit" class="btn text-white" style="background-color: #9F17BD;">
                            <i class="fas fa-user-plus me-1"></i> Crear Asalariado
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lógica de JS para alternar tipo de usuario eliminada, solo se permite creación.
        
        // Vinculación de lugar con parkings (AJAX)
        const lugarSelect = document.getElementById('id_lugar');
        const parkingSelect = document.getElementById('parking_id');

        function actualizarParkings(lugarId) {
            if (!lugarId) {
                parkingSelect.innerHTML = '<option value="">Primero seleccione un lugar</option>';
                parkingSelect.disabled = true;
                return;
            }

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
                        // For create form, no need to check for old('parking_id') to re-select,
                        // as validation errors would repopulate the whole form or parking_id would be empty.
                        // If old('parking_id') is present and matches, it will be selected by Blade's old() helper in the option itself if we were to build them with Blade.
                        // However, since we are building dynamically, we'd need to pass old('parking_id') here if we wanted to re-select it.
                        // For simplicity in create, we just load based on Sede.
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

        // Initial load of parkings if a Sede/Lugar is already selected (e.g., from old input)
        if (lugarSelect.value) {
            actualizarParkings(lugarSelect.value);
        }
    });
</script>
@endsection
