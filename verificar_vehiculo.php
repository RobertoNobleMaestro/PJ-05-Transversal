<?php
// Script para verificar la información de un vehículo directamente en la base de datos

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// ID del vehículo que quieres verificar (ajusta este valor según el vehículo que estás viendo)
$vehiculo_id = 3; // Puedes cambiarlo al ID que estás usando

// Consultar la información completa del vehículo
$vehiculo = \App\Models\Vehiculo::find($vehiculo_id);

if (!$vehiculo) {
    echo "Vehículo con ID {$vehiculo_id} no encontrado.\n";
    exit;
}

echo "Información del vehículo ID {$vehiculo_id}:\n";
echo "-----------------------------------\n";
echo "Marca: " . ($vehiculo->marca ?? 'No definido') . "\n";
echo "Modelo: " . ($vehiculo->modelo ?? 'No definido') . "\n";
echo "Matrícula: " . ($vehiculo->matricula ?? 'No definido') . " (tipo de dato: " . gettype($vehiculo->matricula) . ")\n";
echo "Año: " . ($vehiculo->año ?? 'No definido') . "\n";
echo "Precio por día: " . ($vehiculo->precio_dia ?? 'No definido') . "\n";
echo "Estado: " . ($vehiculo->estado ?? 'No definido') . "\n";
echo "ID Tipo: " . ($vehiculo->id_tipo ?? 'No definido') . "\n";
echo "ID Lugar: " . ($vehiculo->id_lugar ?? 'No definido') . "\n";
echo "Valor actual calculado: " . $vehiculo->calcularValorActual() . "\n";
echo "¿Está amortizado?: " . ($vehiculo->estaAmortizado() ? 'Sí' : 'No') . "\n";

// Verificar si el campo 'matricula' existe en la tabla
try {
    $schema = \Illuminate\Support\Facades\Schema::getColumnListing('vehiculos');
    echo "\nColumnas de la tabla vehiculos:\n";
    echo implode(", ", $schema) . "\n";
    
    // Verificar si existe el campo 'matricula'
    if (in_array('matricula', $schema)) {
        echo "El campo 'matricula' SÍ existe en la tabla.\n";
    } else {
        echo "El campo 'matricula' NO existe en la tabla.\n";
    }
} catch (\Exception $e) {
    echo "Error al verificar el schema: " . $e->getMessage() . "\n";
}

// Verificar el valor directamente en la base de datos
try {
    $resultado = \Illuminate\Support\Facades\DB::table('vehiculos')
        ->select('matricula')
        ->where('id_vehiculos', $vehiculo_id)
        ->first();
    
    echo "\nConsulta directa a la base de datos:\n";
    if ($resultado) {
        echo "Matrícula (desde DB): " . ($resultado->matricula ?? 'NULL') . "\n";
    } else {
        echo "No se encontró el vehículo en la base de datos.\n";
    }
} catch (\Exception $e) {
    echo "Error en consulta directa: " . $e->getMessage() . "\n";
}

// Mostrar todos los atributos del modelo
echo "\nTodos los atributos del modelo:\n";
print_r($vehiculo->getAttributes());
