<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PerfilController;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');


// Rutas del login
Route::get('/login' , [AuthController::class, 'login'])->name('login');


Route::get('/perfil/{id}', [PerfilController::class, 'usuario'])->name('perfil');
Route::get('/perfil/{id}/datos', [PerfilController::class, 'obtenerDatos'])->name('perfil.datos');
Route::post('/perfil/{id}/actualizar', [PerfilController::class, 'actualizar'])->name('perfil.actualizar');
