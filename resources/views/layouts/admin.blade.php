<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- link bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!-- link fontawesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- estilos del panel de administración -->
    <link rel="stylesheet" href="{{ asset('css/admin/style.css') }}">
    <!-- estilos del login-->
    <link rel="stylesheet" href="">
    <title>@yield('title')</title>
    
    <!-- jQuery para AJAX -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

</head>
<body>
    <!-- Nueva navbar blanca con mensaje de bienvenida y botón de cerrar sesión -->
    <header class="bg-white py-2 shadow-sm">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-4"><!-- Espacio vacío a la izquierda --></div>
                <div class="col-4 text-center">
                    <h5 class="m-0" style="color: #999;">Bienvenido Administrador</h5>
                </div>
                <div class="col-4 text-end">
                    @auth
                    <span class="me-3" style="color: #666;">Usuario: {{ Auth::user()->email }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-dark">
                            <i class="fas fa-sign-out-alt me-1"></i> Cerrar Sesión
                        </button>
                    </form>
                    @endauth
                </div>
            </div>
        </div>
    </header>
    
    @yield('content')
    <!-- script de bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <!-- link sweetalert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Script para resaltar la opcion activa en el menu lateral -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener la URL actual
        const currentPath = window.location.pathname;
        
        // Seleccionar todos los links del menu lateral
        const sidebarLinks = document.querySelectorAll('.sidebar-menu a');
        
        // Recorrer los links y aplicar la clase active al correspondiente
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            // Comprobamos si la URL actual contiene la ruta del enlace (para que funcione tambien en subcarpetas)
            if (href && (currentPath.includes('/users') && href.includes('/users')) ||
                (currentPath.includes('/vehiculos') && href.includes('/vehiculos'))) {
                // Aplicar estilos de activo
                link.style.backgroundColor = 'rgba(255,255,255,0.3)';
                link.style.fontWeight = 'bold';
                link.style.borderRadius = '5px';
            }
        });
    });
    </script>
    
    @yield('scripts')
</body>
</html>