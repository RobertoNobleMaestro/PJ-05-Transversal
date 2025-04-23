// Variables globales para el conteo de vehículos y precio total
let vehicleCount = 0; // Este valor se actualizará desde la vista
let precioTotal = 0; // Este valor se actualizará desde la vista

/**
 * Agrega un nuevo vehículo al formulario
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
 * Elimina un vehículo del formulario
 * @param {number} index Índice del vehículo a eliminar
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
 * Calcula el precio para un vehículo específico
 * @param {number} index Índice del vehículo
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
 * Actualiza el precio total de la reserva
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

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Establecer los valores iniciales
    vehicleCount = parseInt(document.getElementById('vehiculos-container').getAttribute('data-count') || 0);
    precioTotal = parseFloat(document.getElementById('precio-total').getAttribute('data-precio') || 0);
    
    // Calcular precio para cada vehículo existente
    for (let i = 0; i < vehicleCount; i++) {
        calcularPrecio(i);
    }
});
