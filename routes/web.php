<?php

    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Facades\Auth;
    use App\Http\Controllers\AuthController;
    use App\Http\Controllers\HomeController;
    use App\Http\Controllers\PerfilController;
    use App\Http\Controllers\UserController;
    use App\Http\Controllers\VehiculoController;
    use App\Http\Controllers\CarritoController;

    // Rutas publicas
    Route::redirect('/', '/home');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home-stats', [HomeController::class, 'stats'])->name('home.stats');
    Route::get('/vehiculos', [HomeController::class, 'listado'])->name('home.listado');

    // Rutas Auth publicas
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'login')->name('login');
        Route::post('/login', 'loginProcess')->name('login.post');
        Route::get('/logout', 'logout')->name('logout');
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

    // API para valoraciones
    Route::get('/api/vehiculos/{id}/valoraciones', function($id) {
        $vehiculo = App\Models\Vehiculo::findOrFail($id);
        $valoraciones = $vehiculo->valoraciones()->with('usuario')->get();
        
        return response()->json($valoraciones);
    });
    Route::post('/vehiculos/{vehiculo}/añadir-al-carrito', [VehiculoController::class, 'añadirAlCarrito']);
