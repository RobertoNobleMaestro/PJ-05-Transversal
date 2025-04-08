<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;

Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Rutas del login
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login.post');
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/admin', [\App\Http\Controllers\AdminController::class, 'index'])->name('admin.index');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::get('/admin/users/{id_usuario}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/admin/users/{id_usuario}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{id_usuario}', [UserController::class, 'destroy'])->name('admin.users.destroy');
});
