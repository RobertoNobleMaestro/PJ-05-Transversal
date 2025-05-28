// Rutas para mensajes de grupo
Route::middleware('auth')->group(function () {
    Route::post('/chofers/mensajes/enviar', [GrupoMensajeController::class, 'enviar']);
    Route::post('/chofers/mensajes', [GrupoMensajeController::class, 'obtener']);
}); 