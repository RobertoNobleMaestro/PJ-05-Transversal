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
        "info": true,
        "searching": false,
        "ordering": true
    });
    
    // Mostrar la tabla una vez inicializada
    $('#vehiculos-table-container').show();
    
    // Manejar clic en el botón de agendar mantenimiento
    $(document).on('click', '.btn-agendar-mantenimiento', function() {
        const vehiculoId = $(this).data('id');
        
        // Abrir modal con un formulario para la fecha y hora
        $('#vehiculo-id').val(vehiculoId);
        $('#modalAgendarMantenimiento').modal('show');
    });
    
    // Enviar formulario de mantenimiento
    $('#formAgendarMantenimiento').submit(function(e) {
        e.preventDefault();
        
        const vehiculoId = $('#vehiculo-id').val();
        const fecha = $('#fecha-mantenimiento').val();
        const hora = $('#hora-mantenimiento').val();
        
        if (!fecha || !hora) {
            mostrarAlerta('Debe seleccionar fecha y hora', 'error');
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
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Actualizar la fecha en la tabla
                    $(`#prox-mant-${vehiculoId}`).text(response.fecha);
                    
                    // Cerrar modal y mostrar mensaje de éxito
                    $('#modalAgendarMantenimiento').modal('hide');
                    mostrarAlerta('Mantenimiento agendado exitosamente', 'success');
                    
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