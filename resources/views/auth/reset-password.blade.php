<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña - Carflow</title>
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
                        <h4 class="mb-0">Restablecer contraseña</h4>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('password.update') }}" id="reset-password-form">
                            @csrf
                            
                            <input type="hidden" name="token" value="{{ $token }}">
                            
                            <div class="form-group">
                                <label for="email">Correo electrónico</label>
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" autocomplete="email" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                <small class="form-text text-muted">Este campo no se puede modificar por razones de seguridad.</small>
                                
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <span id="email-feedback" class="invalid-feedback" style="display: none;"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="password">Nueva contraseña</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password">
                                
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                                <span id="password-feedback" class="invalid-feedback" style="display: none;"></span>
                                <small id="password-requirements" class="form-text text-danger font-weight-bold">La contraseña debe tener al menos 8 caracteres, incluir mayúsculas, minúsculas y números.</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="password-confirm">Confirmar nueva contraseña</label>
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                            </div>
                            
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-primary btn-block py-2">
                                    Restablecer contraseña
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
