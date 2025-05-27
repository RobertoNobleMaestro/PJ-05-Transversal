<?php

// Este script actualiza todos los registros en la tabla asalariados
// para establecer el campo 'estado' a 'alta' si está vacío o es nulo

// Inicializar la aplicación Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Usar Eloquent para actualizar los registros
use Illuminate\Support\Facades\DB;

// Contar registros antes de la actualización
$countBefore = DB::table('asalariados')->count();
$countAlta = DB::table('asalariados')->where('estado', 'alta')->count();

echo "Registros totales antes de la actualización: " . $countBefore . "\n";
echo "Registros con estado 'alta' antes de la actualización: " . $countAlta . "\n";

// Actualizar registros donde estado es NULL o vacío
$updated = DB::table('asalariados')
    ->whereNull('estado')
    ->orWhere('estado', '')
    ->update(['estado' => 'alta']);

echo "Registros actualizados: " . $updated . "\n";

// Verificar después de la actualización
$countAfter = DB::table('asalariados')->where('estado', 'alta')->count();
echo "Registros con estado 'alta' después de la actualización: " . $countAfter . "\n";

echo "¡Actualización completada!\n";
