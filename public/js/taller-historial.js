$(document).ready(function() {
    console.log("Inicializando historial de mantenimientos - versión simplificada");
    
    // Variable para guardar el mantenimiento seleccionado
    let mantenimientoSeleccionado = null;
    
    // Inicializar DataTable con opciones básicas
    let table = $('#table-mantenimientos').DataTable({
        "pageLength": 10,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
        },
        "order": [[0, "desc"]],
        "responsive": true
    });
    
    // Vincular botones de exportación
    $('#btn-export-csv').on('click', function() {
        alert('Exportación CSV no disponible en modo de emergencia');
    });
    
    $('#btn-export-pdf').on('click', function() {
        alert('Exportación PDF no disponible en modo de emergencia');
    });
    
    // Cargar lista de mantenimientos al iniciar
    cargarMantenimientosModoEmergencia('todos');
    
    // Manejar cambios en el filtro de estado
    $('input[name="estado-filter"]').on('change', function() {
        const estado = $(this).val();
        cargarMantenimientosModoEmergencia(estado);
    });
    
    // Función de carga en modo emergencia
    function cargarMantenimientosModoEmergencia(estado) {
        console.log("Cargando mantenimientos con filtro:", estado);
        
        // Limpiar tabla existente
        table.clear().draw();
        
        // Mostrar spinner de carga
        $('#mantenimientos-body').html(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2">Cargando mantenimientos (modo emergencia)...</p>
                </td>
            </tr>
        `);
        
        // Utilizar fetch en lugar de $.ajax para mayor compatibilidad
        fetch(`/taller/mantenimientos?estado=${estado}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Datos recibidos:", data);
                if (data.success) {
                    renderizarMantenimientosModoEmergencia(data.mantenimientos);
                } else {
                    mostrarMensajeError('Error: ' + (data.message || 'No se pudieron cargar los mantenimientos'));
                }
            })
            .catch(error => {
                console.error("Error en fetch:", error);
                mostrarMensajeError('Error de conexión: ' + error.message);
            });
    }
    
    // Función para renderizar mantenimientos en modo emergencia
    function renderizarMantenimientosModoEmergencia(mantenimientos) {
        // Verificar si hay datos
        if (!mantenimientos || mantenimientos.length === 0) {
            $('#mantenimientos-body').html(`
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p>No hay mantenimientos con el filtro seleccionado.</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        // Limpiar tabla
        table.clear();
        
        // Agregar cada mantenimiento a la tabla
        mantenimientos.forEach(function(item) {
            // Color del badge según el estado
            const estadoClass = {
                'pendiente': 'warning',
                'completado': 'success',
                'cancelado': 'danger'
            }[item.estado] || 'secondary';
            
            // Botones de acción según el estado
            let accionesBtns = `
                <button class="btn btn-sm btn-info btn-ver-detalle" data-id="${item.id}">
                    <i class="fas fa-eye"></i>
                </button>
            `;
            
            if (item.estado === 'pendiente') {
                accionesBtns += `
                    <button class="btn btn-sm btn-success btn-completar" data-id="${item.id}">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-cancelar" data-id="${item.id}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
            }
            
            // Agregar fila a la tabla
            table.row.add([
                item.id,
                item.vehiculo,
                item.matricula || 'N/A',
                item.taller,
                item.fecha,
                item.hora,
                `<span class="badge bg-${estadoClass}">${item.estado}</span>`,
                accionesBtns
            ]).draw(false);
        });
    }
    
    // Mostrar mensaje de error en lugar de SweetAlert (que podría fallar)
    function mostrarMensajeError(mensaje) {
        console.error(mensaje);
        $('#mantenimientos-body').html(`
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i> ${mensaje}
                    </div>
                    <button class="btn btn-primary mt-3" onclick="location.reload()">
                        <i class="fas fa-sync"></i> Reintentar
                    </button>
                </td>
            </tr>
        `);
    }
    
    // Manejar clic en botón ver detalle
    $(document).on('click', '.btn-ver-detalle', function() {
        const mantenimientoId = $(this).data('id');
        alert("Función de ver detalle no disponible en modo de emergencia. ID: " + mantenimientoId);
    });
    
    // Manejar clic en botón completar
    $(document).on('click', '.btn-completar', function() {
        const mantenimientoId = $(this).data('id');
        
        if (confirm("¿Desea marcar este mantenimiento como completado?")) {
            cambiarEstadoMantenimientoEmergencia(mantenimientoId, 'completado');
        }
    });
    
    // Manejar clic en botón cancelar
    $(document).on('click', '.btn-cancelar', function() {
        const mantenimientoId = $(this).data('id');
        
        if (confirm("¿Está seguro de cancelar este mantenimiento? Esta acción no se puede deshacer.")) {
            cambiarEstadoMantenimientoEmergencia(mantenimientoId, 'cancelado');
        }
    });
    
    // Cambiar estado en modo emergencia
    function cambiarEstadoMantenimientoEmergencia(id, estado) {
        console.log(`Cambiando estado de mantenimiento ${id} a ${estado}`);
        
        // Obtener token CSRF
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!token) {
            alert("Error: Token CSRF no encontrado");
            return;
        }
        
        // Crear objeto FormData para enviar datos
        const formData = new FormData();
        formData.append('estado', estado);
        formData.append('_token', token);
        formData.append('_method', 'PUT');
        
        // Realizar solicitud fetch
        fetch(`/taller/mantenimiento/${id}/estado`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("Respuesta:", data);
            if (data.success) {
                alert(data.message || "Estado actualizado correctamente");
                
                // Recargar lista de mantenimientos con el filtro actual
                const filtroActual = $('input[name="estado-filter"]:checked').val();
                cargarMantenimientosModoEmergencia(filtroActual);
            } else {
                alert("Error: " + (data.message || "No se pudo actualizar el estado"));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Error de conexión: " + error.message);
        });
    }
}); 