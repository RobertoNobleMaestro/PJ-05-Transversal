@extends('layouts.admin')

@section('title', 'Añadir Usuario')

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
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #9F17BD;
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
        margin-top: 1.5rem;
    }
    
    .submit-btn {
        background: #000000;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: #333333;
    }
    
    .cancel-btn {
        background: #9F17BD;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .cancel-btn:hover {
        background: #7E12A3;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>

<div class="add-user-container">
    <h1 class="form-title">Añadir Nuevo Usuario</h1>
    <form id="addUserForm">
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
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
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
            <button type="button" class="submit-btn" onclick="createUser()">Enviar</button>
            <a href="{{ route('admin.users') }}" class="cancel-btn">Cancelar</a>
        </div>
    </form>
</div>
@endsection



<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addUserForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
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
            if (value.length < 8) {
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
});

function createUser() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Obtener los datos del formulario
    const form = document.getElementById('addUserForm');
    const formData = new FormData(form);
    
    // Convertir FormData a objeto para enviar como JSON
    const formDataObj = {};
    formData.forEach((value, key) => {
        formDataObj[key] = value;
    });
    
    fetch('{{ route("admin.users.store") }}', {
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
            alert(data.message || 'Usuario creado exitosamente');
            window.location.href = '{{ route("admin.users") }}';
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
            alert('Error al crear usuario: ' + (data.message || 'Error desconocido'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error de conexión. Por favor, inténtalo de nuevo.');
    });
}
</script>
