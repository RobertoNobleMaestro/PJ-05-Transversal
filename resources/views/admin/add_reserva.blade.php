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
                <div id="vehiculos-container">
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

<script>
let vehicleCount = 1;
let precioTotal = 0;

function addVehicle() {
    const container = document.getElementById('vehiculos-container');
    const newIndex = vehicleCount;
    
    const vehicleEntry = document.createElement('div');
    vehicleEntry.className = 'vehicle-entry';
    vehicleEntry.id = `vehicle-entry-${newIndex}`;
    
    vehicleEntry.innerHTML = `
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Vehículo #${newIndex + 1}</h5>
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
    `;
    
    container.appendChild(vehicleEntry);
    vehicleCount++;
    
    // Mostrar botones de eliminar si hay más de un vehículo
    const removeButtons = document.querySelectorAll('.remove-vehicle-btn');
    if (removeButtons.length > 1) {
        removeButtons.forEach(button => {
            button.style.display = 'block';
        });
    }
}

function removeVehicle(index) {
    const vehicleEntry = document.getElementById(`vehicle-entry-${index}`);
    vehicleEntry.remove();
    
    // Recalcular precio total
    actualizarPrecioTotal();
    
    // Ocultar botones de eliminar si solo queda un vehículo
    const removeButtons = document.querySelectorAll('.remove-vehicle-btn');
    if (removeButtons.length <= 1) {
        removeButtons[0].style.display = 'none';
    }
}

function calcularPrecio(index) {
    const vehiculoSelect = document.getElementById(`vehiculos_${index}`);
    const fechaInicio = document.getElementById(`fecha_inicio_${index}`);
    const fechaFin = document.getElementById(`fecha_fin_${index}`);
    const precioInfo = document.getElementById(`precio-info-${index}`);
    
    if (vehiculoSelect.value && fechaInicio.value && fechaFin.value) {
        const precioDiario = parseFloat(vehiculoSelect.options[vehiculoSelect.selectedIndex].dataset.precio);
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        // Verificar que la fecha de fin sea posterior a la de inicio
        if (fin < inicio) {
            precioInfo.innerHTML = '<strong class="text-danger">Error: La fecha de fin debe ser posterior a la fecha de inicio.</strong>';
            precioInfo.className = 'alert alert-danger precio-info';
            precioInfo.style.display = 'block';
            return;
        }
        
        // Calcular número de días (incluyendo el día de fin)
        const diffTime = Math.abs(fin - inicio);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        // Calcular precio para este vehículo
        const precioVehiculo = precioDiario * diffDays;
        
        precioInfo.innerHTML = `<strong>Precio para este vehículo:</strong> ${precioVehiculo.toFixed(2)} € (${precioDiario} €/día x ${diffDays} días)`;
        precioInfo.className = 'alert alert-info precio-info';
        precioInfo.style.display = 'block';
        
        // Actualizar precio total
        actualizarPrecioTotal();
    } else {
        precioInfo.style.display = 'none';
    }
}

function actualizarPrecioTotal() {
    let total = 0;
    
    // Recorrer todos los vehículos
    for (let i = 0; i < vehicleCount; i++) {
        const vehiculoElement = document.getElementById(`vehiculos_${i}`);
        const fechaInicioElement = document.getElementById(`fecha_inicio_${i}`);
        const fechaFinElement = document.getElementById(`fecha_fin_${i}`);
        
        // Si este índice existe y tiene todos los valores
        if (vehiculoElement && fechaInicioElement && fechaFinElement &&
            vehiculoElement.value && fechaInicioElement.value && fechaFinElement.value) {
            
            const precioDiario = parseFloat(vehiculoElement.options[vehiculoElement.selectedIndex].dataset.precio);
            const inicio = new Date(fechaInicioElement.value);
            const fin = new Date(fechaFinElement.value);
            
            // Verificar que la fecha de fin sea posterior a la de inicio
            if (fin >= inicio) {
                const diffTime = Math.abs(fin - inicio);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                // Sumar al total
                total += precioDiario * diffDays;
            }
        }
    }
    
    // Actualizar elemento en la UI
    document.getElementById('precio-total').innerHTML = `<strong>Precio Total Estimado:</strong> ${total.toFixed(2)} €`;
    precioTotal = total;
}

// Inicializar primer vehículo con evento para calcular precio
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('vehiculos_0').addEventListener('change', function() {
        calcularPrecio(0);
    });
    
    // Establecer la fecha de hoy como valor por defecto para fecha_reserva
    document.getElementById('fecha_reserva').valueAsDate = new Date();
});
</script>
@endsection
