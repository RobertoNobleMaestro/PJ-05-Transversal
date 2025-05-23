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
    
    // --- VALIDACIÓN VISUAL AVANZADA ---
    function mostrarErrorCampo($campo, mensaje) {
        $campo.addClass('is-invalid');
        $campo.next('.invalid-feedback').show().text(mensaje);
    }
    function limpiarErrorCampo($campo) {
        $campo.removeClass('is-invalid');
        $campo.next('.invalid-feedback').hide();
    }
    // Ocultar feedback al abrir modal
    $('#modalAgendarMantenimiento').on('show.bs.modal', function() {
        $('#formAgendarMantenimiento .is-invalid').removeClass('is-invalid');
        $('#formAgendarMantenimiento .invalid-feedback').hide();
    });
    // Validar en blur
    $('#formAgendarMantenimiento').on('blur change', 'input, select', function() {
        const $campo = $(this);
        if ($campo.prop('required') && !$campo.val()) {
            mostrarErrorCampo($campo, $campo.next('.invalid-feedback').text());
        } else {
            limpiarErrorCampo($campo);
        }
    });
    // Enviar formulario de mantenimiento
    $('#formAgendarMantenimiento').submit(function(e) {
        e.preventDefault();
        let valido = true;
        // Validar todos los campos requeridos
        $(this).find('input, select').each(function() {
            const $campo = $(this);
            if ($campo.prop('required') && !$campo.val()) {
                mostrarErrorCampo($campo, $campo.next('.invalid-feedback').text());
                valido = false;
            } else {
                limpiarErrorCampo($campo);
            }
        });
        if (!valido) return;
        const vehiculoId = $('#vehiculo-id').val();
        const fecha = $('#fecha-mantenimiento').val();
        const hora = $('#hora-mantenimiento').val();
        const tallerId = $('#taller-id').val();
        // Verificar que la fecha no sea anterior a hoy
        const hoy = new Date().toISOString().split('T')[0];
        if (fecha && fecha < hoy) {
            mostrarErrorCampo($('#fecha-mantenimiento'), 'La fecha no puede ser anterior a hoy.');
            return;
        }
        // Enviar solicitud AJAX
        var dataAjax = {
                id_vehiculo: vehiculoId,
                fecha_mantenimiento: fecha,
                hora_mantenimiento: hora,
                taller_id: tallerId,
                motivo_reserva: $('#motivo-reserva').val(),
                _token: $('meta[name="csrf-token"]').attr('content')
        };
        if ($('#motivo-reserva').val() === 'averia') {
            dataAjax.motivo_averia = $('#motivo-averia').val();
        }
        $.ajax({
            url: '/taller/agendar-mantenimiento',
            type: 'POST',
            data: dataAjax,
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
        if (tipo === 'error') {
            let titulo = '¡Atención! Ocurrió un problema';
            let subtitulo = '';
            // Personalización según el error
            if (mensaje.includes('campos') || mensaje.includes('completar')) {
                titulo = 'Faltan datos obligatorios';
                subtitulo = 'Por favor, completa todos los campos requeridos para agendar el mantenimiento.';
            } else if (mensaje.includes('fecha pasada') || mensaje.includes('anterior a hoy')) {
                titulo = 'Fecha inválida';
                subtitulo = 'La fecha seleccionada no puede ser anterior a hoy. Selecciona una fecha válida.';
            } else if (mensaje.includes('hora')) {
                titulo = 'Hora no seleccionada';
                subtitulo = 'Debes seleccionar una hora disponible para el mantenimiento.';
            } else if (mensaje.includes('motivo')) {
                titulo = 'Motivo de reserva requerido';
                subtitulo = 'Por favor selecciona el motivo de la reserva.';
            }
            Swal.fire({
                title: `<span style='color:#4B1668;font-weight:bold;'>${titulo}</span>`,
                html: `<div style='font-size:1.1em;color:#4B1668;font-weight:600;'><i class='fas fa-exclamation-triangle fa-lg me-2' style='color:#4B1668;'></i><span style='color:#111;'>${mensaje}</span></div><div style='font-size:0.97em;color:#333;margin-top:8px;'>${subtitulo}</div>`,
                icon: 'warning',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#4B1668',
                background: '#f8f4fa',
                customClass: {
                    title: '',
                    popup: 'border border-2' + ' border-purple-strong rounded-4'
                }
            });
        } else {
            Swal.fire({
                title: `<span style='color:#4B1668;font-weight:bold;'>Éxito</span>`,
                html: `<div style='font-size:1.1em;color:#4B1668;font-weight:600;'><i class='fas fa-check-circle fa-lg me-2' style='color:#4B1668;'></i><span style='color:#111;'>${mensaje}</span></div>`,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#4B1668',
                background: '#f8f4fa',
                customClass: {
                    title: '',
                    popup: 'border border-2' + ' border-purple-strong rounded-4'
                }
            });
        }
    }
}); 