<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ReservaCrudController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\FacturaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GestorController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VehiculoCrudController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ChatIAController;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/home-stats', [HomeController::class, 'stats'])->name('home.stats');
Route::get('/vehiculos', [HomeController::class, 'listado'])->name('home.listado');
Route::get('/vehiculos/año', [HomeController::class, 'obtenerAño']);
Route::get('/vehiculos/ciudades', [HomeController::class, 'obtenerCiudades']);

// Login con Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// Autenticación manual
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login.post');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerProcess')->name('register.post');
});

// Webhook de Stripe (público)
Route::post('/webhook/stripe', [PagoController::class, 'webhook'])->name('webhook.stripe');


Route::middleware(['auth', 'role:cliente'])->group(function () {

    // Perfil
    Route::get('/perfil/{id}', [PerfilController::class, 'usuario'])->name('perfil');
    Route::get('/perfil/{id}/datos', [PerfilController::class, 'obtenerDatos'])->name('perfil.datos');
    Route::post('/perfil/{id}/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');
    Route::post('/perfil/upload-foto', [PerfilController::class, 'uploadFoto'])->name('perfil.upload-foto');

    Route::get('/perfil-imagen', function () {
        $user = Auth::user();
        return response()->json([
            'foto' => $user->foto_perfil ? asset('img/' . $user->foto_perfil) : asset('img/default.png')
        ]);
    })->name('perfil.imagen');

    // Carrito
    Route::get('/carrito', fn() => view('carrito.index'))->name('carrito');
    Route::get('/ver-carrito', [CarritoController::class, 'index'])->name('carrito.ver');
    Route::delete('/eliminar-reserva/{id}', [CarritoController::class, 'eliminarReserva'])->name('eliminar.reserva');

    // Reservas y vehículos
    Route::post('/reservas', [ReservaController::class, 'crearReserva']);
    Route::get('/vehiculos/{id}/reservas', [ReservaController::class, 'reservasPorVehiculo']);
    Route::get('/vehiculo/detalle_vehiculo/{id}', [VehiculoController::class, 'detalle'])->name('vehiculo.detalle');
    Route::post('/vehiculos/{vehiculo}/añadir-al-carrito', [VehiculoController::class, 'añadirAlCarrito']);

    // Valoraciones
    Route::get('/api/vehiculos/{id}/valoraciones', function ($id) {
        $vehiculo = App\Models\Vehiculo::findOrFail($id);
        return $vehiculo->valoraciones()->with('usuario')->get();
    });

    // Pagos y facturas
    Route::get('/finalizar-compra', [PagoController::class, 'checkout'])->name('pago.checkout');
    Route::post('/pago/procesar', [PagoController::class, 'procesar'])->name('pago.procesar');
    Route::get('/pago/exito/{id_reserva}', [PagoController::class, 'exito'])->name('pago.exito');
    Route::get('/pago/cancelado', [PagoController::class, 'cancelado'])->name('pago.cancelado');
    Route::get('/facturas/descargar/{id_reserva}', [FacturaController::class, 'descargarFactura'])->name('facturas.descargar');
});

Route::middleware(['auth', 'role:gestor'])->group(function () {

    Route::get('/gestor', [GestorController::class, 'dashboard'])->name('gestor.index');

    Route::prefix('gestor/vehiculos')->group(function () {
        Route::get('/', [VehiculoCrudController::class, 'index'])->name('gestor.vehiculos');
        Route::get('/data', [VehiculoCrudController::class, 'getVehiculos'])->name('gestor.vehiculos.data');
        Route::get('/create', [VehiculoCrudController::class, 'create'])->name('gestor.vehiculos.create');
        Route::post('/', [VehiculoCrudController::class, 'store'])->name('gestor.vehiculos.store');
        Route::get('/{id_vehiculos}/edit', [VehiculoCrudController::class, 'edit'])->name('gestor.vehiculos.edit');
        Route::post('/{id_vehiculos}', [VehiculoCrudController::class, 'update'])->name('gestor.vehiculos.update');
        Route::delete('/{id_vehiculos}', [VehiculoCrudController::class, 'destroy'])->name('gestor.vehiculos.destroy');
    });
});

Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

    // Usuarios
    Route::prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.users');
        Route::get('/data', [UserController::class, 'getUsers'])->name('admin.users.data');
        Route::get('/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/', [UserController::class, 'store'])->name('admin.users.store');
        Route::get('/{id_usuario}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::post('/{id_usuario}', [UserController::class, 'update'])->name('admin.users.update');
        Route::delete('/{id_usuario}', [UserController::class, 'destroy'])->name('admin.users.destroy');
    });

    // Lugares
    Route::prefix('admin/lugares')->group(function () {
        Route::get('/', [LugarController::class, 'index'])->name('admin.lugares');
        Route::get('/data', [LugarController::class, 'getLugares'])->name('admin.lugares.data');
        Route::get('/create', [LugarController::class, 'create'])->name('admin.lugares.create');
        Route::post('/', [LugarController::class, 'store'])->name('admin.lugares.store');
        Route::get('/{id_lugar}/edit', [LugarController::class, 'edit'])->name('admin.lugares.edit');
        Route::put('/{id_lugar}', [LugarController::class, 'update'])->name('admin.lugares.update');
        Route::delete('/{id_lugar}', [LugarController::class, 'destroy'])->name('admin.lugares.destroy');
    });

    // Reservas
    Route::prefix('admin/reservas')->group(function () {
        Route::get('/', [ReservaCrudController::class, 'index'])->name('admin.reservas.index');
        Route::get('/data', [ReservaCrudController::class, 'getReservas'])->name('admin.reservas.data');
        Route::get('/create', [ReservaCrudController::class, 'create'])->name('admin.reservas.create');
        Route::post('/', [ReservaCrudController::class, 'store'])->name('admin.reservas.store');
        Route::get('/{id_reservas}/edit', [ReservaCrudController::class, 'edit'])->name('admin.reservas.edit');
        Route::post('/{id_reservas}', [ReservaCrudController::class, 'update'])->name('admin.reservas.update');
        Route::delete('/{id_reservas}', [ReservaCrudController::class, 'destroy'])->name('admin.reservas.destroy');
        Route::get('/{id_reserva}', [ReservaCrudController::class, 'getReservaDetails'])->name('admin.reservas.details');
    });

    // Historial
    Route::get('/admin/historial', [ReservaCrudController::class, 'historial'])->name('admin.historial');
    Route::get('/admin/historial/data', [ReservaCrudController::class, 'getHistorialData'])->name('admin.historial.data');
});

Route::post('/chat/send', [ChatIAController::class, 'send'])->name('chat.send');
