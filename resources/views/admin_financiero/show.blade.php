@extends('layouts.auth')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1>Detalles del Asalariado</h1>
            <p class="text-muted">Información completa de {{ $asalariado->usuario->nombre }}</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.financiero.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Volver
            </a>
            <a href="{{ route('admin.financiero.edit', $asalariado->id) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Información Personal</h5>
            <span class="badge bg-light text-dark">{{ $asalariado->usuario->role->nombre_rol }}</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2 text-center mb-3">
                    @if($asalariado->usuario->foto_perfil)
                        <img src="{{ asset('img/' . $asalariado->usuario->foto_perfil) }}" alt="Foto de perfil" 
                             class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <img src="{{ asset('img/default.png') }}" alt="Foto de perfil por defecto" 
                             class="img-fluid rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                    @endif
                </div>
                <div class="col-md-5">
                    <p><strong>Nombre:</strong> {{ $asalariado->usuario->nombre }}</p>
                    <p><strong>Email:</strong> {{ $asalariado->usuario->email }}</p>
                    <p><strong>Teléfono:</strong> {{ $asalariado->usuario->telefono }}</p>
                    <p><strong>DNI:</strong> {{ $asalariado->usuario->dni }}</p>
                </div>
                <div class="col-md-5">
                    <p><strong>Dirección:</strong> {{ $asalariado->usuario->direccion }}</p>
                    <p><strong>Fecha de nacimiento:</strong> {{ $asalariado->usuario->fecha_nacimiento ? $asalariado->usuario->fecha_nacimiento->format('d/m/Y') : 'No disponible' }}</p>
                    <p><strong>Se unió a Carflow:</strong> {{ $asalariado->usuario->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Información Salarial</h5>
                </div>
                <div class="card-body">
                    <p><strong>Salario mensual:</strong> <span class="text-success">{{ number_format($asalariado->salario, 2, ',', '.') }} €</span></p>
                    <p><strong>Día de cobro:</strong> Día {{ $asalariado->dia_cobro }} de cada mes</p>
                    <p><strong>Próximo pago:</strong>
                        @php
                            $now = new DateTime();
                            $payday = new DateTime($now->format('Y-m-') . $asalariado->dia_cobro);
                            if ($payday < $now) {
                                $payday->modify('+1 month');
                            }
                            $diff = $now->diff($payday);
                        @endphp
                        {{ $payday->format('d/m/Y') }} (en {{ $diff->days }} días)
                    </p>
                    <p><strong>Salario anual:</strong> {{ number_format($asalariado->salario * 12, 2, ',', '.') }} €</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Asignación</h5>
                </div>
                <div class="card-body">
                    <p><strong>Sede:</strong> {{ $asalariado->parking->lugar->nombre }}</p>
                    <p><strong>Parking asignado:</strong> {{ $asalariado->parking->nombre }}</p>
                    <p><strong>Dirección del parking:</strong> {{ $asalariado->parking->lugar->direccion }}</p>
                    <p><strong>Plazas disponibles:</strong> {{ $asalariado->parking->plazas }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
