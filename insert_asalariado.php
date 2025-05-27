<?php

// Inicializar la aplicación Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

try {
    // Verificar usuarios disponibles
    $user = DB::table('users')->first();
    if (!$user) {
        echo "Error: No hay usuarios disponibles en la base de datos.\n";
        exit(1);
    }
    
    echo "Utilizando usuario ID: {$user->id_usuario}\n";
    
    // Crear un asalariado con los campos mínimos
    // Importante: Formato de fecha correcto para MySQL (YYYY-MM-DD)
    $asalariadoId = DB::table('asalariados')->insertGetId([
        'id_usuario' => $user->id_usuario,
        'salario' => 1500,
        'dia_cobro' => 5,
        'hiredate' => '2024-01-15', // Fecha pasada en formato correcto
        'estado' => 'alta',
        'dias_trabajados' => 20,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "¡Éxito! Asalariado creado con ID: $asalariadoId\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Proporcionar más información sobre la estructura de la tabla
    echo "\nEstructura de la tabla 'asalariados':\n";
    $columns = DB::select("SHOW COLUMNS FROM asalariados");
    foreach ($columns as $column) {
        echo "{$column->Field}: {$column->Type}\n";
    }
}
