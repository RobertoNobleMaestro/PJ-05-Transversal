@extends('layouts.admin')

@section('title', 'Solicitudes Pendientes')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            border: none;
            transition: opacity 0.3s;
            cursor: pointer;
        }

        .btn-aceptar { background-color: #28a745; color: white; }
        .btn-rechazar { background-color: #dc3545; color: white; }
        .btn-info { background-color: #6f42c1; color: white; }
        .btn-action:hover { opacity: 0.8; color: white; }
        .btn-action i { font-size: 16px; }

        #modalVisualizador {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        #modalContenido {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            width: 90%;
            max-width: 1200px;
            height: 90vh;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        #modalHeader {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        #modalBody {
            flex: 1;
            display: flex;
            padding: 1rem;
            gap: 1rem;
            overflow: hidden;
        }

        #infoPanel {
            width: 300px;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            overflow-y: auto;
        }

        #mapa {
            flex: 1;
            min-width: 0;
            height: 100%;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }

        .info-item {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #dee2e6;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 0.875rem;
            color: #666;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
        }

        .info-value {
            font-size: 1rem;
            color: #333;
        }

        #cerrarModal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            color: #666;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        #cerrarModal:hover {
            background: #f8f9fa;
        }

        #loading-solicitudes {
            padding: 2rem;
            text-align: center;
        }

        #loading-solicitudes p {
            margin-top: 1rem;
            color: #666;
        }
    </style>

    <div class="admin-container">
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        <div class="admin-sidebar" id="sidebar">
            <div class="sidebar-title">CARFLOW</div>
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('chofers.chat') }}" class="{{ request()->routeIs('admin.historial*') ? 'active' : '' }}">
                        <i class="fa-solid fa-comments"></i> Chat
                    </a>
                </li>
            </ul>
        </div>

        <div class="admin-main">
            <div class="admin-header">
                <h1 class="admin-title">Solicitudes Pendientes</h1>
                <a href="{{ route('chofers.dashboard') }}" class="btn btn-outline-danger">
                    <i class="fa-solid fa-backward"></i>
                </a>
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
        <div id="modalContenido" style="display: flex; align-items: center; justify-content: center; width: 100vw; height: 100vh; background: white; border-radius: 0; box-shadow: none;">
            <button id="cerrarModal" style="position: absolute; top: 20px; right: 30px; z-index: 1000;">&times;</button>
            <div id="mapa" style="width: 90vw; height: 90vh; border-radius: 12px; border: 1px solid #dee2e6;"></div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    <script src="{{ asset('js/solicitudes.js') }}"></script>
@endsection

