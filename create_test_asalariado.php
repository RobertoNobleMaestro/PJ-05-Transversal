<?php

// Este script crea un asalariado de prueba para solucionar el problema de "No hay asalariados que coincidan con los filtros"

// Inicializar la aplicación Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

// 1. Comprobar la estructura de la tabla asalariados
echo "Estructura de la tabla asalariados:\n";
$columns = Schema::getColumnListing('asalariados');
print_r($columns);

// 2. Verificar usuarios disponibles
echo "\nUsuarios disponibles:\n";
$users = DB::table('users')->where('id_roles', '!=', 1)->get(['id_usuario', 'nombre', 'email']);
if ($users->isEmpty()) {
    echo "No hay usuarios disponibles para asignar como asalariados.\n";
    
    // Crear un usuario si no existe ninguno
    $userId = DB::table('users')->insertGetId([
        'nombre' => 'Usuario Prueba',
        'email' => 'prueba@carflow.com',
        'password' => bcrypt('password'),
        'id_roles' => 2, // Ajustar según tus roles
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);
    echo "Usuario creado con ID: $userId\n";
} else {
    foreach ($users as $user) {
        echo "ID: {$user->id_usuario}, Nombre: {$user->nombre}, Email: {$user->email}\n";
    }
    $userId = $users->first()->id_usuario;
}

// 3. Verificar lugares disponibles
echo "\nLugares disponibles:\n";
$lugares = DB::table('lugares')->get(['id_lugar', 'nombre']);
if ($lugares->isEmpty()) {
    echo "No hay lugares disponibles.\n";
    
    // Crear un lugar si no existe ninguno
    $lugarId = DB::table('lugares')->insertGetId([
        'nombre' => 'Sede Central',
        'direccion' => 'Calle Principal 123',
        'latitud' => 40.416775,
        'longitud' => -3.703790,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ]);
    echo "Lugar creado con ID: $lugarId\n";
} else {
    foreach ($lugares as $lugar) {
        echo "ID: {$lugar->id_lugar}, Nombre: {$lugar->nombre}\n";
    }
    $lugarId = $lugares->first()->id_lugar;
}

// 4. Verificar parkings disponibles
echo "\nParkings disponibles:\n";
$parkings = DB::table('parking')->get(['id', 'nombre']);
$parkingId = null;

if ($parkings->isEmpty()) {
    echo "No hay parkings disponibles. Intentando crear uno...\n";
    
    try {
        // Comprobar estructura de la tabla parking
        echo "Estructura de la tabla parking:\n";
        $parkingColumns = Schema::getColumnListing('parking');
        print_r($parkingColumns);
        
        // Intentar crear un parking con sólo los campos obligatorios
        $parkingData = [
            'nombre' => 'Parking Central',
            'plazas_totales' => 30,
            'plazas_ocupadas' => 0,
        ];
        
        // Añadir id_lugar si existe la columna
        if (in_array('id_lugar', $parkingColumns)) {
            $parkingData['id_lugar'] = $lugarId;
        }
        
        // Añadir timestamps si existen
        if (in_array('created_at', $parkingColumns)) {
            $parkingData['created_at'] = Carbon::now();
        }
        if (in_array('updated_at', $parkingColumns)) {
            $parkingData['updated_at'] = Carbon::now();
        }
        
        // Intentar insertar con los campos mínimos
        $parkingId = DB::table('parking')->insertGetId($parkingData);
        echo "Parking creado con ID: $parkingId\n";
    } catch (\Exception $e) {
        echo "Error al crear parking: " . $e->getMessage() . "\n";
    }
} else {
    foreach ($parkings as $parking) {
        echo "ID: {$parking->id}, Nombre: {$parking->nombre}\n";
    }
    $parkingId = $parkings->first()->id;
}

// 5. Verificar si ya existe un asalariado para este usuario
$existingAsalariado = DB::table('asalariados')
    ->where('id_usuario', $userId)
    ->first();

if ($existingAsalariado) {
    echo "\nYa existe un asalariado para el usuario $userId\n";
    
    // Asegurar que el estado está en 'alta'
    DB::table('asalariados')
        ->where('id', $existingAsalariado->id)
        ->update(['estado' => 'alta']);
    
    echo "Estado actualizado a 'alta'\n";
} else {
    echo "\nCreando nuevo asalariado...\n";
    
    try {
        // Preparar datos básicos para el asalariado
        $asalariadoData = [
            'id_usuario' => $userId,
            'salario' => 1500,
            'estado' => 'alta',
            'dias_trabajados' => 20,
        ];
        
        // Añadir campos adicionales según la estructura
        if (in_array('dia_cobro', $columns)) {
            $asalariadoData['dia_cobro'] = 5;
        }
        
        if (in_array('hiredate', $columns)) {
            $asalariadoData['hiredate'] = Carbon::now()->subMonths(2);
        }
        
        if (in_array('id_lugar', $columns) && $lugarId) {
            $asalariadoData['id_lugar'] = $lugarId;
        }
        
        if (in_array('parking_id', $columns) && $parkingId) {
            $asalariadoData['parking_id'] = $parkingId;
        }
        
        // Añadir timestamps si existen
        if (in_array('created_at', $columns)) {
            $asalariadoData['created_at'] = Carbon::now();
        }
        if (in_array('updated_at', $columns)) {
            $asalariadoData['updated_at'] = Carbon::now();
        }
        
        // Insertar el nuevo asalariado
        $asalariadoId = DB::table('asalariados')->insertGetId($asalariadoData);
        echo "Asalariado creado con ID: $asalariadoId\n";
    } catch (\Exception $e) {
        echo "Error al crear asalariado: " . $e->getMessage() . "\n";
    }
}

// 6. Verificar el número total de asalariados
$totalAsalariados = DB::table('asalariados')->count();
$altaAsalariados = DB::table('asalariados')->where('estado', 'alta')->count();

echo "\nResumen final:\n";
echo "Total de asalariados: $totalAsalariados\n";
echo "Asalariados en estado 'alta': $altaAsalariados\n";

echo "\n¡Proceso completado! Ahora deberías poder ver los asalariados en la interfaz.\n";
