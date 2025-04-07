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
});



Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');

Route::get('/admin', function () {
    return view('admin.index');
})->name('admin.index');
