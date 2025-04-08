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
    Route::get('/logout', 'logout')->name('logout');
});



Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');

Route::get('/admin', function () {
    return view('admin.index');
})->name('admin.index');
