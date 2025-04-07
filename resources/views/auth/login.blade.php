@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container">
    <div class="container-izquierda">
        <!--contenido con los inputs de email y contraseña -->
        <h2 class="login"> Iniciar Sesión </h2>
        <form action="{{route('login.post')}}" method="POST">
            @csrf
            <label for="email">Email</label><br>
            <input type="text" name="email" id="email" placeholder="usuario@gmail.com">
            <br>
            <label for="pwd">Contraseña</label><br>
            <input type="password" name="pwd" id="pwd">
            <br>
            <br>
            <button type="button" id="login"> Iniciar Sesión </button><br>
            <a href="" class=""> Registrarse </a>
        </form>
    </div>
    <div class="container-derecha">
        <!--contenido con el logo y fondo lila-->
        <img src="{{asset('img/logo.png')}}">
    </div>
</div>
<script src="{{ asset('js/login.js') }}"></script>
@endsection