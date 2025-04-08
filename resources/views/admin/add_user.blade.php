@extends('layouts.admin')

@section('title', 'Añadir Usuario')

@section('content')
<div class="container mt-5">
    <h1>Añadir Nuevo Usuario</h1>
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="DNI" class="form-label">DNI</label>
            <input type="text" class="form-control" id="DNI" name="DNI" required>
        </div>
        <div class="mb-3">
            <label for="telefono" class="form-label">Teléfono</label>
            <input type="text" class="form-control" id="telefono" name="telefono" required>
        </div>
        <div class="mb-3">
            <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
        </div>
        <div class="mb-3">
            <label for="direccion" class="form-label">Dirección</label>
            <input type="text" class="form-control" id="direccion" name="direccion" required>
        </div>
        <div class="mb-3">
            <label for="licencia_conducir" class="form-label">Licencia de Conducir</label>
            <input type="text" class="form-control" id="licencia_conducir" name="licencia_conducir">
        </div>
        <div class="mb-3">
            <label for="id_roles" class="form-label">Rol</label>
            <select class="form-select" id="id_roles" name="id_roles">
                <option value="2">Cliente</option>
                <option value="3">Gestor</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Añadir Usuario</button>
    </form>
</div>
@endsection

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
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
        } else if (input.name === 'dni') {
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
</script>
