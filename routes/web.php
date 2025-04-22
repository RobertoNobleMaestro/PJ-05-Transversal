<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\HistorialController;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

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