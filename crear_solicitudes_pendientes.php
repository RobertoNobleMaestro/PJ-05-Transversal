<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

echo "Creando solicitudes pendientes para choferes...\n\n";

$faker = Faker::create('es_ES');

// Obtener choferes disponibles
$choferes = DB::table('choferes')->select('id')->get();
echo "Se encontraron " . $choferes->count() . " choferes.\n";

if ($choferes->isEmpty()) {
    echo "No se encontraron choferes. No se pueden crear solicitudes pendientes.\n";
    exit(1);
}

// Obtener clientes disponibles
$clientes = DB::table('users')
    ->where('id_roles', '!=', 6)  // No seleccionar usuarios que sean choferes
    ->select('id_usuario as id')
    ->get();
    
if ($clientes->isEmpty()) {
    echo "No se encontraron clientes. No se pueden crear solicitudes pendientes.\n";
    exit(1);
} else {
    echo "Se encontraron " . $clientes->count() . " clientes.\n";
}

// Crear solicitud específica para chofer.barcelona1 desde cliente 11
try {
    // Obtener id del chofer barcelona1
    $choferBarcelona1 = DB::table('users')
        ->join('choferes', 'users.id_usuario', '=', 'choferes.id_usuario')
        ->where('users.email', 'chofer.barcelona1@carflow.com')
        ->select('choferes.id')
        ->first();
    
    if (!$choferBarcelona1) {
        echo "Error: No se encontró al chofer barcelona1.\n";
    } else {
        echo "Chofer Barcelona 1 encontrado con ID: " . $choferBarcelona1->id . "\n";
        
        // Generar coordenadas de origen (Barcelona)
        $latitudOrigen = 41.3851;  // Barcelona
        $longitudOrigen = 2.1734;
        
        // Generar coordenadas de destino (cerca de Barcelona)
        $latitudDestino = 41.4 + $faker->randomFloat(4, -0.05, 0.05);
        $longitudDestino = 2.17 + $faker->randomFloat(4, -0.05, 0.05);
        
        // Generar precio
        $precio = $faker->randomFloat(2, 20, 50);
        
        // Insertar solicitud para cliente 11 con chofer barcelona1
        DB::table('solicitudes')->insert([
            'id_chofer' => $choferBarcelona1->id,
            'id_cliente' => 11,  // Cliente 11
            'latitud_origen' => $latitudOrigen,
            'longitud_origen' => $longitudOrigen,
            'latitud_destino' => $latitudDestino,
            'longitud_destino' => $longitudDestino,
            'precio' => $precio,
            'estado' => 'pendiente',  // Importante: estado pendiente
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        echo "Solicitud pendiente creada para Chofer Barcelona 1 desde Cliente 11.\n";
    }
} catch (\Exception $e) {
    echo "Error al crear solicitud para Chofer Barcelona 1: " . $e->getMessage() . "\n";
}

// Determinar cantidad de solicitudes pendientes adicionales a generar
$cantidadSolicitudes = 10;  // Crear 10 solicitudes pendientes adicionales
echo "Se generarán {$cantidadSolicitudes} solicitudes pendientes adicionales.\n";

// Contador para el progreso
$solicitudesCreadas = 0;

// Generar solicitudes pendientes adicionales
for ($i = 0; $i < $cantidadSolicitudes; $i++) {
    try {
        // Seleccionar un chofer aleatorio
        $chofer = $choferes->random();
        
        // Seleccionar un cliente aleatorio
        $cliente = $clientes->random();
        
        // Generar coordenadas de origen (España)
        $latitudOrigen = $faker->latitude(36.0, 43.5);  // Aproximadamente España
        $longitudOrigen = $faker->longitude(-9.0, 3.0);
        
        // Generar coordenadas de destino (cerca del origen, pero distinto)
        $latitudDestino = $latitudOrigen + $faker->randomFloat(6, -0.05, 0.05);
        $longitudDestino = $longitudOrigen + $faker->randomFloat(6, -0.05, 0.05);
        
        // Generar precio
        $precio = $faker->randomFloat(2, 15, 50);
        
        // Insertar solicitud con estado 'pendiente'
        $idSolicitud = DB::table('solicitudes')->insertGetId([
            'id_chofer' => $chofer->id,
            'id_cliente' => $cliente->id,
            'latitud_origen' => $latitudOrigen,
            'longitud_origen' => $longitudOrigen,
            'latitud_destino' => $latitudDestino,
            'longitud_destino' => $longitudDestino,
            'precio' => $precio,
            'estado' => 'pendiente',  // Importante: estado pendiente
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        
        $solicitudesCreadas++;
        echo "Solicitud pendiente adicional #{$solicitudesCreadas} creada correctamente.\n";
        
    } catch (\Exception $e) {
        echo "Error al crear solicitud pendiente #{$i}: " . $e->getMessage() . "\n";
    }
}

echo "\nProceso completado: Total de solicitudes pendientes creadas = " . ($solicitudesCreadas + 1) . ".\n";
echo "Ahora deberías poder ver estas solicitudes en la vista de choferes.\n";
