@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">

<div class="add-user-container">
    <h1 class="form-title">Editar Usuario</h1>
    <form id="editUserForm" data-url="/admin/users/{{ $user->id_usuario }}">
        @csrf
        <div class="form-grid">
            <!-- Columna izquierda: 4 campos -->
            <div>
                <div class="form-group">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $user->nombre }}" required>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <small class="form-text">Dejar en blanco para mantener la contraseña actual.</small>
                </div>

                <div class="form-group">
                    <label for="id_roles" class="form-label">Rol</label>
                    <select class="form-control" id="id_roles" name="id_roles">
                        <option value="2" {{ $user->id_roles == 2 ? 'selected' : '' }}>Cliente</option>
                        <option value="3" {{ $user->id_roles == 3 ? 'selected' : '' }}>Gestor</option>
                    </select>
                </div>
            </div>
            
            <!-- Columna derecha: 4 campos -->
            <div>
                <div class="form-group">
                    <label for="DNI" class="form-label">DNI</label>
                    <input type="text" class="form-control" id="DNI" name="DNI" value="{{ $user->DNI }}" required>
                </div>

                <div class="form-group">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $user->telefono }}" required>
                </div>

                <div class="form-group">
                    <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ date('Y-m-d', strtotime($user->fecha_nacimiento)) }}" max="{{ date('Y-m-d', strtotime('-16 years')) }}" required>
                    <small class="form-text">Debes tener al menos 16 años para registrarte.</small>
                </div>

                <div class="form-group">
                    <label for="licencia_conducir" class="form-label">Licencia de Conducir</label>
                    <input type="text" class="form-control" id="licencia_conducir" name="licencia_conducir" value="{{ $user->licencia_conducir }}">
                </div>
            </div>
        </div>
        
        <!-- Dirección en ancho completo abajo -->
        <div class="form-group full-width" style="margin-bottom: 1.5rem;">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $user->direccion }}" required>
        </div>

        <div class="btn-container">
            <a href="{{ route('admin.users') }}" class="btn btn-cancel">Cancelar</a>
            <button type="button" class="btn btn-submit" onclick="updateUser({{ $user->id_usuario }})">Actualizar</button>
        </div>
    </form>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="users-index" content="{{ route('admin.users') }}">

<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-edit-user.js') }}"></script>
