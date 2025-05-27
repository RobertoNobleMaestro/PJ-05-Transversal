<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

echo "=========== SOLUCIONANDO PROBLEMA DE SOLICITUDES ===========\n\n";

// 1. Verificamos si el problema podría estar relacionado con las migraciones
$migrationExists = DB::table('migrations')
    ->where('migration', 'like', '%modify_solicitudes_table%')
    ->exists();

if ($migrationExists) {
    echo "Migración 'modify_solicitudes_table' encontrada en la base de datos.\n";
    echo "Eliminando la migración para evitar restricciones de null en id_cliente...\n";
    
    // Eliminar la migración de la tabla migrations
    DB::table('migrations')
        ->where('migration', 'like', '%modify_solicitudes_table%')
        ->delete();
    
    echo "Migración eliminada de la tabla migrations.\n\n";
} else {
    echo "Migración 'modify_solicitudes_table' no encontrada en la base de datos.\n\n";
}

// 2. Verificar si hay algún problema con las consultas a través de Eloquent
echo "Revisando y corrigiendo consultas Eloquent en ChoferController...\n";

// Obtener archivos del controlador
$choferControllerPath = __DIR__.'/app/Http/Controllers/ChoferController.php';
$solicitudControllerPath = __DIR__.'/app/Http/Controllers/SolicitudController.php';

// Verificar y corregir ChoferController
if (file_exists($choferControllerPath)) {
    $content = file_get_contents($choferControllerPath);
    
    // Reemplazar consultas que puedan tener problemas de ambigüedad
    $newContent = str_replace(
        "->where('estado', 'pendiente')",
        "->where('solicitudes.estado', 'pendiente')",
        $content
    );
    
    $newContent = str_replace(
        "->orderBy('created_at', 'desc')",
        "->orderBy('solicitudes.created_at', 'desc')",
        $newContent
    );
    
    if ($content !== $newContent) {
        file_put_contents($choferControllerPath, $newContent);
        echo "ChoferController actualizado con referencias explícitas a la tabla 'solicitudes'.\n";
    } else {
        echo "No se requieren cambios en ChoferController.\n";
    }
} else {
    echo "ERROR: No se encontró el archivo ChoferController.\n";
}

// Verificar y corregir SolicitudController
if (file_exists($solicitudControllerPath)) {
    $content = file_get_contents($solicitudControllerPath);
    
    // Reemplazar consultas que puedan tener problemas de ambigüedad
    $newContent = str_replace(
        "->where('estado', 'pendiente')",
        "->where('solicitudes.estado', 'pendiente')",
        $content
    );
    
    $newContent = str_replace(
        "->orderBy('created_at', 'desc')",
        "->orderBy('solicitudes.created_at', 'desc')",
        $newContent
    );
    
    if ($content !== $newContent) {
        file_put_contents($solicitudControllerPath, $newContent);
        echo "SolicitudController actualizado con referencias explícitas a la tabla 'solicitudes'.\n";
    } else {
        echo "No se requieren cambios en SolicitudController.\n";
    }
} else {
    echo "ERROR: No se encontró el archivo SolicitudController.\n";
}

echo "\n";

// 3. Asegurarse de que las solicitudes pendientes existan
echo "Verificando solicitudes pendientes para choferes...\n";

// Obtener todos los choferes
$choferes = DB::table('choferes')->select('id')->get();
$countCreadas = 0;

foreach ($choferes as $chofer) {
    // Verificar si el chofer ya tiene solicitudes pendientes
    $solicitudesPendientes = DB::table('solicitudes')
        ->where('id_chofer', $chofer->id)
        ->where('solicitudes.estado', 'pendiente')
        ->count();
    
    if ($solicitudesPendientes == 0) {
        // Crear al menos una solicitud pendiente para este chofer
        // Obtener un cliente aleatorio
        $cliente = DB::table('users')
            ->where('id_roles', '!=', 6)  // No seleccionar usuarios que sean choferes
            ->inRandomOrder()
            ->first();
        
        if ($cliente) {
            // Generar coordenadas de origen (España)
            $latitudOrigen = 41.3851 + (rand(-100, 100) / 1000);  // Barcelona área
            $longitudOrigen = 2.1734 + (rand(-100, 100) / 1000);
            
            // Generar coordenadas de destino (cerca del origen, pero distinto)
            $latitudDestino = $latitudOrigen + (rand(-50, 50) / 1000);
            $longitudDestino = $longitudOrigen + (rand(-50, 50) / 1000);
            
            // Generar precio
            $precio = rand(1500, 5000) / 100;  // Entre 15 y 50 euros
            
            // Insertar solicitud con estado 'pendiente'
            DB::table('solicitudes')->insert([
                'id_chofer' => $chofer->id,
                'id_cliente' => $cliente->id_usuario,
                'latitud_origen' => $latitudOrigen,
                'longitud_origen' => $longitudOrigen,
                'latitud_destino' => $latitudDestino,
                'longitud_destino' => $longitudDestino,
                'precio' => $precio,
                'estado' => 'pendiente',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            
            $countCreadas++;
        }
    }
}

echo "Se crearon {$countCreadas} nuevas solicitudes pendientes para choferes que no tenían.\n\n";

// 4. Limpiar cachés de la aplicación
echo "Limpiando cachés de la aplicación...\n";
echo exec('php artisan cache:clear') . "\n";
echo exec('php artisan route:clear') . "\n";
echo exec('php artisan config:clear') . "\n";
echo exec('php artisan view:clear') . "\n";
echo exec('php artisan optimize:clear') . "\n";

echo "\n=========== SOLUCIÓN COMPLETA ===========\n";
echo "Por favor, reinicia el servidor web y actualiza el navegador para ver los cambios.\n";
echo "Para reiniciar el servidor: Ctrl+C y luego 'php artisan serve'\n";
