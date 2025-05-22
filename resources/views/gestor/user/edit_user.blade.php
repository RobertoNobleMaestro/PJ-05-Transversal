@extends('layouts.admin')

@section('title', 'Editar Usuario Asalariado')

@section('content')
    <!-- Se han movido los estilos CSS a archivos externos -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">

    <div class="add-user-container">
        <a href="{{ route('gestor.user.index') }}" class="btn btn-cancel" style="margin-bottom: 1.5rem;"><i class="fas fa-arrow-left"></i> Volver al listado</a>
        <!-- Barra de pasos -->
        <div class="wizard-steps" style="display: flex; justify-content: center; margin-bottom: 2rem;">
            <div id="step-indicator-1" class="wizard-step active">
                <span class="wizard-circle">1</span>
                <span class="wizard-label">Datos de Usuario</span>
            </div>
            <div class="wizard-line"></div>
            <div id="step-indicator-2" class="wizard-step">
                <span class="wizard-circle">2</span>
                <span class="wizard-label">Datos de Asalariado</span>
            </div>
        </div>
        <form id="asalariadoWizardForm" data-url="/gestor/users/{{ $user->id_usuario }}">
            @csrf
            <!-- Paso 1: Datos de Usuario -->
            <div id="step-usuario">
                <fieldset>
                    <legend>Datos de Usuario</legend>
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $user->nombre }}">
                            </div>

                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                            </div>

                            <div class="form-group">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text">Dejar en blanco para mantener la contraseña actual.</small>
                            </div>

                            <div class="form-group">
                                <label for="id_roles" class="form-label">Rol</label>
                                <select class="form-control" id="id_roles" name="id_roles">
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id_roles }}" {{ $user->id_roles == $role->id_roles ? 'selected' : '' }}>{{ ucwords(str_replace('_', ' ', $role->nombre)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label for="DNI" class="form-label">DNI</label>
                                <input type="text" class="form-control" id="DNI" name="DNI" value="{{ $user->dni }}" disabled>
                            </div>

                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $user->telefono }}">
                            </div>

                            <div class="form-group">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento"
                                    value="{{ date('Y-m-d', strtotime($user->fecha_nacimiento)) }}"
                                    max="{{ date('Y-m-d', strtotime('-16 years')) }}">
                                <small class="form-text">Debes tener al menos 16 años para registrarte.</small>
                            </div>

                            <div class="form-group">
                                <label for="licencia_conducir" class="form-label">Licencia de Conducir</label>
                                <select class="form-control" id="licencia_conducir" name="licencia_conducir">
                                    <option value="">Selecciona una opción</option>
                                    @foreach($licencias as $licencia)
                                        <option value="{{ $licencia }}" {{ $user->licencia_conducir == $licencia ? 'selected' : '' }}>{{ $licencia }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Dirección en ancho completo abajo -->
                    <div class="form-group full-width" style="margin-bottom: 1.5rem;">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $user->direccion }}">
                    </div>
                </fieldset>
                <div class="btn-container">
                    <button type="button" class="btn btn-submit" id="nextToAsalariado">Siguiente</button>
                </div>
            </div>
            <!-- Paso 2: Datos de Asalariado -->
            <div id="step-asalariado" style="display:none;">
                <fieldset>
                    <legend>Datos de Asalariado</legend>
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="salario" class="form-label">Salario (€)</label>
                                <input type="number" step="0.01" class="form-control" id="salario" name="salario" value="{{ $asalariado->salario }}">
                            </div>

                            <div class="form-group">
                                <label for="dia_cobro" class="form-label">Día de cobro</label>
                                <input type="number" min="1" max="31" class="form-control" id="dia_cobro" name="dia_cobro" value="{{ $asalariado->dia_cobro }}">
                            </div>
                        </div>

                        <div>
                            <div class="form-group">
                                <label for="parking_id" class="form-label">Parking asignado</label>
                                <select class="form-control" id="parking_id" name="parking_id">
                                    <option value="">Selecciona un parking</option>
                                    @foreach($parkings as $parking)
                                        <option value="{{ $parking->id }}" {{ $asalariado->parking_id == $parking->id ? 'selected' : '' }}>{{ $parking->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="btn-container">
                    <button type="button" class="btn btn-cancel" id="backToUsuario">Anterior</button>
                    <button type="button" class="btn btn-submit" onclick="updateUser({{ $user->id_usuario }})">Actualizar</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <meta name="users-index" content="{{ route('gestor.user.index') }}">
    <script src="{{ asset('js/gestor-edit-user.js') }}"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var nextBtn = document.getElementById('nextToAsalariado');
        var backBtn = document.getElementById('backToUsuario');
        if (nextBtn) {
            nextBtn.onclick = function() {
                document.getElementById('step-usuario').style.display = 'none';
                document.getElementById('step-asalariado').style.display = 'block';
                document.getElementById('step-indicator-1').classList.remove('active');
                document.getElementById('step-indicator-2').classList.add('active');
            };
        }
        if (backBtn) {
            backBtn.onclick = function() {
                document.getElementById('step-asalariado').style.display = 'none';
                document.getElementById('step-usuario').style.display = 'block';
                document.getElementById('step-indicator-2').classList.remove('active');
                document.getElementById('step-indicator-1').classList.add('active');
            };
        }
    });
    </script>
    <style>
    .wizard-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #bbb;
        font-weight: 500;
    }
    .wizard-step.active {
        color: #222;
    }
    .wizard-circle {
        display: inline-block;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #333;
        color: #fff;
        text-align: center;
        line-height: 32px;
        font-weight: bold;
        margin-bottom: 4px;
    }
    .wizard-step.active .wizard-circle {
        background: #9F17BD;
    }
    .wizard-label {
        font-size: 15px;
    }
    .wizard-line {
        width: 80px;
        height: 2px;
        background: #eee;
        margin: 0 16px 18px 16px;
        align-self: center;
    }
    </style>
@endsection