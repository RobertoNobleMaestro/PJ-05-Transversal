<?php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\HistorialController;

    // Rutas publicas
    Route::redirect('/', '/home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home-stats', [HomeController::class, 'stats'])->name('home.stats');
    Route::get('/vehiculos', [HomeController::class, 'listado'])->name('home.listado');
    Route::get('/vehiculos/año', [HomeController::class, 'obtenerAño']);
    Route::get('/vehiculos/ciudades', [HomeController::class, 'obtenerCiudades']);

    // Rutas Auth publicas
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'loginProcess')->name('login.post');
        Route::get('/logout', 'logout')->name('logout');
        Route::get('/register', 'register')->name('register');
        Route::post('/register', 'registerProcess')->name('register.post');
    });

    // Auth
    Route::middleware('auth')->group(function () {

        // Perfil
        Route::get('/perfil/{id}', [PerfilController::class, 'usuario'])->name('perfil');
        Route::get('/perfil/{id}/datos', [PerfilController::class, 'obtenerDatos'])->name('perfil.datos');
        Route::post('/perfil/{id}/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');
        Route::post('/perfil/upload-foto', [PerfilController::class, 'uploadFoto'])->name('perfil.upload-foto');

        // ActualizaciÃ³n imagen de perfil /home
        Route::get('/perfil-imagen', function () {
            $user = Auth::user();
            return response()->json([
                'foto' => $user->foto_perfil ? asset('img/' . $user->foto_perfil) : asset('img/default.png')
            ]);
        })->name('perfil.imagen');

        // Carrito
        Route::get('/carrito', fn () => view('carrito.index'))->name('carrito');
        Route::get('/ver-carrito', [CarritoController::class, 'index'])->name('carrito.ver');
    });

    // Admin
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin', fn () => view('admin.index'))->name('admin.index');

    // Vehiculos
    Route::get('/vehiculo/detalle_vehiculo/{id}', [VehiculoController::class, 'detalle'])->name('vehiculo.detalle');
    Route::get('/vehiculos/{id}/reservas', [ReservaController::class, 'reservasPorVehiculo']);
    Route::post('/reservas', [ReservaController::class, 'crearReserva']);
    // API para valoraciones
    Route::get('/api/vehiculos/{id}/valoraciones', function($id) {
        $vehiculo = App\Models\Vehiculo::findOrFail($id);
        $valoraciones = $vehiculo->valoraciones()->with('usuario')->get();
        
        return response()->json($valoraciones);
    });
    Route::post('/vehiculos/{vehiculo}/añadir-al-carrito', [VehiculoController::class, 'añadirAlCarrito']);

    // Rutas de pago (dentro del middleware 'auth')
    Route::get('/finalizar-compra', [PagoController::class, 'checkout'])->name('pago.checkout');
    Route::get('/pago/exito/{id_reserva}', [PagoController::class, 'exito'])->name('pago.exito');
    Route::get('/pago/cancelado', [PagoController::class, 'cancelado'])->name('pago.cancelado');

    // Facturas
    Route::get('/facturas/descargar/{id_reserva}', [FacturaController::class, 'descargarFactura'])->name('facturas.descargar')->middleware('auth');

    // Webhook de Stripe (ruta pública)
    Route::post('/webhook/stripe', [PagoController::class, 'webhook'])->name('webhook.stripe');

    // Eliminar reserva
    Route::delete('/eliminar-reserva/{id}', [CarritoController::class, 'eliminarReserva'])->name('eliminar.reserva');
// Rutas del login
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {
    // Panel principal de administración
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    
    // CRUD de usuarios
    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/data', [UserController::class, 'getUsers'])->name('admin.users.data'); // Ruta para AJAX
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id_usuario}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users/{id_usuario}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id_usuario}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    
    // CRUD de vehículos
    Route::get('/admin/vehiculos', [VehiculoController::class, 'index'])->name('admin.vehiculos');
    Route::get('/admin/vehiculos/data', [VehiculoController::class, 'getVehiculos'])->name('admin.vehiculos.data'); // Ruta para AJAX
    Route::get('/admin/vehiculos/create', [VehiculoController::class, 'create'])->name('admin.vehiculos.create');
    Route::post('/admin/vehiculos', [VehiculoController::class, 'store'])->name('admin.vehiculos.store');
    Route::get('/admin/vehiculos/{id_vehiculos}/edit', [VehiculoController::class, 'edit'])->name('admin.vehiculos.edit');
    Route::post('/admin/vehiculos/{id_vehiculos}', [VehiculoController::class, 'update'])->name('admin.vehiculos.update');
    Route::delete('/admin/vehiculos/{id_vehiculos}', [VehiculoController::class, 'destroy'])->name('admin.vehiculos.destroy');
    
    // CRUD de lugares
    Route::get('/admin/lugares', [LugarController::class, 'index'])->name('admin.lugares');
    Route::get('/admin/lugares/data', [LugarController::class, 'getLugares'])->name('admin.lugares.data'); // Ruta para AJAX
    Route::get('/admin/lugares/create', [LugarController::class, 'create'])->name('admin.lugares.create');
    Route::post('/admin/lugares', [LugarController::class, 'store'])->name('admin.lugares.store');
    Route::get('/admin/lugares/{id_lugar}/edit', [LugarController::class, 'edit'])->name('admin.lugares.edit');
    Route::put('/admin/lugares/{id_lugar}', [LugarController::class, 'update'])->name('admin.lugares.update');
    Route::delete('/admin/lugares/{id_lugar}', [LugarController::class, 'destroy'])->name('admin.lugares.destroy');
    
    // CRUD de reservas
    Route::get('/admin/reservas', [ReservaController::class, 'index'])->name('admin.reservas');
    Route::get('/admin/reservas/data', [ReservaController::class, 'getReservas'])->name('admin.reservas.data'); // Ruta para AJAX
    Route::get('/admin/reservas/create', [ReservaController::class, 'create'])->name('admin.reservas.create');
    Route::post('/admin/reservas', [ReservaController::class, 'store'])->name('admin.reservas.store');
    Route::get('/admin/reservas/{id_reservas}/edit', [ReservaController::class, 'edit'])->name('admin.reservas.edit');
    Route::put('/admin/reservas/{id_reservas}', [ReservaController::class, 'update'])->name('admin.reservas.update');
    Route::delete('/admin/reservas/{id_reservas}', [ReservaController::class, 'destroy'])->name('admin.reservas.destroy');
    
    // Historial de Reservas
    Route::get('/admin/historial', [HistorialController::class, 'index'])->name('admin.historial');
    Route::get('/admin/historial/data', [HistorialController::class, 'getData'])->name('admin.historial.data'); // Ruta para AJAX
    Route::get('/admin/historial/reportes', [HistorialController::class, 'reportes'])->name('admin.historial.reportes');
});