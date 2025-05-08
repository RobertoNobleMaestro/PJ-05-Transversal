<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat con el Gestor</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('css/chat/style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- opcional -->
</head>
<body>
<div class="chat-container">
    <h3 class="chat-header">Chat con el Gestor</h3>

    <div id="chat-box" class="chat-box">
        @foreach($messages as $msg)
            <div class="message {{ $msg->sender_type === 'user' ? 'me' : 'other' }}">
                <p>{{ $msg->message }}</p>
                <small>{{ $msg->created_at->format('H:i d/m/Y') }}</small>
            </div>
        @endforeach
    </div>

    <form id="chat-form" class="chat-form">
        @csrf
        <input type="hidden" name="sender_type" value="user">
        <input type="hidden" name="user_id" value="{{ Auth::user()->id_usuario }}">
        <input type="hidden" name="gestor_id" value="{{ $gestorId }}">

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
    const yoEsGestor = "{{ Auth::user()->rol }}" === "gestor";

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const message = messageInput.value.trim();

        if (!message) {
            Swal.fire({
                icon: 'warning',
                title: 'Mensaje vacío',
                text: 'Escribe algo antes de enviar.',
                confirmButtonColor: '#9F17BD'
            });
            return;
        }

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
                const esMio = (yoEsGestor && msg.sender_type === 'gestor') || (!yoEsGestor && msg.sender_type === 'user');

                const div = document.createElement('div');
                div.className = 'message ' + (esMio ? 'me' : 'other');
                div.innerHTML = `<p>${msg.message}</p><small>Ahora</small>`;
                chatBox.appendChild(div);
                chatBox.scrollTop = chatBox.scrollHeight;
                messageInput.value = '';
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'No se pudo enviar el mensaje.',
                    confirmButtonColor: '#9F17BD'
                });
            }
        } catch (err) {
            console.error(err);
            Swal.fire({
                icon: 'error',
                title: 'Error de red',
                text: 'No se pudo conectar con el servidor.',
                confirmButtonColor: '#9F17BD'
            });
        }
    });
});
</script>
</body>
</html>
