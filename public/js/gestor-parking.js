let mapInstance;

document.addEventListener('DOMContentLoaded', function () {
    // Mostrar notificación si hay mensaje de éxito o error
    if (window.sessionSuccess) {
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: window.sessionSuccess,
            confirmButtonColor: '#9F17BD'
        });
    }
    if (window.sessionError) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: window.sessionError,
            confirmButtonColor: '#9F17BD'
        });
    }

    mapInstance = L.map('map').setView([40.4168, -3.7038], 6);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(mapInstance);

    // Evita declarar múltiples veces la misma variable con let marker
    window.parkingsData = window.parkingsData || [];
    // Los datos de los parkings deben ser inyectados desde el backend en una variable global o por AJAX
    if (typeof parkingsBladeData !== 'undefined') {
        parkingsBladeData.forEach(function(parking, index) {
            const marker = L.marker([parking.latitud, parking.longitud]).addTo(mapInstance);
            marker.bindPopup(`
                <strong>${parking.nombre}</strong><br>
                Plazas: ${parking.plazas}<br>
                <button class='btn btn-sm btn-warning mt-2'
                    onclick="openEditPanel(${parking.id}, '${parking.nombre.replace(/'/g, "\\'")}', ${parking.plazas}, ${parking.latitud}, ${parking.longitud})">
                    Editar
                </button>
                <button class='btn btn-sm btn-danger mt-2'
                    onclick="confirmarEliminacion(${parking.id})">
                    Eliminar
                </button>
            `);
        });
    }
});

window.openEditPanel = function(id, nombre, plazas, lat, lng) {
    const form = document.getElementById('editParkingForm');

    document.getElementById('parkingId').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('plazas').value = plazas;
    document.getElementById('latitud').value = lat;
    document.getElementById('longitud').value = lng;
    form.action = `/gestor/parking/${id}`;
    form.method = 'POST'; // Laravel usa POST + _method para PUT

    // Asegura que el input _method sea PUT
    let methodInput = form.querySelector('input[name="_method"]');
    if (!methodInput) {
        methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'PUT';
        form.appendChild(methodInput);
    } else {
        methodInput.value = 'PUT';
    }

    // Cambia el título y el botón
    document.getElementById('editPanelTitle').textContent = 'Editar Parking';
    document.getElementById('submitBtn').textContent = 'Guardar';

    const panel = document.getElementById('editPanel');
    panel.classList.add('show');
    panel.style.display = 'block';

    setTimeout(() => {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 300);
}

window.closeEditPanel = function() {
    const panel = document.getElementById('editPanel');
    panel.classList.remove('show');
    panel.style.display = 'none';
}

window.validarFormulario = function(e) {
    e.preventDefault(); // Evita el envío inmediato

    const nombre = document.getElementById('nombre').value.trim();
    const plazas = document.getElementById('plazas').value.trim();
    const latitud = document.getElementById('latitud').value.trim();
    const longitud = document.getElementById('longitud').value.trim();

    if (!nombre || !plazas || !latitud || !longitud) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, rellena todos los campos antes de guardar.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    if (isNaN(plazas) || plazas <= 0) {
        Swal.fire({
            icon: 'error',
            title: 'Plazas inválidas',
            text: 'La cantidad de plazas debe ser un número mayor que cero.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    if (isNaN(latitud) || isNaN(longitud)) {
        Swal.fire({
            icon: 'error',
            title: 'Coordenadas inválidas',
            text: 'Latitud y longitud deben ser valores numéricos.',
            confirmButtonColor: '#9F17BD'
        });
        return false;
    }

    // Confirmación antes de guardar
    Swal.fire({
        title: '¿Guardar cambios?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, guardar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('editParkingForm').submit();
        }
    });

    return false;
}

window.confirmarEliminacion = function(parkingId) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción eliminará el parking de forma permanente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e3342f',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.getElementById('deleteForm');
            form.action = `/gestor/parking/${parkingId}`;
            form.submit();
        }
    });
}

window.openCreatePanel = function() {
    const parkingId = document.getElementById('parkingId');
    const nombre = document.getElementById('nombre');
    const plazas = document.getElementById('plazas');
    const latitud = document.getElementById('latitud');
    const longitud = document.getElementById('longitud');
    const form = document.getElementById('editParkingForm');
    const panel = document.getElementById('editPanel');

    if (!parkingId || !nombre || !plazas || !latitud || !longitud || !form || !panel) {
        alert('Error: No se encontraron los campos del formulario. Revisa los IDs en el HTML.');
        return;
    }

    parkingId.value = '';
    nombre.value = '';
    plazas.value = '';
    latitud.value = '';
    longitud.value = '';

    form.action = '/gestor/parking';
    form.method = 'POST';

    // Elimina el input _method si existe
    let methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();

    // Cambia el título y el botón
    document.getElementById('editPanelTitle').textContent = 'Añadir Parking';
    document.getElementById('submitBtn').textContent = 'Añadir';

    panel.classList.add('show');
    panel.style.display = 'block';
    setTimeout(() => {
        panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }, 300);
} 