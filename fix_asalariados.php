<?php

// Script para arreglar el problema de "No hay asalariados que coincidan con los filtros"
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Comprobación inicial
$totalAsalariados = DB::table('asalariados')->count();
$altaAsalariados = DB::table('asalariados')->where('estado', 'alta')->count();

echo "Estado actual:\n";
echo "Total de asalariados: $totalAsalariados\n";
echo "Asalariados en estado 'alta': $altaAsalariados\n\n";

// Si no hay asalariados, intentaremos arreglar los existentes o crear uno nuevo
if ($altaAsalariados == 0) {
    // 1. Primero intentemos actualizar los asalariados existentes si hay
    if ($totalAsalariados > 0) {
        echo "Actualizando asalariados existentes a estado 'alta'...\n";
        try {
            $updated = DB::table('asalariados')->update(['estado' => 'alta']);
            echo "Actualizado: $updated asalariados\n";
        } catch (\Exception $e) {
            echo "Error al actualizar: " . $e->getMessage() . "\n";
        }
    } else {
        // 2. Si no hay asalariados, intentamos crear uno básico
        echo "No hay asalariados. Intentando crear uno nuevo...\n";
        
        // Obtener un usuario existente
        $user = DB::table('users')->first();
        if (!$user) {
            echo "No hay usuarios en la base de datos. Imposible continuar.\n";
            exit(1);
        }
        
        try {
            // Datos mínimos para un asalariado (solo usar los campos que sabemos que existen)
            $asalariadoData = [
                'id_usuario' => $user->id_usuario,
                'salario' => 1500,
                'estado' => 'alta',
                'hiredate' => '2025-01-15', // Fecha en formato YYYY-MM-DD
                'dias_trabajados' => 20,
                'dia_cobro' => 15,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            
            // Insertar el asalariado
            $id = DB::table('asalariados')->insertGetId($asalariadoData);
            echo "Asalariado creado con ID: $id\n";
        } catch (\Exception $e) {
            echo "Error al crear asalariado: " . $e->getMessage() . "\n";
        }
    }
    
    // Verificación final
    $finalCount = DB::table('asalariados')->where('estado', 'alta')->count();
    echo "\nComprobación final:\n";
    echo "Asalariados en estado 'alta': $finalCount\n";
    
    if ($finalCount > 0) {
        echo "\n¡ARREGLADO! Ahora deberías poder ver asalariados en la interfaz.\n";
    } else {
        echo "\nNo se pudo resolver el problema automáticamente.\n";
    }
} else {
    echo "Ya tienes $altaAsalariados asalariados en estado 'alta'. No es necesario arreglar nada.\n";
}
