<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Carrito de Vehículos</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Carrito de Vehículos Reservados</h1>
    <div id="carrito">Cargando...</div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            cargarCarrito();
        });

        function cargarCarrito() {
            fetch('/ver-carrito', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener los datos del carrito');
                }
                return response.json();
            })
            .then(data => {
                const contenedor = document.getElementById('carrito');
                contenedor.innerHTML = '';

                if (data.length === 0) {
                    contenedor.innerHTML = '<p>No tienes vehículos reservados.</p>';
                    return;
                }

                data.forEach(vehiculo => {
                    const div = document.createElement('div');
                    div.innerHTML = `
                        <h3>${vehiculo.marca} ${vehiculo.modelo}</h3>
                        <p>Año: ${vehiculo.anio}</p>
                        <p>Precio por día: ${vehiculo.precio_dia}€</p>
                        ${vehiculo.imagenes?.[0] ? `<img src="/storage/${vehiculo.imagenes[0].ruta}" width="200">` : ''}
                        <hr>
                    `;
                    contenedor.appendChild(div);
                });
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('carrito').innerHTML = '<p style="color:red;">Error al cargar el carrito.</p>';
            });
        }
    </script>
</body>
</html>
