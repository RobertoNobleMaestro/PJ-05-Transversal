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
            <a href="{{ route('login.google') }}" class="google-login-button">
                <img src="{{ asset('img/google-icon.svg') }}" alt="Google Icon" style="width:20px; margin-right:10px;">
                Iniciar sesión con Google
            </a>
            
        </form>
    </div>
    <div class="container-derecha">
        <!--contenido con el logo y fondo lila-->
        <img src="{{asset('img/logo.png')}}">
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

@section('scripts')
    <script src="{{asset('js/login.js')}}"></script>
@endsection 