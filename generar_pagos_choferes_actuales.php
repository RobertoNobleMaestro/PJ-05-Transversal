<?php
// Script para generar registros de pagos de choferes en el período actual (2025)

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== GENERACIÓN DE PAGOS DE CHOFERES RECIENTES (2025) ===\n\n";

// Verificar si hay restricciones de clave foránea que debamos desactivar
try {
    // Desactivar temporalmente las restricciones de clave foránea
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    echo "Restricciones de clave foránea desactivadas temporalmente.\n";
    
    // Generar datos aleatorios para el período actual (últimos 3 meses de 2025)
    $fechaInicio = Carbon::create(2025, 3, 1); // Marzo 2025
    $fechaFin = Carbon::create(2025, 5, 27); // Hoy (27 de mayo 2025)
    $diasRango = $fechaFin->diffInDays($fechaInicio);
    
    // Cantidad de registros a generar (más frecuentes en el período reciente)
    $cantidadPagos = 50;
    echo "Se generarán {$cantidadPagos} registros de pagos de servicios de taxi para 2025.\n";
    
    // Generar IDs simulados para choferes y solicitudes
    $choferesIds = range(1, 10);
    $solicitudesIds = range(1, 50);
    
    // Insertar registros
    $registrosCreados = 0;
    $totalImporteEmpresa = 0;
    
    for ($i = 0; $i < $cantidadPagos; $i++) {
        try {
            // Seleccionar IDs aleatorios
            $choferId = $choferesIds[array_rand($choferesIds)];
            $solicitudId = $solicitudesIds[array_rand($solicitudesIds)];
            
            // Generar fecha aleatoria dentro del rango (2025)
            $diasAleatorios = rand(0, $diasRango);
            $fechaPago = $fechaInicio->copy()->addDays($diasAleatorios);
            
            // Generar importes realistas (tarifa media 15-50€)
            $importeTotal = round(rand(1500, 5000) / 100, 2);
            
            // Empresa se queda con 20-30%
            $porcentaje = rand(20, 30) / 100;
            $importeEmpresa = round($importeTotal * $porcentaje, 2);
            $importeChofer = round($importeTotal - $importeEmpresa, 2);
            
            // Acumular el total para mostrar estadísticas
            $totalImporteEmpresa += $importeEmpresa;
            
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
        } catch (\Exception $e) {
            echo "Error al crear registro #{$i}: " . $e->getMessage() . "\n";
        }
    }
    
    echo "\nSe crearon {$registrosCreados} de {$cantidadPagos} registros.\n";
    echo "Total de ingresos para la empresa en 2025: " . number_format($totalImporteEmpresa, 2) . "€\n";
    
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

echo "\n=== VERIFICACIÓN DE DATOS DE 2025 ===\n";

try {
    // Verificar ingresos para el período actual (abril-mayo 2025)
    $fechaInicio = Carbon::create(2025, 4, 1);
    $fechaFin = Carbon::create(2025, 5, 31);
    
    $ingresosPeriodoActual = DB::table('pagos_choferes')
        ->where('estado_pago', 'pagado')
        ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
        ->sum('importe_empresa');
        
    echo "Ingresos por servicios de taxi (Abril-Mayo 2025): " . number_format($ingresosPeriodoActual, 2) . "€\n";
    
    // Mostrar últimos 5 registros de 2025
    echo "\nÚltimos 5 registros de 2025:\n";
    $ultimosRegistros = DB::table('pagos_choferes')
        ->where('fecha_pago', '>=', '2025-01-01')
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
