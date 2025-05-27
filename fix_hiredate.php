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
    
    // Crear un asalariado con los campos correctos, tratando hiredate como un entero
    // que representa el día del mes (similar a dia_cobro)
    $asalariadoId = DB::table('asalariados')->insertGetId([
        'id_usuario' => $user->id_usuario,
        'salario' => 1500,
        'dia_cobro' => 5, // día del mes para cobro
        'hiredate' => 15, // día del mes como entero, no como fecha
        'estado' => 'alta',
        'dias_trabajados' => 20,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    
    echo "¡Éxito! Asalariado creado con ID: $asalariadoId\n";
    
    // Verificar el estado final
    $countAlta = DB::table('asalariados')->where('estado', 'alta')->count();
    echo "Asalariados en estado 'alta': $countAlta\n";
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
