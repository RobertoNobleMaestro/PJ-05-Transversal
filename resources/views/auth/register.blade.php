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
            <form action="" method="" id="" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div>
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" id="nombre" required>
                        <span class="error_message" id="error_nombre"></span>
                    </div>

                    <div>
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" required>
                        <span class="error_message" id="error_email"></span>
                    </div>

                    <div>
                        <label for="dni">DNI</label>
                        <input type="text" name="dni" id="dni" required pattern="[0-9]{8}[A-Za-z]">
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
                        <input type="tel" name="telf" id="telf" required>
                        <span class="error_message" id="error_telf"></span>
                    </div>

                    <div>
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" required>
                        <span class="error_message" id="error_fecha_nacimiento"></span>
                    </div>

                    <div>
                        <label for="direccion">Dirección</label>
                        <input type="text" name="direccion" id="direccion" required>
                        <span class="error_message" id="error_direccion"></span>
                    </div>

                    <div>
                        <label for="permiso">Permiso de Conducir</label>
                        <input type="text" name="permiso" id="permiso" required>
                        <span class="error_message" id="error_permiso"></span>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection