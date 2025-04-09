@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
    <div class="container-register">
        <div class="auth-container">
            <div class="container-arriba">
                <!--contenido con el logo y fondo lila-->
                <img src="{{asset('img/logo.png')}}">
            </div>
            <div class="container-abajo">
                <!-- contenido con el formulario para registrarse -->
                <form action="{{ route('register') }}" method="POST" id="registerForm" enctype="multipart/form-data">
                    @csrf
                    <h2 class="singIn"> Registrarse </h2>
                    <div class="form-row">
                        <div>
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" id="nombre">
                            <span class="error_message" id="error_nombre"></span>
                        </div>

                        <div>
                            <label for="email">Email</label>
                            <input type="email" name="email" id="email">
                            <span class="error_message" id="error_email"></span>
                        </div>

                        <div>
                            <label for="dni">DNI</label>
                            <input type="text" name="dni" id="dni">
                            <span class="error_message" id="error_dni"></span>
                        </div>

                        <div>
                            <label for="imagen">Imagen de Perfil</label>
                            <input type="file" name="imagen" id="imagen" accept="image/*">
                            <span class="error_message" id="error_imagen"></span>
                        </div>
                    </div>

                    <div class="form-row">
                        <div>
                            <label for="telf">Teléfono</label>
                            <input type="tel" name="telf" id="telf">
                            <span class="error_message" id="error_telf"></span>
                        </div>

                        <div>
                            <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                            <input type="date" name="fecha_nacimiento" id="fecha_nacimiento">
                            <span class="error_message" id="error_fecha_nacimiento"></span>
                        </div>

                        <div>
                            <label for="direccion">Dirección</label>
                            <input type="text" name="direccion" id="direccion">
                            <span class="error_message" id="error_direccion"></span>
                        </div>

                        <div>
                            <label for="permiso">Permiso de Conducir</label>
                            <select name="permiso" id="permiso">
                                <option value="">Selecciona una opción</option>
                                @foreach ($licencias as $licencia)
                                    <option value="{{ $licencia }}">{{ $licencia }}</option>
                                @endforeach
                            </select>
                            <span class="error_message" id="error_permiso"></span>
                        </div>

                    </div>
                    <div class="form-row submit-row">
                        <button type="submit" class="btn-completar-registro">Completar Registro</button>
                    </div>
                </form>
            </div>
        </div>
@endsection

@section('scripts')
    <script src="{{asset('js/register.js')}}"></script>
@endsection