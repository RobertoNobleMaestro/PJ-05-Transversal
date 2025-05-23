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

        #mapaPanel {
            flex: 1;
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #dee2e6;
        }

        #mapa {
            width: 100%;
            height: 100%;
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

            <div id="loading-solicitudes" class="text-center d-none">
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
                        @forelse($solicitudes as $solicitud)
                            <tr>
                                <td>{{ $solicitud->cliente->nombre }}</td>
                                <td>{{ number_format($solicitud->precio, 2) }} €</td>
                                <td>
                                    <button type="button" class="btn-action btn-info ver-mapa" 
                                            data-solicitud='{{ json_encode($solicitud) }}'>
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-aceptar"
                                            onclick="aceptarSolicitud({{ $solicitud->id }})">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button type="button" class="btn-action btn-rechazar"
                                            onclick="rechazarSolicitud({{ $solicitud->id }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center">No hay solicitudes pendientes</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Visualizador -->
    <div id="modalVisualizador">
        <div id="modalContenido">
            <div id="modalHeader">
                <h5>Detalles de la Solicitud</h5>
                <button id="cerrarModal">&times;</button>
            </div>
            <div id="modalBody">
                <div id="infoPanel"></div>
                <div id="mapaPanel">
                    <div id="mapa"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endsection

@section('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/solicitudes.js') }}"></script>
@endsection

