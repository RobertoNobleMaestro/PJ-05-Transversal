@extends('layouts.admin')

@section('title', 'Añadir Vehículo')

@section('content')
<style>
    .add-vehicle-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 2rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    }

    .form-title {
        color: #2d3748;
        font-size: 1.8rem;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #9F17BD;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group.full-width {
        grid-column: span 4;
    }
    
    .form-group.half-width {
        grid-column: span 2;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #4a5568;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        transition: border-color 0.3s;
    }

    .form-control:focus {
        border-color: #9F17BD;
        outline: none;
        box-shadow: 0 0 0 2px rgba(159, 23, 189, 0.1);
    }

    .form-check {
        display: flex;
        align-items: center;
        margin-top: 0.5rem;
    }

    .form-check-input {
        margin-right: 0.5rem;
    }

    .btn-container {
        display: flex;
        justify-content: space-between;
        margin-top: 1.5rem;
    }
    
    .submit-btn {
        background: #000000;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .submit-btn:hover {
        background: #333333;
    }
    
    .cancel-btn {
        background: #9F17BD;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
    }

    .cancel-btn:hover {
        background: #7E12A3;
    }

    .error-message {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>

<div class="add-vehicle-container">
    <h1 class="form-title">Añadir Nuevo Vehículo</h1>
    <form id="addVehiculoForm">
        @csrf
        <div class="form-grid">
            <!-- Columna izquierda -->
            <div>
                <div class="form-group">
                    <label for="marca" class="form-label">Marca</label>
                    <input type="text" class="form-control" id="marca" name="marca" required>
                </div>

                <div class="form-group">
                    <label for="modelo" class="form-label">Modelo</label>
                    <input type="text" class="form-control" id="modelo" name="modelo" required>
                </div>

                <div class="form-group">
                    <label for="año" class="form-label">Año</label>
                    <input type="number" class="form-control" id="año" name="año" min="1900" max="{{ date('Y') + 1 }}" required>
                </div>



                <div class="form-group">
                    <label for="precio_dia" class="form-label">Precio por día</label>
                    <input type="number" class="form-control" id="precio_dia" name="precio_dia" step="0.01" min="0" required>
                </div>
            </div>
            
            <!-- Columna derecha -->
            <div>
                <div class="form-group">
                    <label for="kilometraje" class="form-label">Kilometraje</label>
                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" min="0" required>
                </div>

                <div class="form-group">
                    <label for="id_lugar" class="form-label">Lugar</label>
                    <select class="form-control" id="id_lugar" name="id_lugar" required>
                        <option value="">Seleccionar lugar</option>
                        @foreach($lugares as $lugar)
                            <option value="{{ $lugar->id_lugar }}">{{ $lugar->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_tipo" class="form-label">Tipo de vehículo</label>
                    <select class="form-control" id="id_tipo" name="id_tipo" required>
                        <option value="">Seleccionar tipo</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo }}">{{ $tipo->nombre_tipo }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="seguro_incluido" name="seguro_incluido">
                        <label for="seguro_incluido" class="form-check-label">Seguro incluido</label>
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="disponibilidad" name="disponibilidad" checked>
                        <label for="disponibilidad" class="form-check-label">Disponible</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-container">
            <a href="{{ route('admin.vehiculos') }}" class="cancel-btn">Cancelar</a>
            <button type="button" class="submit-btn" onclick="createVehiculo()">Enviar</button>
        </div>
    </form>
</div>
@endsection

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            validateField(input);
        });
    });

    function validateField(input) {
        let errorMessage = '';
        const value = input.value.trim();
        
        if (input.name === 'marca' || input.name === 'modelo') {
            if (value.length < 2) {
                errorMessage = 'Este campo debe tener al menos 2 caracteres.';
            }
        } else if (input.name === 'año') {
            const currentYear = new Date().getFullYear();
            if (parseInt(value) < 1900 || parseInt(value) > currentYear + 1) {
                errorMessage = `El año debe estar entre 1900 y ${currentYear + 1}.`;
            }

        } else if (input.name === 'precio_dia') {
            if (parseFloat(value) <= 0) {
                errorMessage = 'El precio debe ser mayor que 0.';
            }
        } else if (input.name === 'kilometraje') {
            if (parseInt(value) < 0) {
                errorMessage = 'El kilometraje no puede ser negativo.';
            }
        } else if ((input.name === 'id_lugar' || input.name === 'id_tipo') && value === '') {
            errorMessage = 'Por favor, seleccione una opción.';
        } else if (input.required && value === '') {
            errorMessage = 'Este campo es obligatorio.';
        }
        
        const errorElement = input.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.textContent = errorMessage;
        } else if (errorMessage) {
            const span = document.createElement('span');
            span.classList.add('error-message');
            span.style.color = 'red';
            span.textContent = errorMessage;
            input.parentNode.insertBefore(span, input.nextSibling);
        }
    }
});

function createVehiculo() {
    // Limpiar mensajes de error previos
    document.querySelectorAll('.text-danger').forEach(el => el.remove());
    
    // Validar campos antes de enviar
    const form = document.getElementById('addVehiculoForm');
    const inputs = form.querySelectorAll('input, select');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.name) { // Solo validar elementos con nombres
            const value = input.value.trim();
            let errorMessage = '';
            
            // Reglas de validación para cada campo
            if (input.name === 'marca' || input.name === 'modelo') {
                if (value.length < 2) {
                    errorMessage = 'Este campo debe tener al menos 2 caracteres.';
                    isValid = false;
                }
            } else if (input.name === 'año') {
                const currentYear = new Date().getFullYear();
                if (parseInt(value) < 1900 || parseInt(value) > currentYear + 1) {
                    errorMessage = `El año debe estar entre 1900 y ${currentYear + 1}.`;
                    isValid = false;
                }
            } else if (input.name === 'precio_dia') {
                if (parseFloat(value) <= 0) {
                    errorMessage = 'El precio debe ser mayor que 0.';
                    isValid = false;
                }
            } else if (input.name === 'kilometraje') {
                if (parseInt(value) < 0) {
                    errorMessage = 'El kilometraje no puede ser negativo.';
                    isValid = false;
                }
            } else if ((input.name === 'id_lugar' || input.name === 'id_tipo') && value === '') {
                errorMessage = 'Por favor, seleccione una opción.';
                isValid = false;
            } else if (input.required && value === '') {
                errorMessage = 'Este campo es obligatorio.';
                isValid = false;
            }
            
            // Mostrar mensaje de error si es necesario
            if (errorMessage) {
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('error-message')) {
                    errorElement.textContent = errorMessage;
                } else {
                    const span = document.createElement('span');
                    span.classList.add('error-message');
                    span.style.color = 'red';
                    span.textContent = errorMessage;
                    input.parentNode.insertBefore(span, input.nextSibling);
                }
            }
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: '<span class="text-warning"><i class="fas fa-exclamation-triangle"></i> Campos Incompletos</span>',
            html: '<p class="lead">Por favor, complete todos los campos requeridos correctamente</p>',
            confirmButtonColor: '#9F17BD'
        });
        return;
    }
    
    // Mostrar indicador de carga
    Swal.fire({
        title: '<i class="fas fa-spinner fa-spin"></i> Procesando...',
        text: 'Creando nuevo vehículo',
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        allowEnterKey: false
    });
    
    // Obtener los datos del formulario
    const formData = new FormData(form);
    
    // Añadir checkboxes manualmente (ya que solo se incluyen si están marcados)
    formData.set('seguro_incluido', document.getElementById('seguro_incluido').checked ? 1 : 0);
    formData.set('disponibilidad', document.getElementById('disponibilidad').checked ? 1 : 0);
    
    fetch('{{ route("admin.vehiculos.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: '<span class="text-success"><i class="fas fa-check-circle"></i> ¡Completado!</span>',
                html: `<p class="lead">${data.message || 'Vehículo creado exitosamente'}</p>`,
                confirmButtonColor: '#9F17BD',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("admin.vehiculos") }}';
                }
            });
        } else if (data.errors) {
            // Construir mensaje de error HTML
            let errorHtml = '<ul class="text-start list-unstyled">';
            
            // Muestra errores de validación
            Object.keys(data.errors).forEach(field => {
                errorHtml += `<li><i class="fas fa-exclamation-circle text-danger"></i> ${data.errors[field][0]}</li>`;
                
                const input = document.querySelector(`[name="${field}"]`);
                if (input) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-danger mt-1';
                    errorDiv.textContent = data.errors[field][0];
                    input.parentNode.appendChild(errorDiv);
                }
            });
            
            errorHtml += '</ul>';
            
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error de validación</span>',
                html: errorHtml,
                confirmButtonColor: '#9F17BD'
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
                html: `<p class="lead">Error al crear vehículo: ${data.message || 'Error desconocido'}</p>`,
                confirmButtonColor: '#9F17BD'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: '<span class="text-danger"><i class="fas fa-times-circle"></i> Error</span>',
            html: '<p class="lead">Error de conexión. Por favor, inténtalo de nuevo.</p>',
            confirmButtonColor: '#9F17BD'
        });
    });
}
</script>
