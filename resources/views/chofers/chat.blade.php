@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Chat</div>

                <div class="card-body">
                    <input type="hidden" id="grupo_id" value="{{ $grupo->id }}">
                    
                    <div id="chat-messages" class="chat-messages" style="height: 400px; overflow-y: auto;">
                        <!-- Los mensajes se cargarán aquí -->
                    </div>

                    <div class="input-group mt-3">
                        <input type="text" id="mensaje" class="form-control" placeholder="Escribe tu mensaje...">
                        <button class="btn btn-primary" onclick="enviarMensaje()">Enviar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/chofers-chat.js') }}"></script>
@endpush

@push('styles')
<style>
.chat-messages {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.25rem;
}

.mensaje {
    margin-bottom: 1rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    max-width: 75%;
}

.mensaje-propio {
    background-color: #007bff;
    color: white;
    margin-left: auto;
}

.mensaje-otro {
    background-color: #e9ecef;
    color: #212529;
    margin-right: auto;
}

.mensaje .nombre {
    font-weight: bold;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.mensaje .texto {
    margin-bottom: 0.25rem;
}

.mensaje .hora {
    font-size: 0.75rem;
    opacity: 0.8;
    text-align: right;
}
</style>
@endpush
@endsection