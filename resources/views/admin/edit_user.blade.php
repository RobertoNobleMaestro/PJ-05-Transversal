<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
<div class="container mt-5">
    <h1>Editar Usuario</h1>
    <form id="editUserForm">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="{{ $user->nombre }}" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password">
            <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual.</small>
        </div>
        <div class="mb-3">
            <label for="DNI" class="form-label">DNI</label>
            <input type="text" class="form-control" id="DNI" name="DNI" value="{{ $user->DNI }}" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" value="{{ $user->telefono }}" required>
        </div>
        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="{{ $user->fecha_nacimiento }}" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" value="{{ $user->direccion }}" required>
        </div>
        <div class="mb-3">
            <label for="licencia_conducir" class="form-label">Licencia de Conducir</label>
            <input type="text" class="form-control" id="licencia_conducir" name="licencia_conducir" value="{{ $user->licencia_conducir }}">
        </div>
        <div class="mb-3">
            <label for="id_roles" class="form-label">Rol</label>
            <select class="form-select" id="id_roles" name="id_roles">
                <option value="2" {{ $user->id_roles == 2 ? 'selected' : '' }}>Cliente</option>
                <option value="3" {{ $user->id_roles == 3 ? 'selected' : '' }}>Gestor</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" onclick="updateUser({{ $user->id_usuario }})">Actualizar Usuario</button>
    </form>
</div>
@endsection

<script>

function updateUser(userId) {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Obtener los datos del formulario
    const form = document.getElementById('editUserForm');
    const formData = new FormData(form);
    
    // Convertir FormData a objeto para enviar como JSON
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    
    fetch(`/admin/users/${userId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json'
        },
        body: JSON.stringify(formDataObj)
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert(data.message);
            window.location.href = '{{ route('admin.users') }}';
        } else if (data.errors) {
            // Muestra errores de validación
            Object.keys(data.errors).forEach(field => {
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger mt-1';
                    errorDiv.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorDiv);
                }
            });
        } else {
            alert('Error al actualizar: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión. Por favor, inténtalo de nuevo.');
    });
}

// Versión anterior eliminada
</script>
