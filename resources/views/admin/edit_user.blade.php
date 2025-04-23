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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editUserForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });
});

function validateField(input) {
    let errorMessage = '';
    const value = input.value.trim();
    
    if (input.name === 'email') {
        if (!validateEmail(value)) {
            errorMessage = 'Por favor, ingrese un email válido.';
        }
    } else if (input.name === 'DNI') {
        if (!/^\d{8}[A-Z]$/.test(value)) {
            errorMessage = 'El formato del DNI es inválido. Debe terminar con una letra mayúscula.';
        } else if (!validateDNI(value)) {
            errorMessage = 'El DNI es inválido. La letra no coincide.';
        }
    } else if (input.name === 'password') {
        if (value.length < 8 && value.length > 0) { // Solo validar si hay valor (puede estar vacío en edición)
            errorMessage = 'La contraseña debe tener al menos 8 caracteres.';
        }
    } else if (input.name === 'telefono') {
        if (!/^\d{9}$/.test(value)) {
            errorMessage = 'El teléfono debe contener exactamente 9 números.';
        }
    } else if (input.name === 'direccion') {
        if (value.length < 5) {
            errorMessage = 'La dirección debe tener al menos 5 caracteres.';
        }
    } else if (input.required && value === '') {
        errorMessage = 'Este campo es obligatorio.';
    }
    
    const errorElement = input.nextElementSibling;
    if (errorElement && errorElement.classList.contains('error-message')) {
        errorElement.textContent = errorMessage;
    } else if (errorMessage) {
        const span = document.createElement('span');
        span.classList.add('error-message');
        span.style.color = 'red';
        span.textContent = errorMessage;
        input.parentNode.insertBefore(span, input.nextSibling);
    }
    
    return errorMessage === '';
}

function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validateDNI(dni) {
    const re = /^\d{8}[A-Z]$/;
    if (!re.test(dni)) {
        return false;
    }
    
    const number = parseInt(dni.slice(0, 8), 10);
    const letter = dni.charAt(8);
    const letters = "TRWAGMYFPDXBNJZSQVHLCKE";
    const calculatedLetter = letters[number % 23];
    
    return calculatedLetter === letter;
}

function updateUser(userId) {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar campos antes de enviar
    const form = document.getElementById('editUserForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            if (!validateField(input)) {
                isValid = false;
            }
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Actualizando usuario',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
    // Obtener los datos del formulario
    const formData = new FormData(form);
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
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Usuario actualizado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("admin.users") }}';
                }
            });
        } else if (data.errors) {
            // Construir mensaje de error HTML
            let errorHtml = '<ul class="text-start list-unstyled">';
            
            // Muestra errores de validación
            Object.keys(data.errors).forEach(field => {
                errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${data.errors[field][0]}</li>`;
                
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger mt-1';
                    errorDiv.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorDiv);
                }
            });
            
            errorHtml += '</ul>';
            
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
                html: errorHtml,
                confirmButtonColor: '#9F17BD'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: `<p class="lead">Error al actualizar usuario: ${data.message || 'Error desconocido'}</p>`,
                confirmButtonColor: '#9F17BD'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}

</script>
