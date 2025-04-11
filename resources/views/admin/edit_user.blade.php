@extends('layouts.admin')

@section('title', 'Editar Usuario')

@section('content')
<style>
    .add-user-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .form-title {
        color: #2d3748;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 2rem;
        text-align: left;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group.full-width {
        grid-column: span 4;
    }
    
    .form-group.half-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #4a5568;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #9F17BD;
        outline: none;
        box-shadow: 0 0 0 2px rgba(159, 23, 189, 0.1);
    }

    /* Eliminado el estilo de photo-upload */

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 2rem;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-submit {
        background-color: #000;
        color: white;
    }

    .btn-submit:hover {
        background-color: #333;
        transform: translateY(-1px);
    }

    .btn-cancel {
        background-color: #9F17BD;
        color: white;
    }

    .btn-cancel:hover {
        background-color: #8614a0;
        transform: translateY(-1px);
    }

    .text-danger {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .form-text {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>

<div class="add-user-container">
    <h1 class="form-title">Editar Usuario</h1>
    <form id="editUserForm">
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
