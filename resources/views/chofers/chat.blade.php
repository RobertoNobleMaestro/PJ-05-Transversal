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
    color: #4A90E2;
    text-decoration: none;
}

.izq_grupos h3 a:hover {
    color: #357ABD;
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
                    <h3>Chats: <a href="#"><i class="fa-solid fa-plus"></i></a></h3>
                </div>

                <!-- div central donde conversar -->
                <div class="central-convers">
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="{{asset('js/index-admin.js')}}"></script>
    @endsection

@endsection