@extends('layouts.admin')

@section('title', 'Espacio Privado Chofers')

@section('content')
   
    <!-- Archivos de estilos -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chofers/styles.css') }}">
    
    <!-- Meta tag para token CSRF para las peticiones AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
                    <a href="{{ route('chofers.dashboard') }}" class="btn btn-outline-danger"><i
                            class="fa-solid fa-backward"></i>
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
                            <li class="list-group-item">
                                <a href="#"
                                    class="text-decoration-none text-dark grupo-link d-flex align-items-center justify-content-between"
                                    data-nombre="{{ $grupo->nombre }}" data-participantes="{{ $grupo->usuarios->count() }}"
                                    data-miembros='@json($grupo->usuarios->pluck("nombre"))'>
                                    <div class="d-flex align-items-center">
                                        @if($grupo->imagen_grupo)
                                            <img src="{{ asset('img/' . $grupo->imagen_grupo) }}" alt="Imagen Grupo" width="30"
                                                height="30" class="me-2 rounded-circle">
                                        @else
                                            <i class="fa-solid fa-users me-2"></i>
                                        @endif
                                        <span>{{ $grupo->nombre }}</span>
                                    </div>
                                    <i class="fa-solid fa-chevron-right text-muted"></i>
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
                <form action="{{ route('chofers.grupos.store') }}" method="POST" enctype="multipart/form-data">
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

    <!-- Modal Información del Grupo -->
    <div class="modal fade" id="infoGrupoModal" tabindex="-1" aria-labelledby="infoGrupoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content" style="border-radius: 12px;">
                <div class="modal-header" style="background-color: #8c4ae2; color: white;">
                    <h5 class="modal-title" id="infoGrupoModalLabel">Información del Grupo</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nombre:</strong> <span id="modalNombreGrupo" style="font-weight: bold;"></span></p>
                    <p><strong>Miembros:</strong></p>
                    <ul id="modalMiembrosGrupo" class="ps-3"></ul>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>




    @section('scripts')
        <script src="{{asset('js/chofers-chat.js')}}"></script>
    @endsection

@endsection