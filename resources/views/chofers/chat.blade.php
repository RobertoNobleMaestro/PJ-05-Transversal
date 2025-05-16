@extends('layouts.admin')

@section('title', 'Espacio Privado Chofers')

@section('content')
    <style>
        /* Estilo general del contenedor del chat */
        .container {
            display: flex;
            height: calc(100vh - 100px);
            background-color: #f5f5f5;
            gap: 20px;
            padding: 20px;
        }

        /* Columna izquierda - lista de chats */
        .izq_grupos {
            background-color: #fff;
            width: 30%;
            max-width: 300px;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
        }

        .izq_grupos h3 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
        }

        .izq_grupos h3 a {
            margin-left: 10px;
            font-size: 1rem;
            color: #8c4ae2;
            text-decoration: none;
        }

        .izq_grupos h3 a:hover {
            color: #551475;
        }

        /* Columna central - conversación */
        .central-convers {
            flex: 1;
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Opcional: animación o transición suave */
        .central-convers,
        .izq_grupos {
            transition: all 0.3s ease;
        }

        /* Botón flotante (si decides usarlo también) */
        .floating-btn {
            position: absolute;
            right: 20px;
            bottom: 20px;
            background-color: #4A90E2;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .central-convers {
            display: flex;
            flex-direction: column;
            height: 100%;
        }
    </style>
    <!-- Se han movido los estilos CSS a un archivo externo -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">

    <!-- Botón de hamburguesa para menú móvil -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Overlay para cerrar menú al hacer clic fuera -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-container">
        <!-- Barra lateral lila -->
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li><a href="" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i
                            class="fa-solid fa-car"></i> Solicitudes</a></li>
            </ul>
        </div>

        <!-- Contenido principal -->
        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Chats de: {{ auth()->user()->nombre }} </h1>
                <div class="admin-welcome">
                    <a href="{{ route('logout') }}" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i>
                    </a>
                </div>
            </div>
            <div class="container">
                <!-- div lateral para los grupos del usuario -->
                <div class="izq_grupos">
                    <h3>Chats:
                        <a href="#" data-bs-toggle="modal" data-bs-target="#crearGrupoModal">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </h3>

                    <ul class="list-group">
                        @forelse ($grupos as $grupo)
                            <li class="list-group-item d-flex align-items-center">
                                @if($grupo->imagen_grupo)
                                    <img src="{{ asset('img/' . $grupo->imagen_grupo) }}" alt="Imagen Grupo" width="30" height="30"
                                        class="me-2 rounded-circle">
                                @else
                                    <i class="fa-solid fa-users me-2"></i>
                                @endif
                                <a href="#" class="text-decoration-none text-dark grupo-link" data-nombre="{{ $grupo->nombre }}"
                                    data-participantes="{{ $grupo->usuarios->count() }}">
                                    {{ $grupo->nombre }} <i class="fa-solid fa-chevron-right ms-1"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item">No perteneces a ningún grupo aún.</li>
                        @endforelse

                    </ul>


                </div>

                <!-- div central donde conversar -->
                <div class="central-convers">
                    <div id="contenidoGrupo"></div>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal para crear grupo -->
    <div class="modal fade" id="crearGrupoModal" tabindex="-1" aria-labelledby="crearGrupoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('grupos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="crearGrupoModalLabel">Crear nuevo grupo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del grupo</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="mb-3"> <label class="form-label">Seleccionar usuarios del grupo</label>
                            <div class="form-check">
                                @foreach ($choferesCompaneros as $chofer)
                                    <div>
                                        <input class="form-check-input" type="checkbox" name="usuarios[]"
                                            value="{{ $chofer->id_usuario }}" id="chofer{{ $chofer->id_usuario }}">
                                        <label class="form-check-label" for="chofer{{ $chofer->id_usuario }}">
                                            {{ $chofer->nombre }} ({{ $chofer->email }})
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="imagen_grupo" class="form-label">Imagen del grupo (opcional)</label>
                            <input type="file" name="imagen_grupo" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Crear grupo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    @section('scripts')
        <script src="{{asset('js/chofers-chat.js')}}"></script>
    @endsection

@endsection