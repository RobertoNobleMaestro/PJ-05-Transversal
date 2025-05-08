<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat con {{ $usuario->nombre }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/chat/style.css') }}">
</head>
<body>
<div class="chat-container">
    <h3 class="chat-header">Chat con {{ $usuario->nombre }}</h3>

    <div id="chat-box" class="chat-box">
        @foreach($mensajes as $msg)
            @php
                $esMio = $msg->sender_type === 'gestor';
            @endphp
            <div class="message {{ $esMio ? 'me' : 'other' }}">
                <p>{{ $msg->message }}</p>
                <small>{{ $msg->created_at->format('H:i d/m/Y') }}</small>
            </div>
        @endforeach
    </div>

    <form id="chat-form" class="chat-form">
        @csrf
        <input type="hidden" name="sender_type" value="gestor">
        <input type="hidden" name="user_id" value="{{ $id }}">
        <input type="hidden" name="gestor_id" value="{{ Auth::user()->id_usuario }}">

        <div class="input-group mt-3">
            <input type="text" name="message" id="messageInput" class="form-control" placeholder="Escribe un mensaje..." required>
            <button type="submit" class="btn btn-send">Enviar</button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('chat-form');
    const messageInput = document.getElementById('messageInput');
    const chatBox = document.getElementById('chat-box');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        try {
            const response = await fetch("{{ route('chat.send') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (response.ok && data.data) {
                const msg = data.data;

                const div = document.createElement('div');
                div.className = 'message me'; // El gestor siempre es "me" aquí
                div.innerHTML = `<p>${msg.message}</p><small>Ahora</small>`;
                chatBox.appendChild(div);
                chatBox.scrollTop = chatBox.scrollHeight;
                form.reset();
            } else {
                alert("Error al enviar mensaje");
            }
        } catch (err) {
            console.error(err);
            alert("Error de red");
        }
    });
});
</script>
</body>
</html>
