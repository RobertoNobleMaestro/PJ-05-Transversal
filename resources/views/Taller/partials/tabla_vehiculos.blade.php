<table class="crud-table" id="vehiculos-table">
    <thead>
        <tr>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Kilometraje</th>
            <th>Año</th>
            <th>Sede</th>
            <th>Tipo</th>
            <th>Parking</th>
            <th>Últ. Mantenimiento</th>
            <th>Próx. Mantenimiento</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($vehiculos as $vehiculo)
            <tr>
                <td>{{ $vehiculo->marca }}</td>
                <td>{{ $vehiculo->modelo }}</td>
                <td>{{ $vehiculo->kilometraje }} kms</td>
                <td>{{ $vehiculo->año }}</td>
                <td>{{ $vehiculo->lugar->nombre ?? '-' }}</td>
                <td>{{ $vehiculo->tipo->nombre ?? '-' }}</td>
                <td>{{ $vehiculo->parking->nombre ?? '-' }}</td>
                <td>
                    {{ $vehiculo->ultima_mant_formateada ?? '-' }}
                </td>
                <td id="prox-mant-{{ $vehiculo->id_vehiculos }}">
                    {{ $vehiculo->proxima_mant_formateada ?? '-' }}
                </td>
                <td>
                    <button class="btn btn-primary btn-sm btn-agendar-mantenimiento" 
                            data-id="{{ $vehiculo->id_vehiculos }}">
                        <i class="fas fa-calendar-check"></i>
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="11">No hay vehículos que coincidan con los filtros.</td>
            </tr>
        @endforelse
    </tbody>
</table>
<div class="d-flex justify-content-center mt-3">
    {!! $vehiculos->links('Taller.pagination') !!}
</div>
