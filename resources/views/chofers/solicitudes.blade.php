@extends('layouts.admin')

@section('title', 'Solicitudes Pendientes')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/chofers/solicitudes.css') }}">

    <div class="admin-container">
        <button class="hamburger-btn" id="hamburgerBtn">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('chofers.chat') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}">
                        <i class="fa-solid fa-comments"></i> Chat
                    </a>
                </li>
                <li class="btn-volver-responsive">
                    <a href="{{ route('chofers.dashboard') }}">
                        <i class="fa-solid fa-arrow-left"></i> Volver
                    </a>
                </li>
            </ul>
        </div>

        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Solicitudes Pendientes</h1>
                <div class="header-actions">
                    <button type="button" class="btn btn-success" id="btnDisponible">
                        <i class="fas fa-check-circle"></i> Estoy Disponible
                    </button>
                    <a href="{{ route('chofers.dashboard') }}" class="btn btn-outline-danger ms-2 btn-volver-desktop">
                        <i class="fa-solid fa-backward"></i>
                    </a>
                </div>
            </div>

            <div id="loading-solicitudes" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p>Cargando solicitudes...</p>
            </div>

            <div class="table-responsive mt-4">
                <table class="crud-table">
                    <thead>
                        <tr>
                            <th>Cliente</th>
                            <th>Precio</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Las solicitudes se cargarán dinámicamente aquí -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Visualizador -->
    <div id="modalVisualizador">
        <div id="modalContenido">
            <button id="cerrarModal">&times;</button>
            <div id="mapa"></div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/chofers/solicitudes.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
@endsection

@section('scripts')
    <script>
        window.userId = {{ Auth::id() ?? 'null' }};
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/solicitudes.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hamburgerBtn = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
            }

            hamburgerBtn.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        });
    </script>
@endsection

