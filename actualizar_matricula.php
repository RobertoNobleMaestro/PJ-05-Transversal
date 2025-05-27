<?php
// Script para actualizar la matrícula del vehículo amortizado

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ID del vehículo que queremos actualizar
$vehiculo_id = 3;

// Matrícula a asignar (formato español típico)
$matricula = '1234 ABC';

try {
    // Actualizar el vehículo
    $vehiculo = \App\Models\Vehiculo::find($vehiculo_id);
    
    if (!$vehiculo) {
        echo "Error: Vehículo con ID {$vehiculo_id} no encontrado.\n";
        exit;
    }
    
    $vehiculo->matricula = $matricula;
    $vehiculo->save();
    
    echo "¡Matrícula actualizada correctamente!\n";
    echo "Vehículo ID {$vehiculo_id}: {$vehiculo->marca} {$vehiculo->modelo}, Matrícula: {$vehiculo->matricula}\n";
    
    // Verificar directamente en la base de datos
    $result = \Illuminate\Support\Facades\DB::table('vehiculos')
        ->select('id_vehiculos', 'marca', 'modelo', 'matricula')
        ->where('id_vehiculos', $vehiculo_id)
        ->first();
    
    echo "\nVerificación en la base de datos:\n";
    echo "ID: {$result->id_vehiculos}, Marca: {$result->marca}, Modelo: {$result->modelo}, Matrícula: {$result->matricula}\n";
    
} catch (\Exception $e) {
    echo "Error al actualizar la matrícula: " . $e->getMessage() . "\n";
}
