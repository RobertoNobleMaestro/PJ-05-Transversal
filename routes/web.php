<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ValoracionController;
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
use App\Http\Controllers\GestorUserController;
use App\Http\Controllers\VehiculoController;
use App\Http\Controllers\VehiculoCrudController;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ChatViewController;
use App\Http\Controllers\TallerController;
use App\Http\Controllers\HistorialGestorController;
use App\Http\Controllers\ParkingGestorController;
use App\Http\Controllers\ChatIAController;
use App\Http\Controllers\AdminFinancieroController;
use App\Http\Controllers\AsalariadoController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\ChoferController;
use Illuminate\Support\Facades\Schema;


Route::redirect('/', '/home');

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/home-stats', [HomeController::class, 'stats'])->name('home.stats');
Route::get('/vehiculos', [HomeController::class, 'listado'])->name('home.listado');
Route::get('/vehiculos/año', [HomeController::class, 'obtenerAño']);
Route::get('/vehiculos/ciudades', [HomeController::class, 'obtenerCiudades']);



// Login con Google
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// AutenticaciÃ³n manual
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'login')->name('login');
    Route::post('/login', 'loginProcess')->name('login.post');
    Route::get('/logout', 'logout')->name('logout');
    Route::get('/register', 'register')->name('register');
    Route::post('/register', 'registerProcess')->name('register.post');
});

// Rutas de recuperaciÃ³n de contraseÃ±a
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');

// Webhook de Stripe (pÃºblico)
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
    Route::get('/carrito/count', [CarritoController::class, 'getCartCount'])->name('carrito.count');
    Route::delete('/eliminar-reserva/{id}', [CarritoController::class, 'eliminarReserva'])->name('eliminar.reserva');

    // Reservas y vehÃ­culos
    Route::post('/reservas', [ReservaController::class, 'crearReserva']);
    Route::get('/vehiculos/{id}/reservas', [ReservaController::class, 'reservasPorVehiculo']);
    Route::get('/vehiculo/detalle_vehiculo/{id}', [VehiculoController::class, 'detalle'])->name('vehiculo.detalle');
    Route::post('/vehiculos/{vehiculo}/añadir-al-carrito', [VehiculoController::class, 'añadirAlCarrito']);

    // Mostrar mapa 
    Route::get('/vehiculo/detalle_vehiculo/{id}', [VehiculoController::class, 'showMapa']);

    // Valoraciones
    Route::get('/api/vehiculos/{id}/valoraciones', function ($id) {
        $vehiculo = App\Models\Vehiculo::findOrFail($id);
        return $vehiculo->valoraciones()->with('usuario')->get();
    });
    Route::post('/valoraciones', [ValoracionController::class, 'store'])->middleware('auth');
    Route::put('/valoraciones/editar/{id}', [ValoracionController::class, 'update'])->middleware('auth');
    Route::get('/valoraciones/{id}', [ValoracionController::class, 'show'])->middleware('auth');
    Route::delete('/valoraciones/{id}', [ValoracionController::class, 'destroy'])->middleware('auth');
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
    Route::get('gestor/vehiculos/{id}/crudreservas', [VehiculoCrudController::class, 'getReservas']);
    Route::get('/gestor/historial', [HistorialGestorController::class, 'historial'])->name('gestor.historial');
    Route::get('/gestor/historial/data', [HistorialGestorController::class, 'getHistorialData'])->name('gestor.historial.data');
    Route::prefix('gestor')->middleware('auth')->group(function () {
    Route::get('/parking', [ParkingGestorController::class, 'index'])->name('gestor.parking.index');
    Route::put('/parking/{id}', [ParkingGestorController::class, 'update'])->name('gestor.parking.update');
    Route::delete('/parking/{id}', [ParkingGestorController::class, 'destroy'])->name('gestor.parking.destroy');
});

});


// Rutas para el espacio privado de los chofers 
Route::middleware(['auth', 'role:chofer'])->group(function(){
    Route::get('/chofers', [ChoferController::class, 'dashboard'])->name('chofers.dashboard');
    
    // Chat por grupo
    Route::get('/chofers/chat', [ChoferController::class, 'showChatView'])->name('chofers.chat');
    Route::post('/chofers/grupos', [ChoferController::class, 'storeGrupo'])->name('chofers.grupos.store');
    
    // Endpoints AJAX para el chat por grupo
    Route::get('/api/chofers/grupos', [ChoferController::class, 'getUserGrupos'])->name('api.chofers.grupos');
    Route::post('/api/chofers/mensajes', [ChoferController::class, 'getGrupoMensajes'])->name('api.chofers.mensajes');
    Route::post('/api/chofers/mensajes/enviar', [ChoferController::class, 'sendGrupoMensaje'])->name('api.chofers.mensajes.enviar');
    Route::get('/api/chofers/sede/{sede}', [ChoferController::class, 'getChoferesPorSede'])->name('api.chofers.sede');
});

// Ruta para la solicitud de transporte privado (cliente)
Route::get('/solicitar-chofer', [ChoferController::class, 'pideCoche'])->name('chofers.cliente-pide');

// Ruta de depuración para el chat (solo para desarrollo)
Route::get('/debug/chat', function() {
    if (!Auth::check()) {
        return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder a esta página');
    }
    
    $usuario = Auth::user();
    
    // Verificar si hay grupos
    $grupos = DB::table('grupo_usuario')
        ->where('id_usuario', $usuario->id_usuario)
        ->join('grupos', 'grupo_usuario.grupo_id', '=', 'grupos.id')
        ->select('grupos.*')
        ->get();
        
    return response()->json([
        'usuario' => [
            'id' => $usuario->id_usuario,
            'nombre' => $usuario->nombre,
            'email' => $usuario->email,
            'rol' => $usuario->id_roles
        ],
        'grupos' => $grupos,
        'estado_tabla' => [
            'grupo_usuario_exists' => Schema::hasTable('grupo_usuario'),
            'grupos_exists' => Schema::hasTable('grupos'),
            'messages_exists' => Schema::hasTable('messages')
        ]
    ]);
});

// Rutas para el administrador financiero
Route::middleware(['auth', 'role:admin_financiero'])->group(function () {
    Route::get('/admin-financiero', [AsalariadoController::class, 'index'])->name('admin.financiero');
    Route::get('/admin-financiero/resumen', [AsalariadoController::class, 'index'])->name('admin.financiero.resumen');
    
    // Rutas para la gestión de asalariados
    Route::prefix('asalariados')->group(function () {
        Route::get('/', [AsalariadoController::class, 'index'])->name('asalariados.index');
        Route::get('/data', [AsalariadoController::class, 'getAsalariados'])->name('asalariados.data');
        Route::get('/{id}/editar', [AsalariadoController::class, 'edit'])->name('asalariados.edit');
        Route::match(['post', 'put'], '/{id}/update', [AsalariadoController::class, 'update'])->name('asalariados.update');
        Route::post('/{id}/update-ajax', [AsalariadoController::class, 'updateAjax'])->name('asalariados.update.ajax');
        Route::get('/{id}/detalle', [AsalariadoController::class, 'show'])->name('asalariados.show');
        Route::get('/{id}/ficha-salarial', [AsalariadoController::class, 'descargarFichaSalarial'])->name('asalariados.ficha.salarial');
    });
    // Nuevo sistema de reportes financieros - COMENTADO
    // Route::get('/financial/dashboard', [FinancialReportController::class, 'dashboard'])->name('financial.dashboard');
    // Route::get('/financial/vehiculos', [FinancialReportController::class, 'vehiculosRentabilidad'])->name('financial.vehiculos');
    // Route::get('/financial/proyecciones', [FinancialReportController::class, 'proyecciones'])->name('financial.proyecciones');
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
    });

    // Historial
    Route::get('/admin/historial', [ReservaCrudController::class, 'historial'])->name('admin.historial');
    Route::get('/admin/historial/data', [ReservaCrudController::class, 'getHistorialData'])->name('admin.historial.data');
});
// Chat routes
Route::middleware(['auth'])->group(function () {
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
});
Route::middleware(['auth'])->get('/chat', [ChatViewController::class, 'index'])->name('chat.index');

Route::middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/gestor/chats', [ChatViewController::class, 'listarConversaciones'])->name('gestor.chat.listar');
    Route::get('/gestor/chats/{id_usuario}', [ChatViewController::class, 'verConversacion'])->name('gestor.chat.conversacion');
    Route::delete('/gestor/chats/mensaje/{id}', [ChatViewController::class, 'eliminarMensaje'])->name('gestor.chat.delete');
    Route::prefix('gestor')->middleware(['auth', 'role:gestor'])->group(function () {
    Route::get('/users', [GestorUserController::class, 'index'])->name('gestor.user.index');
    Route::get('/users/data', [GestorUserController::class, 'getUsers'])->name('gestor.user.data');
    Route::get('/users/create', [GestorUserController::class, 'create'])->name('gestor.user.create');
    Route::post('/users', [GestorUserController::class, 'store'])->name('gestor.user.store');
    Route::get('/users/{id}/edit', [GestorUserController::class, 'edit'])->name('gestor.user.edit');
    Route::post('/users/{id}', [GestorUserController::class, 'update'])->name('gestor.user.update');
    Route::delete('/users/{id}', [GestorUserController::class, 'destroy'])->name('gestor.user.destroy');
    });
});
Route::get('/chat/stream/{id_usuario}', [ChatController::class, 'stream'])->middleware('auth');

// Route::post('/chat/send', [ChatIAController::class, 'send'])->name('chat.send2');



Route::get('/run-migrations-safe', function () {
    // Verifica la clave proporcionada
    if (request('key') !== env('DEPLOY_KEY')) {
        abort(403, 'Acceso no autorizado');
    }

    try {
        // Ejecuta las migraciones
        Artisan::call('migrate:fresh --seed --force');
        $output = Artisan::output();

        return response()->json([
            'message' => 'Migraciones ejecutadas correctamente',
            'output' => $output
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => 'Error al ejecutar migraciones: ' . $e->getMessage()
        ], 500);
    }
});



// Rutas para el espacio privado de los mecánicos
// Route::middleware(['auth', 'role:mecanico'])->group(function () {
    Route::get('/taller', [TallerController::class, 'index'])->name('Taller.index');
Route::post('/taller/filtrar', [TallerController::class, 'filtrarVehiculos'])->name('Taller.filtrar');
    Route::get('/taller/historial', [TallerController::class, 'historial'])->name('Taller.historial');

    Route::get('/taller/mantenimientos', [TallerController::class, 'getMantenimientos'])->name('Taller.mantenimientos');
    Route::get('/taller/mantenimiento/{id}', [TallerController::class, 'getDetalleMantenimiento'])->name('Taller.mantenimiento.detalle');
    Route::put('/taller/mantenimiento/{id}/estado', [TallerController::class, 'actualizarEstadoMantenimiento'])->name('Taller.mantenimiento.actualizar-estado');
    Route::post('/taller/agendar-mantenimiento', [TallerController::class, 'agendarMantenimiento'])->name('Taller.agendar');
    Route::get('/taller/horarios-disponibles', [TallerController::class, 'getHorariosDisponibles'])->name('Taller.horarios');
    Route::get('/taller/getMantenimientos', [TallerController::class, 'getMantenimientos'])->name('Taller.getMantenimientos');

    Route::get('/taller/{id}/edit', [TallerController::class, 'edit'])->name('Taller.edit');
    Route::put('/taller/{id}', [TallerController::class, 'update'])->name('Taller.update');
    Route::delete('/taller/{id}', [TallerController::class, 'destroy'])->name('Taller.destroy');
    
// });
Route::get('/taller/factura/{id}', [TallerController::class, 'descargarFactura'])->name('Taller.factura');
