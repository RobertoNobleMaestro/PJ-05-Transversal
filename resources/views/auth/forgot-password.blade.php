<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar contraseña - Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/PaginaPrincipal/auth/password-reset.css') }}">
    <script src="{{ asset('js/password-validation.js') }}"></script>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="logo-container">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('img/logo.png') }}" alt="Carflow Logo">
                    </a>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">Recuperar contraseña</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        <p class="mb-4">Ingresa tu dirección de correo electrónico y te enviaremos un enlace para restablecer tu contraseña.</p>
                        
                        <form method="POST" action="{{ route('password.email') }}" id="forgot-password-form">
                            @csrf
                            
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" autocomplete="email" autofocus>
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <span id="email-feedback" class="invalid-feedback" style="display: none;"></span>
                            </div>
                            
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-block py-2">
                                    Enviar enlace de recuperación
                                </button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer bg-white text-center">
                        <a href="{{ route('login') }}" class="text-decoration-none">
                            <i class="fas fa-arrow-left mr-1"></i> Volver a iniciar sesión
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
