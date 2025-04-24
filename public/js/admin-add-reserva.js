/**
 * CREACIÓN DE RESERVAS - PANEL DE ADMINISTRACIÓN
 * Este archivo contiene las funciones necesarias para la creación de reservas
 * desde el panel de administración. Permite añadir múltiples vehículos a una 
 * misma reserva, calcular precios dinámicamente y validar fechas.
 * Es una funcionalidad clave para la gestión integral del negocio de alquiler.
 */

// Variables globales para el conteo de vehículos y precio total
let vehicleCount = 1; // Empezamos con un vehículo
let precioTotal = 0;

/**
 * addVehicle() - Agrega un nuevo vehículo al formulario de reserva
 * 
 * Esta función crea dinámicamente un nuevo conjunto de campos para añadir
 * otro vehículo a la reserva. Utiliza una plantilla HTML predefinida y 
 * actualiza los identificadores para mantener la coherencia del formulario.
 * Permite crear reservas con múltiples vehículos de forma intuitiva.
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
 * Esta función permite eliminar un vehículo específico del formulario,
 * recalcula el precio total y actualiza la interfaz. También gestiona
 * la visibilidad de los botones para garantizar que siempre quede al
 * menos un vehículo en la reserva.
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
 * Valida que las fechas sean coherentes y muestra el desglose del cálculo
 * para facilitar la comprensión del precio final.
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
 * Esta función recorre todos los vehículos añadidos al formulario, 
 * suma sus precios individuales y actualiza el precio total mostrado.
 * Se ejecuta automáticamente cada vez que se modifica un vehículo
 * o sus fechas para mantener siempre actualizado el total.
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
    document.getElementById('precio-total').innerHTML = `<strong>Precio Total Estimado:</strong> ${total.toFixed(2)} €`;
    precioTotal = total;
}

/**
 * Inicialización cuando el DOM está completamente cargado
 * 
 * Configura los eventos iniciales para el primer vehículo y 
 * establece la fecha actual como valor predeterminado para
 * la fecha de reserva.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Configuración inicial
    document.getElementById('vehiculos_0').addEventListener('change', function() {
        calcularPrecio(0);
    });
    
    // Establecer la fecha de hoy como valor por defecto para fecha_reserva
    const fechaReservaInput = document.getElementById('fecha_reserva');
    if (fechaReservaInput) {
        fechaReservaInput.valueAsDate = new Date();
    }
});
