<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $vehiculo->marca }} {{ $vehiculo->modelo }} | Carflow</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Vehiculos/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

@include('layouts.navbar')
<div class="breadcrumb-container">
    <div class="container">
        <small>Inicio > Alquiler vehiculos > {{ $vehiculo->tipo->nombre }} > {{ $vehiculo->marca }} > {{ $vehiculo->modelo }}</small>
    </div>
</div>


<div class="container vehiculo-detail-section">
    <div class="row">
    <div style="padding-bottom: 30px;">
        <a href="{{ asset('home') }}" class="btn-volver">
        <i class="fas fa-arrow-left me-2"></i> Volver
        </a>
    </div>
        <div class="col-md-6">
            <div class="imagen-box text-center">
            @foreach($imagenes as $imagen)
                <img src="{{ asset('img/vehiculos/' . $imagen->nombre_archivo) }}" alt="Imagen del vehículo" class="img-fluid">
            @endforeach

            </div>
        </div>
        <div class="col-md-6">
            <p class="text-muted">
                Publicado: {{ $vehiculo->created_at->format('d/m/Y H:i') }} | 
                Modificado: {{ $vehiculo->updated_at->format('d/m/Y H:i') }}
            </p>
            <h2 class="d-flex justify-content-between">
                {{ $vehiculo->marca }} {{ $vehiculo->modelo }}
                <span class="h4 text-success">€{{ number_format( $vehiculo->precio_dia, 2, ',', '.') }}</span>
            </h2>
            <p>{{ $vehiculo->descripcion }}</p>

            <!-- Características en 4 filas de 2 columnas -->
            <div class="caracteristicas-box">
                <div class="row caracteristicas">
                    <div class="col-md-6"><i class="fas fa-cogs"></i> Transmisión: {{ $vehiculo->caracteristicas->transmision }}</div>
                    <div class="col-md-6"><i class="fas fa-car"></i> Tipo: {{ $vehiculo->tipo->nombre }}</div>

                    <div class="col-md-6"><i class="fas fa-tachometer-alt"></i> Kilometraje: {{ number_format($vehiculo->kilometraje, 0, ',', '.') }} km</div>
                    <div class="col-md-6"><i class="fas fa-map-marker-alt"></i> Ubicación: {{ $vehiculo->lugar->nombre }}</div>

                    <div class="col-md-6"><i class="fas fa-snowflake"></i> Aire acondicionado: {{ $vehiculo->caracteristicas->aire_acondicionado ? 'Sí' : 'No' }}</div>
                    <div class="col-md-6"><i class="fas fa-sun"></i> Techo solar: {{ $vehiculo->caracteristicas->techo ? 'Sí' : 'No' }}</div>

                    <div class="col-md-6"><i class="fas fa-suitcase"></i> Maletero: {{ $vehiculo->caracteristicas->capacidad_maletero }} L</div>
                    <div class="col-md-6"><i class="fas fa-shield-alt"></i> Seguro incluido: {{ $vehiculo->seguro_incluido ? 'Sí' : 'No' }}</div>
                </div>
            </div>
            <button id="btnAbrirFormulario" class="btn-volver" style="border:none;">
                <i class="fas fa-comment"></i> Dejar una valoración
            </button>
        </div>
    </div>

    <hr>
    <h4 class="mt-5">CALENDARIO DE RESERVAS</h4>
<div id="contador-dias"></div>
<div id="calendario-reservas" class="mb-5"></div>

<!-- FullCalendar -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendario-reservas');
    const contadorDias = document.getElementById('contador-dias');
    const precioDia = {{ $vehiculo->precio_dia }};
    let reservas = [];

    let fechaInicioManual = null;
    let fechaFinManual = null;

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        height: 'auto',
        contentHeight: 'auto',
        selectable: false,

        events: {
            url: `/vehiculos/{{ $vehiculo->id_vehiculos }}/reservas`,
            failure: () => Swal.fire('Error', 'No se pudieron cargar las reservas.', 'error'),
            success: (data) => {
                reservas = data;
            }
        },

        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },

        dateClick: function (info) {
            const clickedDateStr = info.dateStr;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const clickedDate = new Date(clickedDateStr);
            clickedDate.setHours(0, 0, 0, 0);

            if (clickedDate < today) return;

            // Primer clic: seleccionar fecha inicio
            if (!fechaInicioManual) {
                fechaInicioManual = clickedDateStr;
                fechaFinManual = null;
                limpiarSeleccionVisual();
                pintarDia(clickedDateStr);
                contadorDias.textContent = 'Selecciona la fecha de fin';
                contadorDias.classList.add('visible');
                return;
            }

            // Segundo clic: seleccionar fecha fin
            fechaFinManual = clickedDateStr;

            // Reordenar si clicó al revés
            if (new Date(fechaFinManual) < new Date(fechaInicioManual)) {
                [fechaInicioManual, fechaFinManual] = [fechaFinManual, fechaInicioManual];
            }

            const rangoFechasStr = obtenerRangoFechasStr(fechaInicioManual, fechaFinManual);

            // Validar fechas bloqueadas por reservas existentes
            for (let dia of rangoFechasStr) {
                for (let reserva of reservas) {
                    const resInicio = new Date(reserva.start);
                    const resFin = new Date(reserva.end);
                    const diaFecha = new Date(dia);
                    if (diaFecha >= resInicio && diaFecha < resFin) {
                        Swal.fire('Rango no disponible', 'Algunas fechas ya están reservadas.', 'warning');
                        limpiarSeleccionVisual();
                        fechaInicioManual = null;
                        fechaFinManual = null;
                        contadorDias.classList.remove('visible');
                        return;
                    }
                }
            }

            // Pintar visualmente el rango
            limpiarSeleccionVisual();
            rangoFechasStr.forEach(pintarDia);

            const totalDias = rangoFechasStr.length;
            const precioTotal = (totalDias * precioDia).toFixed(2);
            contadorDias.textContent = `Has seleccionado ${totalDias} día${totalDias > 1 ? 's' : ''}`;
            contadorDias.classList.add('visible');

            // Rellenar modal
            document.getElementById('modal-fechas').innerText = `${fechaInicioManual} - ${fechaFinManual}`;
            document.getElementById('modal-dias').innerText = `${totalDias} día${totalDias > 1 ? 's' : ''}`;
            document.getElementById('modal-precio-dia').innerText = precioDia.toFixed(2);
            document.getElementById('modal-total').innerText = precioTotal;

            // Abrir modal
            $('#reservaModal').modal('show');

            // Evento click botón confirmar
            document.getElementById('btnConfirmarReserva').onclick = function () {
                fetch('/reservas', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id_vehiculos: {{ $vehiculo->id_vehiculos }},
                        fecha_ini: fechaInicioManual,
                        fecha_final: fechaFinManual
                    })
                })
                .then(res => res.json())
                .then(() => {
                    $('#reservaModal').modal('hide');
                    Swal.fire('¡Reservado!', 'La reserva fue creada correctamente.', 'success');
                    calendar.refetchEvents();
                    limpiarSeleccionVisual();
                    fechaInicioManual = null;
                    fechaFinManual = null;
                    contadorDias.classList.remove('visible');
                })
                .catch(() => {
                    Swal.fire('Error', 'No se pudo realizar la reserva.', 'error');
                });
            };
        }
    });

    calendar.render();

    $('#reservaModal').on('hidden.bs.modal', function () {
    // Siempre limpiar visual si se cierra el modal
    limpiarSeleccionVisual();
    fechaInicioManual = null;
    fechaFinManual = null;
    contadorDias.classList.remove('visible');
});


    // Funciones auxiliares
    function limpiarSeleccionVisual() {
        document.querySelectorAll('.fc-daygrid-day').forEach(el => {
            el.classList.remove('fc-day-selected');
        });
    }

    function pintarDia(fechaStr) {
        const celda = document.querySelector(`[data-date="${fechaStr}"]`);
        if (celda) {
            celda.classList.add('fc-day-selected');
        }
    }

    function obtenerRangoFechasStr(fechaInicioStr, fechaFinStr) {
        const rango = [];
        const inicio = new Date(fechaInicioStr);
        const fin = new Date(fechaFinStr);
        const actual = new Date(inicio.getTime());

        while (actual <= fin) {
            const fechaFormateada = actual.toISOString().split('T')[0];
            rango.push(fechaFormateada);
            actual.setDate(actual.getDate() + 1);
        }

        return rango;
    }
});
</script>

    <div id="formulario-valoracion" class="mt-4" style="display: none;">
        <h5>Deja tu valoración</h5>
        <form id="form-valoracion" class="mb-4" method="POST">
            <input type="hidden" name="id_vehiculos" value="{{ $vehiculo->id_vehiculos }}">
            <div class="form-group">
                <div class="row">
                    @auth
                    <div class="col-md-6">
                        <label for="nombre">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="{{ Auth::user()->nombre }}" readonly>
                    </div>
                    @endauth
                    <div class="col-md-6">
                        <label for="valoracion">Puntuación</label>
                        <div class="rating">
                            <i class="far fa-star" data-value="1"></i>
                            <i class="far fa-star" data-value="2"></i>
                            <i class="far fa-star" data-value="3"></i>
                            <i class="far fa-star" data-value="4"></i>
                            <i class="far fa-star" data-value="5"></i>
                        </div>
                        <input type="hidden" id="valoracion" name="valoracion" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="comentario">Comentario</label>
                <textarea class="form-control" id="comentario" name="comentario" rows="3" placeholder="Escribe tu comentario aquí..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Enviar Valoración</button>
        </form>
    </div>
    <h4 class="mt-5">VALORACIONES</h4>

    <div id="valoraciones-container">
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Cargando valoraciones...</span>
            </div>
            <p>Cargando valoraciones...</p>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/vehiculos.js') }}"></script>
<script src="{{ asset('js/valoraciones.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        iniciarDetalleVehiculo({{ $vehiculo->id_vehiculos }});
    });
</script>
<!-- Modal personalizado Bootstrap -->
<div class="modal fade" id="reservaModal" tabindex="-1" role="dialog" aria-labelledby="reservaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content shadow" style="border-radius: 16px; background-color: #ffffff;">
      <div class="modal-header" style="background-color: #6f42c1; color: white; border-top-left-radius: 16px; border-top-right-radius: 16px;">
        <h5 class="modal-title" id="reservaModalLabel">Confirmar reserva</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Cerrar">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center" style="color: #000;">
        <p><i class="fas fa-calendar-alt" style="color: #6f42c1; margin-right: 0.5rem;"></i> <strong>Fechas:</strong><br>
        <span id="modal-fechas"></span></p>
        <p><i class="fas fa-clock" style="color: #6f42c1; margin-right: 0.5rem;"></i> <strong>Días seleccionados:</strong> <span id="modal-dias"></span></p>
        <p><i class="fas fa-euro-sign" style="color: #6f42c1; margin-right: 0.5rem;"></i> <strong>Precio por día:</strong> €<span id="modal-precio-dia"></span></p>
        <p class="h5 mt-3 text-dark"><strong>Total estimado:</strong> €<span id="modal-total"></span></p>
      </div>
      <div class="modal-footer justify-content-between" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="btnConfirmarReserva" style="background-color: #6f42c1; border-color: #6f42c1;">Reservar</button>
      </div>
    </div>
  </div>
</div>

<script>
    const vehiculoId = {{ $vehiculo->id_vehiculos }};
    const userId = {{ Auth::id() ?? 'null' }};
</script>
</body>
</html>
