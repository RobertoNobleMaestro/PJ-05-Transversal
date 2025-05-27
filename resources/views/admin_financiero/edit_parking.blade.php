@extends('layouts.admin_financiero')

@section('content')
<div class="container py-4">
    <!-- Encabezado de página -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Editar Parking</h1>
            <p class="text-muted">Actualizar los datos y la valoración del parking</p>
        </div>
        <div class="col-md-4 text-end d-flex align-items-center justify-content-end">
            <a href="{{ route('admin.financiero.parkings.index') }}" class="btn btn-outline-secondary rounded-pill">
                <i class="fas fa-arrow-left"></i> Volver a la lista
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">Datos del Parking</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.financiero.parkings.update', $parking->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="nombre">Nombre del Parking</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                id="nombre" name="nombre" value="{{ old('nombre', $parking->nombre) }}" required>
                            @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="plazas">Número de Plazas</label>
                            <input type="number" class="form-control @error('plazas') is-invalid @enderror" 
                                id="plazas" name="plazas" value="{{ old('plazas', $parking->plazas) }}" 
                                min="1" required>
                            <small class="form-text text-muted">
                                Este valor determina los metros cuadrados (25m² por plaza).
                            </small>
                            @error('plazas')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_lugar">Ubicación</label>
                            <select class="form-control @error('id_lugar') is-invalid @enderror" 
                                id="id_lugar" name="id_lugar" required>
                                <option value="">Seleccione una ubicación</option>
                                @foreach($lugares as $lugar)
                                <option value="{{ $lugar->id_lugar }}" 
                                    {{ (old('id_lugar', $parking->id_lugar) == $lugar->id_lugar) ? 'selected' : '' }}>
                                    {{ $lugar->nombre }} - {{ $lugar->direccion }}
                                </option>
                                @endforeach
                            </select>
                            @error('id_lugar')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0 text-primary">Información de Valoración</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="small font-weight-bold">Metros Cuadrados <span class="float-right">{{ number_format($parking->metros_cuadrados, 0, ',', '.') }} m²</span></h5>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ min(100, $parking->metros_cuadrados/100) }}%" 
                                aria-valuenow="{{ $parking->metros_cuadrados }}" aria-valuemin="0" aria-valuemax="10000"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="small font-weight-bold">Precio por Metro <span class="float-right">{{ number_format($parking->precio_por_metro, 2, ',', '.') }} €</span></h5>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ min(100, $parking->precio_por_metro/10) }}%" 
                                aria-valuenow="{{ $parking->precio_por_metro }}" aria-valuemin="0" aria-valuemax="1000"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <h5 class="small font-weight-bold">Valor Total <span class="float-right">{{ number_format($parking->valor_total, 2, ',', '.') }} €</span></h5>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $parking->valor_total/50000) }}%" 
                                aria-valuenow="{{ $parking->valor_total }}" aria-valuemin="0" aria-valuemax="5000000"></div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <p class="mb-0"><i class="fas fa-info-circle"></i> Al modificar el número de plazas, se recalcularán automáticamente los metros cuadrados y el valor total del parking.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
