<?php
// Script para verificar la estructura de la tabla asalariados

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== VERIFICACIÓN DE ESTRUCTURA DE LA TABLA ASALARIADOS ===\n\n";

// Verificar si la tabla existe
if (Schema::hasTable('asalariados')) {
    echo "La tabla 'asalariados' existe en la base de datos.\n\n";
    
    // Obtener las columnas de la tabla
    $columnas = Schema::getColumnListing('asalariados');
    
    echo "Columnas existentes en la tabla asalariados:\n";
    foreach ($columnas as $index => $columna) {
        echo ($index + 1) . ". {$columna}\n";
    }
    
    // Verificar si existe la columna dias_trabajados
    if (in_array('dias_trabajados', $columnas)) {
        echo "\nLa columna 'dias_trabajados' YA EXISTE en la tabla asalariados.\n";
        
        // Verificar el tipo de datos de la columna
        $tipo = DB::getSchemaBuilder()->getColumnType('asalariados', 'dias_trabajados');
        echo "Tipo de datos de la columna: {$tipo}\n";
        
        // Verificar si hay datos en la columna
        $totalRegistros = DB::table('asalariados')->count();
        $registrosConDiasTrabajados = DB::table('asalariados')
            ->whereNotNull('dias_trabajados')
            ->count();
            
        echo "Total de registros: {$totalRegistros}\n";
        echo "Registros con valor en dias_trabajados: {$registrosConDiasTrabajados}\n";
        
        // Mostrar algunos ejemplos
        if ($registrosConDiasTrabajados > 0) {
            echo "\nEjemplos de registros con dias_trabajados:\n";
            $ejemplos = DB::table('asalariados')
                ->whereNotNull('dias_trabajados')
                ->limit(5)
                ->get(['id', 'id_usuario', 'dias_trabajados', 'estado']);
                
            foreach ($ejemplos as $ejemplo) {
                echo "ID: {$ejemplo->id}, Usuario: {$ejemplo->id_usuario}, Días trabajados: {$ejemplo->dias_trabajados}, Estado: {$ejemplo->estado}\n";
            }
        }
    } else {
        echo "\nLa columna 'dias_trabajados' NO EXISTE en la tabla asalariados. Se necesita crear una migración.\n";
    }
} else {
    echo "La tabla 'asalariados' NO EXISTE en la base de datos.\n";
}

echo "\n=== FIN DE LA VERIFICACIÓN ===\n";
