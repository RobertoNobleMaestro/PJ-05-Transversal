/**
 * EDICIÓN DE RESERVAS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene las funciones necesarias para gestionar la edición
 * de reservas existentes en el sistema. Permite modificar fechas, vehículos,
 * agregar nuevos vehículos a una reserva o eliminarlos, y actualiza automáticamente
 * los precios. Es una funcionalidad crucial para la gestión operativa del negocio.
 */

// Variables globales para el conteo de vehículos y precio total
let vehicleCount = 0; // Este valor se actualizará desde la vista
let precioTotal = 0; // Este valor se actualizará desde la vista

/**
 * addVehicle() - Agrega un nuevo vehículo al formulario de edición de reserva
 * 
 * Esta función crea dinámicamente un nuevo conjunto de campos para añadir
 * un vehículo adicional a la reserva que se está editando. Utiliza una plantilla
 * HTML predefinida y mantiene la coherencia de IDs y numeración para el envío correcto
 * del formulario. Gestiona también la visualización de los botones de eliminación.
 */
function addVehicle() {
    const container = document.getElementById('vehiculos-container');
    const newIndex = vehicleCount;
    
    const vehicleEntry = document.createElement('div');
    vehicleEntry.className = 'vehicle-entry';
    vehicleEntry.id = `vehicle-entry-${newIndex}`;
    
    // El HTML del nuevo vehículo se tomará del atributo data-template del contenedor
    const template = container.getAttribute('data-template');
    
    // Reemplazar los marcadores de posición en la plantilla
    let html = template.replace(/\${newIndex}/g, newIndex);
    html = html.replace(/\${vehicleNumber}/g, newIndex + 1);
    
    vehicleEntry.innerHTML = html;
    container.appendChild(vehicleEntry);
    vehicleCount++;
    
    // Mostrar botones de eliminar si hay más de un vehículo
    const removeButtons = document.querySelectorAll('.remove-vehicle-btn');
    if (removeButtons.length > 1) {
        removeButtons.forEach(button => {
            button.style.display = 'block';
        });
    }
}

/**
 * removeVehicle(index) - Elimina un vehículo del formulario de reserva
 * 
 * @param {number} index - Índice del vehículo a eliminar
 * 
 * Esta función elimina un vehículo específico de la reserva en edición,
 * recalcula el precio total y actualiza la interfaz. También gestiona
 * la visibilidad de los botones de eliminación para garantizar que siempre
 * quede al menos un vehículo en la reserva.
 */
function removeVehicle(index) {
    const vehicleEntry = document.getElementById(`vehicle-entry-${index}`);
    vehicleEntry.remove();
    
    // Recalcular precio total
    actualizarPrecioTotal();
    
    // Ocultar botones de eliminar si solo queda un vehículo
    const removeButtons = document.querySelectorAll('.remove-vehicle-btn');
    if (removeButtons.length <= 1) {
        removeButtons[0].style.display = 'none';
    }
}

/**
 * calcularPrecio(index) - Calcula el precio para un vehículo específico
 * 
 * @param {number} index - Índice del vehículo para el que se calcula el precio
 * 
 * Esta función calcula el precio de alquiler para un vehículo específico 
 * basándose en el precio diario del vehículo y el rango de fechas seleccionado.
 * Realiza validaciones para garantizar la coherencia de fechas y muestra
 * el desglose del cálculo con formato claro. Finalmente, actualiza el precio total.
 */
function calcularPrecio(index) {
    const vehiculoSelect = document.getElementById(`vehiculos_${index}`);
    const fechaInicio = document.getElementById(`fecha_inicio_${index}`);
    const fechaFin = document.getElementById(`fecha_fin_${index}`);
    const precioInfo = document.getElementById(`precio-info-${index}`);
    
    if (vehiculoSelect.value && fechaInicio.value && fechaFin.value) {
        const precioDiario = parseFloat(vehiculoSelect.options[vehiculoSelect.selectedIndex].dataset.precio);
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        // Verificar que la fecha de fin sea posterior a la de inicio
        if (fin < inicio) {
            precioInfo.innerHTML = '<strong class="text-danger">Error: La fecha de fin debe ser posterior a la fecha de inicio.</strong>';
            precioInfo.className = 'alert alert-danger precio-info';
            precioInfo.style.display = 'block';
            return;
        }
        
        // Calcular número de días (incluyendo el día de fin)
        const diffTime = Math.abs(fin - inicio);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        // Calcular precio para este vehículo
        const precioVehiculo = precioDiario * diffDays;
        
        precioInfo.innerHTML = `<strong>Precio para este vehículo:</strong> ${precioVehiculo.toFixed(2)} € (${precioDiario} €/día x ${diffDays} días)`;
        precioInfo.className = 'alert alert-info precio-info';
        precioInfo.style.display = 'block';
        
        // Actualizar precio total
        actualizarPrecioTotal();
    } else {
        precioInfo.style.display = 'none';
    }
}

/**
 * actualizarPrecioTotal() - Actualiza el precio total de la reserva
 * 
 * Esta función recorre todos los vehículos incluidos en la reserva,
 * suma sus precios individuales basados en el rango de fechas específico
 * de cada uno y actualiza el precio total mostrado. Se ejecuta automáticamente
 * cada vez que se modifica un vehículo o sus fechas.
 */
function actualizarPrecioTotal() {
    let total = 0;
    
    // Recorrer todos los vehículos
    for (let i = 0; i < vehicleCount; i++) {
        const vehiculoElement = document.getElementById(`vehiculos_${i}`);
        const fechaInicioElement = document.getElementById(`fecha_inicio_${i}`);
        const fechaFinElement = document.getElementById(`fecha_fin_${i}`);
        
        // Si este índice existe y tiene todos los valores
        if (vehiculoElement && fechaInicioElement && fechaFinElement &&
            vehiculoElement.value && fechaInicioElement.value && fechaFinElement.value) {
            
            const precioDiario = parseFloat(vehiculoElement.options[vehiculoElement.selectedIndex].dataset.precio);
            const inicio = new Date(fechaInicioElement.value);
            const fin = new Date(fechaFinElement.value);
            
            // Verificar que la fecha de fin sea posterior a la de inicio
            if (fin >= inicio) {
                const diffTime = Math.abs(fin - inicio);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                
                // Sumar al total
                total += precioDiario * diffDays;
            }
        }
    }
    
    // Actualizar elemento en la UI
    document.getElementById('precio-total').innerHTML = `<strong>Precio Total:</strong> ${total.toFixed(2)} €`;
    precioTotal = total;
}

/**
 * validateReservaForm() - Valida el formulario de edición de reserva
 * 
 * @returns {boolean} - Retorna true si el formulario es válido, false en caso contrario
 * 
 * Esta función verifica todos los campos del formulario de edición de reserva,
 * asegurando que se hayan completado los datos obligatorios, que las fechas sean
 * coherentes y que todos los vehículos tengan la información necesaria.
 * Muestra mensajes de error claros y específicos cuando hay problemas.
 */
function validateReservaForm() {
    let isValid = true;
    const errorMessages = [];
    
    // Validar usuario y lugar (no deberían cambiar en edición, pero por seguridad)
    const usuarioSelect = document.getElementById('usuario');
    if (usuarioSelect && !usuarioSelect.value) {
        errorMessages.push('Debes seleccionar un usuario para la reserva');
        usuarioSelect.classList.add('is-invalid');
        isValid = false;
    } else if (usuarioSelect) {
        usuarioSelect.classList.remove('is-invalid');
    }
    
    const lugarSelect = document.getElementById('lugar');
    if (lugarSelect && !lugarSelect.value) {
        errorMessages.push('Debes seleccionar un lugar para la reserva');
        lugarSelect.classList.add('is-invalid');
        isValid = false;
    } else if (lugarSelect) {
        lugarSelect.classList.remove('is-invalid');
    }
    
    // Validar estado de la reserva
    const estadoSelect = document.getElementById('estado');
    if (!estadoSelect.value) {
        errorMessages.push('Debes seleccionar un estado para la reserva');
        estadoSelect.classList.add('is-invalid');
        isValid = false;
    } else {
        estadoSelect.classList.remove('is-invalid');
    }
    
    // Validar vehículos
    for (let i = 0; i < vehicleCount; i++) {
        const vehiculoElement = document.getElementById(`vehiculos_${i}`);
        const fechaInicioElement = document.getElementById(`fecha_inicio_${i}`);
        const fechaFinElement = document.getElementById(`fecha_fin_${i}`);
        
        // Solo validar si el elemento existe
        if (vehiculoElement && fechaInicioElement && fechaFinElement) {
            if (!vehiculoElement.value) {
                errorMessages.push(`Debes seleccionar un vehículo en la entrada #${i+1}`);
                vehiculoElement.classList.add('is-invalid');
                isValid = false;
            } else {
                vehiculoElement.classList.remove('is-invalid');
            }
            
            if (!fechaInicioElement.value) {
                errorMessages.push(`Debes seleccionar una fecha de inicio en la entrada #${i+1}`);
                fechaInicioElement.classList.add('is-invalid');
                isValid = false;
            } else {
                fechaInicioElement.classList.remove('is-invalid');
            }
            
            if (!fechaFinElement.value) {
                errorMessages.push(`Debes seleccionar una fecha de fin en la entrada #${i+1}`);
                fechaFinElement.classList.add('is-invalid');
                isValid = false;
            } else {
                fechaFinElement.classList.remove('is-invalid');
            }
            
            // Validar que fecha fin sea posterior a fecha inicio
            if (fechaInicioElement.value && fechaFinElement.value) {
                const inicio = new Date(fechaInicioElement.value);
                const fin = new Date(fechaFinElement.value);
                
                if (fin < inicio) {
                    errorMessages.push(`La fecha de fin debe ser posterior a la fecha de inicio en la entrada #${i+1}`);
                    fechaFinElement.classList.add('is-invalid');
                    isValid = false;
                }
            }
        }
    }
    
    // Mostrar errores si existen
    if (!isValid) {
        let errorHtml = '<ul class="text-start list-unstyled mb-0">';
        errorMessages.forEach(msg => {
            errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${msg}</li>`;
        });
        errorHtml += '</ul>';
        
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
            html: errorHtml,
            confirmButtonColor: '#9F17BD'
        });
    }
    
    return isValid;
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los valores iniciales de la reserva existente, calcula los
 * precios de los vehículos ya asociados y establece los eventos para
 * la actualización dinámica de precios y validación del formulario.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Establecer los valores iniciales
    vehicleCount = parseInt(document.getElementById('vehiculos-container').getAttribute('data-count') || 0);
    precioTotal = parseFloat(document.getElementById('precio-total').getAttribute('data-precio') || 0);
    
    // Calcular precio para cada vehículo existente
    for (let i = 0; i < vehicleCount; i++) {
        calcularPrecio(i);
        
        // Configurar eventos para cada vehículo
        const vehiculoSelect = document.getElementById(`vehiculos_${i}`);
        const fechaInicio = document.getElementById(`fecha_inicio_${i}`);
        const fechaFin = document.getElementById(`fecha_fin_${i}`);
        
        if (vehiculoSelect) {
            vehiculoSelect.addEventListener('change', function() {
                calcularPrecio(i);
            });
        }
        
        if (fechaInicio) {
            fechaInicio.addEventListener('change', function() {
                calcularPrecio(i);
            });
        }
        
        if (fechaFin) {
            fechaFin.addEventListener('change', function() {
                calcularPrecio(i);
            });
        }
    }
    
    // Añadir evento submit al formulario
    const form = document.getElementById('editReservaForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateReservaForm()) {
                e.preventDefault();
            }
        });
    }
});
