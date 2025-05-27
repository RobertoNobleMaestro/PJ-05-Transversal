<?php
// Script para generar registros de pagos de choferes directamente

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== GENERACIÓN DE PAGOS DE CHOFERES (SERVICIOS DE TAXI) ===\n\n";

// Verificar si hay restricciones de clave foránea que debamos desactivar
try {
    // Desactivar temporalmente las restricciones de clave foránea
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    echo "Restricciones de clave foránea desactivadas temporalmente.\n";
    
    // Generar datos aleatorios para un rango de 12 meses
    $fechaInicio = Carbon::now()->subMonths(12);
    $fechaFin = Carbon::now();
    $diasRango = $fechaFin->diffInDays($fechaInicio);
    
    // Cantidad de registros a generar
    $cantidadPagos = rand(100, 150);
    echo "Se generarán {$cantidadPagos} registros de pagos de servicios de taxi.\n";
    
    // Limpiar tabla si existe
    try {
        DB::table('pagos_choferes')->truncate();
        echo "Tabla pagos_choferes truncada correctamente.\n";
    } catch (\Exception $e) {
        echo "Advertencia: No se pudo truncar la tabla: " . $e->getMessage() . "\n";
    }
    
    // Generar IDs simulados para choferes y solicitudes
    $choferesIds = range(1, 10);
    $solicitudesIds = range(1, 50);
    
    // Insertar registros
    $registrosCreados = 0;
    
    for ($i = 0; $i < $cantidadPagos; $i++) {
        try {
            // Seleccionar IDs aleatorios
            $choferId = $choferesIds[array_rand($choferesIds)];
            $solicitudId = $solicitudesIds[array_rand($solicitudesIds)];
            
            // Generar fecha aleatoria dentro del rango
            $diasAleatorios = rand(0, $diasRango);
            $fechaPago = $fechaInicio->copy()->addDays($diasAleatorios);
            
            // Generar importes realistas (tarifa media 15-50€)
            $importeTotal = round(rand(1500, 5000) / 100, 2);
            
            // Empresa se queda con 20-30%
            $porcentaje = rand(20, 30) / 100;
            $importeEmpresa = round($importeTotal * $porcentaje, 2);
            $importeChofer = round($importeTotal - $importeEmpresa, 2);
            
            // Insertar registro
            DB::table('pagos_choferes')->insert([
                'chofer_id' => $choferId,
                'solicitud_id' => $solicitudId,
                'importe_total' => $importeTotal,
                'importe_empresa' => $importeEmpresa,
                'importe_chofer' => $importeChofer,
                'estado_pago' => 'pagado',
                'fecha_pago' => $fechaPago,
                'created_at' => $fechaPago,
                'updated_at' => $fechaPago
            ]);
            
            $registrosCreados++;
            
            // Mostrar progreso cada 10 registros
            if ($i % 10 == 0) {
                echo "Progreso: {$i}/{$cantidadPagos}\n";
            }
        } catch (\Exception $e) {
            echo "Error al crear registro #{$i}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nSe crearon {$registrosCreados} de {$cantidadPagos} registros.\n";
    
    // Reactivar las restricciones de clave foránea
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "Restricciones de clave foránea reactivadas.\n";
    
} catch (\Exception $e) {
    echo "Error durante la generación de datos: " . $e->getMessage() . "\n";
    
    // Asegurar que las restricciones de clave foránea se reactivan
    try {
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        echo "Restricciones de clave foránea reactivadas tras error.\n";
    } catch (\Exception $e2) {
        echo "Error al reactivar restricciones: " . $e2->getMessage() . "\n";
    }
}

echo "\n=== VERIFICACIÓN DE DATOS GENERADOS ===\n";

try {
    $totalRegistros = DB::table('pagos_choferes')->count();
    echo "Total de registros en pagos_choferes: {$totalRegistros}\n";
    
    $totalIngresos = DB::table('pagos_choferes')->sum('importe_empresa');
    echo "Total de ingresos para la empresa: " . number_format($totalIngresos, 2) . "€\n";
    
    // Mostrar últimos 5 registros
    echo "\nÚltimos 5 registros:\n";
    $ultimosRegistros = DB::table('pagos_choferes')
        ->orderBy('fecha_pago', 'desc')
        ->limit(5)
        ->get();
    
    foreach ($ultimosRegistros as $index => $registro) {
        echo ($index + 1) . ". Fecha: {$registro->fecha_pago}, Importe Empresa: {$registro->importe_empresa}€\n";
    }
    
} catch (\Exception $e) {
    echo "Error al verificar datos: " . $e->getMessage() . "\n";
}

echo "\n=== GENERACIÓN COMPLETADA ===\n";
