<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CarritoController;

Route::get('/ver-carrito', [CarritoController::class, 'index'])->middleware('auth');
Route::get('/carrito', function () {
    return view('carrito.index');
})->middleware('auth');

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas del login
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login.post');
});


Route::get('/perfil/{id}', [PerfilController::class, 'usuario'])->name('perfil');
Route::get('/perfil/{id}/datos', [PerfilController::class, 'obtenerDatos'])->name('perfil.datos');
Route::post('/perfil/{id}/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');
Route::post('/perfil/upload-foto', [PerfilController::class, 'uploadFoto'])->name('perfil.upload-foto');

Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');

Route::get('/admin', function () {
    return view('admin.index');
})->name('admin.index');

use App\Http\Controllers\VehiculoController;

Route::get('/vehiculo/detalle_vehiculo/{id}', [VehiculoController::class, 'detalle'])->name('vehiculo.detalle');

// Route::get('/vehiculo/{id}', [VehiculoController::class, 'detalle'])->name('vehiculo.detalle');