@extends('layouts.auth')

@section('title', 'Registro')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container-register">
        <div class="auth-container">
            <div class="container-arriba">
                <!--contenido con el logo y fondo lila-->
                <img src="{{asset('img/logo.png')}}">
            </div>
            <div class="container-abajo">
                <!-- contenido con el formulario para registrarse -->
                <form action="{{ route('register.post') }}" method="POST" id="registerForm" enctype="multipart/form-data">
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
                        
                            <select id="opcionImagen" class="form-select mb-2">
                                <option value="archivo" selected>Seleccionar de mis imágenes</option>
                                <option value="camara">Tomar foto con cámara</option>
                            </select>
                        
                            <!-- Input tradicional de archivos -->
                            <input type="file" name="imagen" id="imagenInput" accept="image/*">
                        
                            <!-- Contenedor para la cámara -->
                            <div id="camaraContainer" style="display:none; text-align:center;">
                                <video id="videoCamara" autoplay style="width:100%; max-width:300px; border-radius:10px;"></video>
                                <br>
                                <button type="button" class="btn btn-success mt-2" id="btnCapturarFoto">Capturar</button>
                                <canvas id="canvasFoto" name="canvasFoto" style="display:none;"></canvas>
                            </div>
                        
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
                            <label for="licencia_conducir">Permiso de Conducir</label>
                            <select name="licencia_conducir" id="permiso">
                                <option value="">Selecciona una opción</option>
                                @foreach ($licencias as $licencia)
                                    <option value="{{ $licencia }}">{{ $licencia }}</option>
                                @endforeach
                            </select>
                            <span class="error_message" id="error_permiso"></span>
                        </div>

                        <div>
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password">
                            <span class="error_message" id="error_password"></span>
                        </div>

                        <div>
                            <label for="confirm_password">Confirmar Contraseña</label>
                            <input type="password" name="password_confirmation" id="confirm_password">
                            <span class="error_message" id="error_password_confirmation"></span>
                        </div>
                        

                    </div>
                    <div class="form-row submit-row">
                        <button type="submit" id="registerButton" class="btn-completar-registro">Completar Registro</button>
                    </div>
                    <a href="{{route('login')}}" class=""> Ya tienes cuenta? Inicia Sesión </a>
                </form>
            </div>
        </div>
@endsection

@section('scripts')
    <script src="{{asset('js/register.js')}}"></script>
@endsection