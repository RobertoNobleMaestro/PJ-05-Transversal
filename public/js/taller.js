$(document).ready(function() {
    // Inicializar DataTable con opciones
    var table = $('#vehiculos-table').DataTable({
        "pageLength": 10,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "order": [[0, "asc"]],
        "responsive": true,
        "paging": false,
        "info": false,
        "searching": false,
        "ordering": true
    });
    
    // Mostrar la tabla una vez inicializada
    $('#vehiculos-table-container').show();
    
    // Manejar clic en el botón de agendar mantenimiento
    $(document).on('click', '.btn-agendar-mantenimiento', function() {
        const vehiculoId = $(this).data('id');
        
        // Resetar el formulario
        $('#formAgendarMantenimiento')[0].reset();
        $('#hora-mantenimiento').prop('disabled', true).html('<option value="">Seleccione primero un taller y fecha</option>');
        $('#disponibilidad-info').text('');
        $('#alerta-disponibilidad').hide();
        
        // Establecer la fecha mínima como hoy
        const hoy = new Date().toISOString().split('T')[0];
        $('#fecha-mantenimiento').attr('min', hoy);
        
        // Abrir modal con un formulario para la fecha y hora
        $('#vehiculo-id').val(vehiculoId);
        $('#modalAgendarMantenimiento').modal('show');
    });
    
    // Manejar cambios en el taller seleccionado o fecha
    $('#taller-id, #fecha-mantenimiento').on('change', function() {
        const tallerId = $('#taller-id').val();
        const fecha = $('#fecha-mantenimiento').val();
        
        // Resetear la selección de hora
        $('#hora-mantenimiento').prop('disabled', true).html('<option value="">Cargando horarios disponibles...</option>');
        $('#disponibilidad-info').text('');
        
        // Verificar que se haya seleccionado un taller y una fecha
        if (tallerId && fecha) {
            // Verificar que la fecha no sea anterior a hoy
            const hoy = new Date().toISOString().split('T')[0];
            if (fecha < hoy) {
                mostrarAlerta('No se puede seleccionar una fecha pasada', 'error');
                $('#fecha-mantenimiento').val('');
                return;
            }
            
            // Obtener horarios disponibles
            obtenerHorariosDisponibles(tallerId, fecha);
            
            // Mostrar alerta de recordatorio de capacidad
            $('#alerta-disponibilidad').show();
        }
    });
    
    // Función para obtener horarios disponibles
    function obtenerHorariosDisponibles(tallerId, fecha) {
        $.ajax({
            url: '/taller/horarios-disponibles',
            type: 'GET',
            data: {
                taller_id: tallerId,
                fecha: fecha,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Llenar el selector de horas
                    const horasSelect = $('#hora-mantenimiento');
                    horasSelect.html('<option value="">Seleccione una hora</option>');
                    
                    // Agregar opciones de horas
                    response.horarios.forEach(function(horario) {
                        const disabled = !horario.disponible;
                        const option = `<option value="${horario.hora}" ${disabled ? 'disabled' : ''}>${horario.hora} (${horario.ocupacion})</option>`;
                        horasSelect.append(option);
                    });
                    
                    // Habilitar selector de hora
                    horasSelect.prop('disabled', false);
                    
                    // Información sobre disponibilidad
                    $('#disponibilidad-info').html('<small>Horarios en gris no están disponibles (ocupación máxima).</small>');
                    
                } else {
                    mostrarAlerta('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                let mensaje = 'Error al cargar horarios disponibles';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                mostrarAlerta(mensaje, 'error');
                $('#hora-mantenimiento').prop('disabled', true).html('<option value="">Error al cargar horarios</option>');
            }
        });
    }
    
    // Enviar formulario de mantenimiento
    $('#formAgendarMantenimiento').submit(function(e) {
        e.preventDefault();
        
        const vehiculoId = $('#vehiculo-id').val();
        const fecha = $('#fecha-mantenimiento').val();
        const hora = $('#hora-mantenimiento').val();
        const tallerId = $('#taller-id').val();
        
        // Validar formulario
        if (!fecha || !hora || !tallerId) {
            mostrarAlerta('Debe completar todos los campos', 'error');
            return;
        }
        
        // Verificar que la fecha no sea anterior a hoy
        const hoy = new Date().toISOString().split('T')[0];
        if (fecha < hoy) {
            mostrarAlerta('No se puede agendar mantenimiento en una fecha pasada', 'error');
            return;
        }
        
        // Enviar solicitud AJAX
        $.ajax({
            url: '/taller/agendar-mantenimiento',
            type: 'POST',
            data: {
                id_vehiculo: vehiculoId,
                fecha_mantenimiento: fecha,
                hora_mantenimiento: hora,
                taller_id: tallerId,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar la fecha en la tabla
                    $(`#prox-mant-${vehiculoId}`).text(response.fecha);
                    
                    // Cerrar modal y mostrar mensaje de éxito
                    $('#modalAgendarMantenimiento').modal('hide');
                    mostrarAlerta(response.message, 'success');
                    
                    // Limpiar formulario
                    $('#formAgendarMantenimiento')[0].reset();
                } else {
                    mostrarAlerta('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                let mensaje = 'Error al procesar la solicitud';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    mensaje = xhr.responseJSON.message;
                }
                mostrarAlerta(mensaje, 'error');
            }
        });
    });
    
    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo) {
        Swal.fire({
            title: tipo === 'success' ? 'Éxito' : 'Error',
            text: mensaje,
            icon: tipo,
            confirmButtonText: 'Aceptar'
        });
    }
}); 