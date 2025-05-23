@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle"></i> Registrar Nuevo Activo
                    </h5>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.financiero.activo.store') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="nombre" class="col-md-4 col-form-label text-md-right">Nombre</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control @error('nombre') is-invalid @enderror" name="nombre" value="{{ old('nombre') }}" required autofocus>
                                @error('nombre')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="descripcion" class="col-md-4 col-form-label text-md-right">Descripción</label>
                            <div class="col-md-6">
                                <textarea id="descripcion" class="form-control @error('descripcion') is-invalid @enderror" name="descripcion" rows="3">{{ old('descripcion') }}</textarea>
                                @error('descripcion')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="categoria" class="col-md-4 col-form-label text-md-right">Categoría</label>
                            <div class="col-md-6">
                                <select id="categoria" name="categoria" class="form-control @error('categoria') is-invalid @enderror" required>
                                    <option value="">Seleccione una categoría</option>
                                    @foreach($categorias as $valor => $nombre)
                                        <option value="{{ $valor }}" {{ old('categoria') == $valor ? 'selected' : '' }}>
                                            {{ $nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categoria')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="valor" class="col-md-4 col-form-label text-md-right">Valor (€)</label>
                            <div class="col-md-6">
                                <input id="valor" type="number" step="0.01" min="0" class="form-control @error('valor') is-invalid @enderror" name="valor" value="{{ old('valor') }}" required>
                                @error('valor')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="fecha_registro" class="col-md-4 col-form-label text-md-right">Fecha de Registro</label>
                            <div class="col-md-6">
                                <input id="fecha_registro" type="date" class="form-control @error('fecha_registro') is-invalid @enderror" name="fecha_registro" value="{{ old('fecha_registro', date('Y-m-d')) }}" required>
                                @error('fecha_registro')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save"></i> Guardar Activo
                                </button>
                                <a href="{{ route('admin.financiero.balance') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
