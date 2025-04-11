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
        <div class="col-md-6">
            <div class="imagen-box text-center">
                <img src="{{ asset('img/' . $vehiculo->imagen) }}" class="img-fluid mb-3" alt="">
                <img src="{{ asset('img/mercedes.png') }}" class="img-fluid" alt="">
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
            
            <div class="highlight-box">
                <button id="btnAñadirCarrito" 
                        class="btn w-100 d-flex align-items-center"
                        data-vehiculo-id="{{ $vehiculo->id_vehiculos }}">
                    <i class="fas fa-shopping-cart fa-bounce mr-3"></i> 
                    <div>
                        <strong>¡Añade este vehículo a tu carrito!</strong><br>
                    </div>
                </button>
            </div>

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
                    if (diaFecha >= resInicio && diaFecha <= resFin) {
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
            contadorDias.textContent = `Has seleccionado ${totalDias} día${totalDias > 1 ? 's' : ''}`;
            contadorDias.classList.add('visible');

            // Confirmar con modal
            Swal.fire({
                title: '¿Confirmar reserva?',
                html: `Desde <strong>${fechaInicioManual}</strong> hasta <strong>${fechaFinManual}</strong><br><br>
                       <strong>${totalDias} día${totalDias > 1 ? 's' : ''}</strong> de alquiler`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Reservar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
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
                    .then(response => response.json())
                    .then(() => {
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
                } else {
                    limpiarSeleccionVisual();
                    fechaInicioManual = null;
                    fechaFinManual = null;
                    contadorDias.classList.remove('visible');
                }
            });
        }
    });

    calendar.render();

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



    <!-- Valoraciones con Fetch API -->
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

<footer>
    <div class="container text-center">
        <p class="m-0">Carflow &copy; 2025</p>
    </div>
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/vehiculos.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        iniciarDetalleVehiculo({{ $vehiculo->id_vehiculos }});
    });
</script>
</body>
</html>
<style>
    /* Fondo del calendario */
#calendario-reservas {
  background-color: #ffffff;
  border-radius: 16px;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
  padding: 1.5rem;
  margin-top: 2rem;
  color: #000;
  text-decoration: none;
}
a{
    color: black;
    text-decoration: none;
}
a:hover{
    color: black;
    text-decoration: none;   
}
/* Título del mes */
.fc-toolbar-title {
  font-size: 1.5rem;
  font-weight: 600;
  color: #6f42c1;
}

/* Botones de navegación */
.fc-button {
  border: none;
  color: white;
  font-weight: 500;
  padding: 0.4rem 1rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  margin: 0 2px;
}

.fc-button:hover {
  background: #5a2b97;
  transform: scale(1.03);
}

/* Encabezado de los días (Lun, Mar...) */
.fc-col-header-cell {
  background: #f3f0fa;
  color: #6f42c1;
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.9rem;
}

/* Celda del día */
.fc-daygrid-day {
  background-color: #fafafa;
  transition: background-color 0.3s ease;
}

.fc-daygrid-day:hover {
  background-color: #f0e9ff;
}

/* Día actual */
.fc-day-today {
  background-color: #e9d7ff !important;
  border-radius: 8px;
}

/* Evento */
.fc-event {
  background-color: #6f42c1 !important;
  color: #fff !important;
  border: none;
  font-weight: 500;
  font-size: 0.85rem;
  padding: 2px 6px;
  border-radius: 12px;
  text-align: center;
}
/* Resaltar los días seleccionados */
.fc-day-selected {
    background-color: #d0ebff !important;
    border-radius: 8px;
    animation: fadeIn 0.3s ease-in-out;
    position: relative;
}

/* Animación */
@keyframes fadeIn {
    from { background-color: #fff; opacity: 0.2; }
    to { background-color: #d0ebff; opacity: 1; }
}

/* Contador visual */
#contador-dias {
    font-weight: 600;
    color: #0d6efd;
    font-size: 1.1rem;
    margin-bottom: 1rem;
    text-align: center;
    transition: all 0.3s ease-in-out;
    opacity: 0;
}
#contador-dias.visible {
    opacity: 1;
}

/* Responsive ajustes */
@media (max-width: 767px) {
  .fc-toolbar-title {
    font-size: 1.2rem;
  }

  .fc-button {
    padding: 0.3rem 0.8rem;
    font-size: 0.85rem;
  }
}

</style>