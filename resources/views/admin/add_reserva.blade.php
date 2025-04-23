@extends('layouts.admin')

@section('title', 'Añadir Reserva')

@section('content')
<style>
    .form-container {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
    }
    
    .form-heading {
        color: #2d3748;
        font-size: 1.5rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }
    
    .vehicles-container {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        margin: 1rem 0;
        background-color: #f8fafc;
    }
    
    .vehicle-entry {
        padding: 1rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-bottom: 1rem;
        background-color: white;
    }
    
    .vehicle-entry:last-child {
        margin-bottom: 0;
    }
    
    .add-vehicle-btn {
        margin-top: 1rem;
    }
    
    .remove-vehicle-btn {
        color: #ef4444;
        border: none;
        background: none;
        cursor: pointer;
    }
    
    .btn-submit {
        background-color: #9F17BD;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-submit:hover {
        background-color: #8714a1;
    }
    
    .btn-cancel {
        background-color: #9CA3AF;
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 0.375rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .btn-cancel:hover {
        background-color: #6B7280;
    }
</style>

<div class="admin-container">
    <!-- Overlay para menú móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Barra lateral -->
    <div class="admin-sidebar" id="sidebar">
        <div class="sidebar-title">CARFLOW</div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}"><i class="fas fa-users"></i> Usuarios</a></li>
            <li><a href="{{ route('admin.vehiculos') }}" class="{{ request()->routeIs('admin.vehiculos*') ? 'active' : '' }}"><i class="fas fa-car"></i> Vehículos</a></li>
            <li><a href="{{ route('admin.lugares') }}" class="{{ request()->routeIs('admin.lugares*') ? 'active' : '' }}"><i class="fas fa-map-marker-alt"></i> Lugares</a></li>
            <li><a href="{{ route('admin.reservas') }}" class="{{ request()->routeIs('admin.reservas*') ? 'active' : '' }}"><i class="fas fa-calendar-alt"></i> Reservas</a></li>
        </ul>
    </div>

    <!-- Contenido principal -->
    <div class="admin-main">
        <div class="admin-header">
            <h1 class="admin-title">Añadir Reserva</h1>
            <a href="{{ route('admin.reservas') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
        </div>
        
        <div class="form-container">
            @if(session('error'))
                <div class="alert alert-danger mb-4">
                    {{ session('error') }}
                </div>
            @endif
            
            <form action="{{ route('admin.reservas.store') }}" method="POST" id="reservaForm">
                @csrf
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_reserva" class="form-label">Fecha de Reserva</label>
                            <input type="date" class="form-control @error('fecha_reserva') is-invalid @enderror" id="fecha_reserva" name="fecha_reserva" value="{{ old('fecha_reserva', date('Y-m-d')) }}" required>
                            @error('fecha_reserva')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select @error('estado') is-invalid @enderror" id="estado" name="estado" required>
                                <option value="pendiente" {{ old('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                                <option value="confirmada" {{ old('estado') == 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                <option value="cancelada" {{ old('estado') == 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                <option value="completada" {{ old('estado') == 'completada' ? 'selected' : '' }}>Completada</option>
                            </select>
                            @error('estado')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_usuario" class="form-label">Usuario</label>
                            <select class="form-select @error('id_usuario') is-invalid @enderror" id="id_usuario" name="id_usuario" required>
                                <option value="">Seleccionar Usuario</option>
                                @foreach($usuarios as $usuario)
                                    <option value="{{ $usuario->id_usuario }}" {{ old('id_usuario') == $usuario->id_usuario ? 'selected' : '' }}>
                                        {{ $usuario->nombre }} ({{ $usuario->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_usuario')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id_lugar" class="form-label">Lugar de Recogida/Entrega</label>
                            <select class="form-select @error('id_lugar') is-invalid @enderror" id="id_lugar" name="id_lugar" required>
                                <option value="">Seleccionar Lugar</option>
                                @foreach($lugares as $lugar)
                                    <option value="{{ $lugar->id_lugar }}" {{ old('id_lugar') == $lugar->id_lugar ? 'selected' : '' }}>
                                        {{ $lugar->nombre }} ({{ $lugar->direccion }})
                                    </option>
                                @endforeach
                            </select>
                            @error('id_lugar')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                
                <h4 class="form-heading">Vehículos</h4>
                <div id="vehiculos-container" class="vehicles-container">
                    <div class="vehicle-entry" id="vehicle-entry-0">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Vehículo #1</h5>
                                    <button type="button" class="remove-vehicle-btn" onclick="removeVehicle(0)" style="display: none;">
                                        <i class="fas fa-times"></i> Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="vehiculos_0" class="form-label">Seleccionar Vehículo</label>
                                <select class="form-select" id="vehiculos_0" name="vehiculos[]" required>
                                    <option value="">Seleccionar Vehículo</option>
                                    @foreach($vehiculos as $vehiculo)
                                        <option value="{{ $vehiculo->id_vehiculos }}" data-precio="{{ $vehiculo->precio_dia }}">
                                            {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->precio_dia }}€/día)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_inicio_0" class="form-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control fecha-inicio" id="fecha_inicio_0" name="fecha_inicio[]" required onchange="calcularPrecio(0)">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="fecha_fin_0" class="form-label">Fecha de Fin</label>
                                    <input type="date" class="form-control fecha-fin" id="fecha_fin_0" name="fecha_fin[]" required onchange="calcularPrecio(0)">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info precio-info" id="precio-info-0" style="display: none;">
                                    Seleccione un vehículo y fechas para ver el precio.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="button" class="btn btn-outline-primary add-vehicle-btn" onclick="addVehicle()">
                    <i class="fas fa-plus"></i> Añadir Otro Vehículo
                </button>
                
                <div class="alert alert-info mt-4" id="precio-total">
                    <strong>Precio Total Estimado:</strong> 0.00 €
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.reservas') }}" class="btn btn-cancel">Cancelar</a>
                    <button type="submit" class="btn btn-submit">Guardar Reserva</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plantilla para nuevos vehículos (oculta) -->
<template id="vehicle-template">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Vehículo #${vehicleNumber}</h5>
                <button type="button" class="remove-vehicle-btn" onclick="removeVehicle(${newIndex})">
                    <i class="fas fa-times"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-12">
            <label for="vehiculos_${newIndex}" class="form-label">Seleccionar Vehículo</label>
            <select class="form-select" id="vehiculos_${newIndex}" name="vehiculos[]" required onchange="calcularPrecio(${newIndex})">
                <option value="">Seleccionar Vehículo</option>
                @foreach($vehiculos as $vehiculo)
                    <option value="{{ $vehiculo->id_vehiculos }}" data-precio="{{ $vehiculo->precio_dia }}">
                        {{ $vehiculo->marca }} {{ $vehiculo->modelo }} ({{ $vehiculo->precio_dia }}€/día)
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fecha_inicio_${newIndex}" class="form-label">Fecha de Inicio</label>
                <input type="date" class="form-control fecha-inicio" id="fecha_inicio_${newIndex}" name="fecha_inicio[]" required onchange="calcularPrecio(${newIndex})">
            </div>
        </div>
        <div class="col-md-6">
            <div class="mb-3">
                <label for="fecha_fin_${newIndex}" class="form-label">Fecha de Fin</label>
                <input type="date" class="form-control fecha-fin" id="fecha_fin_${newIndex}" name="fecha_fin[]" required onchange="calcularPrecio(${newIndex})">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-info precio-info" id="precio-info-${newIndex}" style="display: none;">
                Seleccione un vehículo y fechas para ver el precio.
            </div>
        </div>
    </div>
</template>

@section('scripts')
<!-- Se ha movido el código JavaScript a un archivo externo -->
<script src="{{ asset('js/admin-add-reserva.js') }}"></script>
<script>
    // Configurar los valores iniciales desde el servidor
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener la plantilla para nuevos vehículos
        const template = document.getElementById('vehicle-template').innerHTML;
        const container = document.getElementById('vehiculos-container');
        
        // Pasar la plantilla al contenedor
        container.setAttribute('data-template', template);
    });
</script>
@endsection
