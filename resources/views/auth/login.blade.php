@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="container-izquierda">
        <!--contenido con los inputs de email y contraseña -->
        <h2 class="login"> Iniciar Sesión </h2>
        <form action="{{route('login.post')}}" method="POST" id="loginForm">
            @csrf
            <label for="email">Email</label><br>
            <input type="text" name="email" id="email" placeholder="usuario@gmail.com">
            <span class="error_message" id="error_email"></span>
            <br>
            <label for="password">Contraseña</label><br>
            <input type="password" name="password" id="password" placeholder="********">
            <span class="error_message" id="error_password"></span>
            <br>
            <br>
            <button type="submit" id="login" disabled>Iniciar Sesión</button><br>
            <a href="{{route('register')}}" class=""> Registrarse </a>
        </form>
    </div>
    <div class="container-derecha">
        <!--contenido con el logo y fondo lila-->
        <img src="{{asset('img/logo.png')}}">
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Habilitar el botón de login cuando se ingresen email y contraseña
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const loginButton = document.getElementById('login');

// Función para verificar si ambos campos tienen valores
function checkInputs() {
    if (emailInput.value.trim() !== '' && passwordInput.value.trim() !== '') {
        loginButton.disabled = false;
    } else {
        loginButton.disabled = true;
    }
}

// Agregar event listeners para los campos de entrada
emailInput.addEventListener('input', checkInputs);
passwordInput.addEventListener('input', checkInputs);

// Manejar el envío del formulario
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);

    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '¡Bienvenido!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = data.redirect;
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Error al iniciar sesión'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ha ocurrido un error al intentar iniciar sesión'
        });
    });
});
</script>
@endsection