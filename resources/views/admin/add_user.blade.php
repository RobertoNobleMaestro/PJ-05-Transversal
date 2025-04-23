@extends('layouts.admin')

@section('title', 'Añadir Usuario')

@section('content')
<!-- Se han movido los estilos CSS a archivos externos -->
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-forms.css') }}">

<div class="add-user-container">
    <!-- Se eliminó la barra lateral (sidebar) que contenía CARFLOW y los enlaces a Usuarios y Vehículos -->
    
    <!-- Contenido principal sin margen izquierdo -->
    <div>
        <h1 class="form-title">Añadir Nuevo Usuario</h1>
        <form id="addUserForm" data-url="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-grid">
                <!-- Columna izquierda: 4 campos -->
                <div>
                    <div class="form-group">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="id_roles" class="form-label">Rol</label>
                        <select class="form-control" id="id_roles" name="id_roles">
                            <option value="2">Cliente</option>
                            <option value="3">Gestor</option>
                        </select>
                    </div>
                </div>
                
                <!-- Columna derecha: 4 campos -->
                <div>
                    <div class="form-group">
                        <label for="DNI" class="form-label">DNI</label>
                        <input type="text" class="form-control" id="DNI" name="DNI" required>
                    </div>

                    <div class="form-group">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" max="{{ date('Y-m-d', strtotime('-16 years')) }}" required>
                        <small class="form-text">Debes tener al menos 16 años para registrarte.</small>
                    </div>

                    <div class="form-group">
                        <label for="licencia_conducir" class="form-label">Licencia de Conducir</label>
                        <input type="text" class="form-control" id="licencia_conducir" name="licencia_conducir">
                    </div>
                </div>
            </div>
            
            <!-- Dirección en ancho completo abajo -->
            <div class="form-group full-width" style="margin-bottom: 1.5rem;">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" class="form-control" id="direccion" name="direccion" required>
            </div>

            <div class="btn-container">
                <a href="{{ route('admin.users') }}" class="cancel-btn">Cancelar</a>
                <button type="button" class="submit-btn" onclick="createUser()">Enviar</button>
            </div>
        </form>
    </div>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="users-index" content="{{ route('admin.users') }}">

<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-add-user.js') }}"></script>
