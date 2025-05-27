@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Parking</h1>
            <p class="text-muted">Actualización de información del parking {{ $parking->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.parkings') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver al listado
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <!-- Formulario de edición -->
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-white">Datos del Parking</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.financiero.parkings.update', $parking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="id" class="form-label">ID</label>
                                <input type="text" class="form-control" id="id" value="{{ $parking->id }}" disabled>
                            </div>
                            <div class="col-md-8">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" value="{{ $parking->nombre }}" disabled>
                                <small class="text-muted">El nombre del parking no puede ser modificado desde aquí.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ubicacion" class="form-label">Ubicación</label>
                            <input type="text" class="form-control" id="ubicacion" value="{{ $parking->lugar->nombre ?? 'Sin ubicación' }} - {{ $parking->lugar->direccion ?? '' }}" disabled>
                            <small class="text-muted">La ubicación del parking se gestiona desde el módulo de Lugares.</small>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="metros_cuadrados" class="form-label">Metros Cuadrados</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('metros_cuadrados') is-invalid @enderror" 
                                           id="metros_cuadrados" name="metros_cuadrados" 
                                           value="{{ old('metros_cuadrados', $parking->metros_cuadrados ?? 0) }}" 
                                           min="1" step="1" required>
                                    <span class="input-group-text">m²</span>
                                </div>
                                @error('metros_cuadrados')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Área total del parking en metros cuadrados.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="precio_metro" class="form-label">Precio por Metro Cuadrado</label>
                                <div class="input-group">
                                    <input type="number" class="form-control @error('precio_metro') is-invalid @enderror" 
                                           id="precio_metro" name="precio_metro" 
                                           value="{{ old('precio_metro', $parking->precio_metro ?? 0) }}" 
                                           min="0" step="0.01" required>
                                    <span class="input-group-text">€</span>
                                </div>
                                @error('precio_metro')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Valor estimado por metro cuadrado.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Valor Total Estimado</label>
                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="text" class="form-control" id="valor_total" 
                                       value="{{ number_format(($parking->metros_cuadrados ?? 0) * ($parking->precio_metro ?? 0), 2, ',', '.') }}" 
                                       disabled>
                            </div>
                            <small class="text-muted">Este valor se calcula automáticamente multiplicando los metros cuadrados por el precio por metro.</small>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Cambios
                            </button>
                            <a href="{{ route('admin.financiero.parkings') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Información adicional y efectos de los cambios -->
        <div class="col-md-4">
            <div class="card bg-light mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Impacto de los Cambios
                    </h5>
                    <p class="card-text">
                        Los cambios en los valores de metros cuadrados y precio por metro cuadrado afectarán a:
                    </p>
                    <ul>
                        <li>Balance de activos de la empresa</li>
                        <li>Cálculos de mantenimiento mensual</li>
                        <li>Reportes financieros</li>
                        <li>Presupuestos anuales</li>
                    </ul>
                    <p class="card-text">
                        Asegúrese de que los valores introducidos sean precisos y estén respaldados por valoraciones reales.
                    </p>
                </div>
            </div>

            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-calculator me-2"></i>
                        Cálculo de Mantenimiento
                    </h5>
                    <p class="card-text">
                        El costo mensual de mantenimiento se calcula en base a los metros cuadrados:
                    </p>
                    <div class="bg-white text-dark p-3 rounded">
                        <div class="mb-2">
                            <strong>Fórmula:</strong> 200€ por parking al mes
                        </div>
                        <div>
                            <strong>Costo mensual:</strong> 200€
                        </div>
                        <div>
                            <strong>Costo anual:</strong> 2.400€
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Calcular valor total en tiempo real
    document.addEventListener('DOMContentLoaded', function() {
        const metrosCuadradosInput = document.getElementById('metros_cuadrados');
        const precioMetroInput = document.getElementById('precio_metro');
        const valorTotalInput = document.getElementById('valor_total');
        
        function calcularValorTotal() {
            const metrosCuadrados = parseFloat(metrosCuadradosInput.value) || 0;
            const precioMetro = parseFloat(precioMetroInput.value) || 0;
            const valorTotal = metrosCuadrados * precioMetro;
            
            // Formatear como moneda española
            valorTotalInput.value = new Intl.NumberFormat('es-ES', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(valorTotal);
        }
        
        metrosCuadradosInput.addEventListener('input', calcularValorTotal);
        precioMetroInput.addEventListener('input', calcularValorTotal);
    });
</script>
@endsection
