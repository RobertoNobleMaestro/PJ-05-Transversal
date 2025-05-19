<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conversaciones con Usuarios</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/chat/style.css') }}">
</head>
<body>
    <div class="chat-list-container">
    <a href="{{ route('gestor.index') }}" class="btn btn-chat mb-3">
                            Volver
                        </a> 
        <h2 class="chat-list-title">Conversaciones con Usuarios</h2>

        @if($usuarios->isEmpty())
            <div class="alert alert-info">No tienes conversaciones activas aún.</div>
        @else
            <ul class="chat-list">
                @foreach($usuarios as $usuario)
                    <li class="chat-item d-flex justify-content-between align-items-center">
                        <div class="user-info">
                            <i class="fas fa-user-circle mr-2" style="font-size: 1.5rem; color: #9F17BD;"></i>
                            <span class="user-name">{{ $usuario->nombre }}</span>
                        </div>
                        <a href="{{ route('gestor.chat.conversacion', $usuario->id_usuario) }}" class="btn btn-chat">
                            Ver chat
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</body>
</html>
