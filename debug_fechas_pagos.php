<?php
// Script para depurar el problema de las fechas en los pagos de choferes

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== DEPURACIÓN DE FECHAS EN PAGOS DE CHOFERES ===\n\n";

// Primero, ver distribución de fechas por mes para pagos_choferes
echo "DISTRIBUCIÓN DE PAGOS POR MES:\n";
$meses = DB::table('pagos_choferes')
    ->select(DB::raw('YEAR(fecha_pago) as año, MONTH(fecha_pago) as mes, COUNT(*) as total'))
    ->groupBy('año', 'mes')
    ->orderBy('año', 'desc')
    ->orderBy('mes', 'desc')
    ->get();

foreach ($meses as $mes) {
    echo "Año: {$mes->año}, Mes: {$mes->mes}, Registros: {$mes->total}\n";
}

// Verificar si hay registros en abril-mayo 2025
echo "\nREGISTROS PARA ABRIL-MAYO 2025:\n";
$abrilMayo2025 = DB::table('pagos_choferes')
    ->whereBetween('fecha_pago', ['2025-04-01', '2025-05-31'])
    ->get();

echo "Total registros encontrados: " . $abrilMayo2025->count() . "\n";

if ($abrilMayo2025->count() > 0) {
    echo "\nPRIMEROS 5 REGISTROS DE ABRIL-MAYO 2025:\n";
    foreach (array_slice($abrilMayo2025->toArray(), 0, 5) as $index => $registro) {
        echo ($index + 1) . ". ID: {$registro->id}, Fecha: {$registro->fecha_pago}, Importe: {$registro->importe_empresa}€\n";
    }
} else {
    echo "No se encontraron registros para abril-mayo 2025\n";
}

// Intentar generar registros directamente con fechas explícitas
echo "\nGENERANDO 10 REGISTROS CON FECHAS EXPLÍCITAS...\n";
DB::statement('SET FOREIGN_KEY_CHECKS=0');

for ($i = 1; $i <= 10; $i++) {
    $dia = str_pad($i + 10, 2, '0', STR_PAD_LEFT); // Días del 11 al 20
    $fechaAbril = "2025-04-{$dia}";
    $fechaMayo = "2025-05-{$dia}";
    
    // Generar importes
    $importeTotal = round(rand(2000, 4000) / 100, 2);
    $importeEmpresa = round($importeTotal * 0.25, 2);
    $importeChofer = round($importeTotal - $importeEmpresa, 2);
    
    // Insertar para abril
    try {
        DB::table('pagos_choferes')->insert([
            'chofer_id' => rand(1, 10),
            'solicitud_id' => rand(1, 50),
            'importe_total' => $importeTotal,
            'importe_empresa' => $importeEmpresa,
            'importe_chofer' => $importeChofer,
            'estado_pago' => 'pagado',
            'fecha_pago' => $fechaAbril,
            'created_at' => $fechaAbril,
            'updated_at' => $fechaAbril
        ]);
        echo "Registro creado para fecha: {$fechaAbril}\n";
    } catch (\Exception $e) {
        echo "Error al crear registro para {$fechaAbril}: " . $e->getMessage() . "\n";
    }
    
    // Insertar para mayo
    try {
        DB::table('pagos_choferes')->insert([
            'chofer_id' => rand(1, 10),
            'solicitud_id' => rand(1, 50),
            'importe_total' => $importeTotal,
            'importe_empresa' => $importeEmpresa,
            'importe_chofer' => $importeChofer,
            'estado_pago' => 'pagado',
            'fecha_pago' => $fechaMayo,
            'created_at' => $fechaMayo,
            'updated_at' => $fechaMayo
        ]);
        echo "Registro creado para fecha: {$fechaMayo}\n";
    } catch (\Exception $e) {
        echo "Error al crear registro para {$fechaMayo}: " . $e->getMessage() . "\n";
    }
}

DB::statement('SET FOREIGN_KEY_CHECKS=1');

// Verificar nuevamente si hay registros en abril-mayo 2025
echo "\nVERIFICANDO NUEVAMENTE ABRIL-MAYO 2025 DESPUÉS DE INSERCIÓN DIRECTA:\n";
$abrilMayo2025 = DB::table('pagos_choferes')
    ->whereBetween('fecha_pago', ['2025-04-01', '2025-05-31'])
    ->get();

echo "Total registros encontrados: " . $abrilMayo2025->count() . "\n";

if ($abrilMayo2025->count() > 0) {
    echo "\nPRIMEROS 5 REGISTROS DE ABRIL-MAYO 2025:\n";
    $contador = 0;
    foreach ($abrilMayo2025 as $registro) {
        if ($contador >= 5) break;
        echo ($contador + 1) . ". ID: {$registro->id}, Fecha: {$registro->fecha_pago}, Importe: {$registro->importe_empresa}€\n";
        $contador++;
    }
}

// Verificar ingresos para el módulo financiero
echo "\nVERIFICANDO INGRESOS PARA EL MÓDULO FINANCIERO:\n";
$fechaInicio = Carbon::create(2025, 4, 1);
$fechaFin = Carbon::create(2025, 5, 31);

$ingresosPeriodoActual = DB::table('pagos_choferes')
    ->where('estado_pago', 'pagado')
    ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
    ->sum('importe_empresa');
    
echo "Ingresos por servicios de taxi (Abril-Mayo 2025): " . number_format($ingresosPeriodoActual, 2) . "€\n";

// Verificar si hay algún problema con el formato de las fechas
echo "\nVERIFICANDO FORMATO DE FECHAS:\n";
$fechasDistintas = DB::table('pagos_choferes')
    ->select('fecha_pago')
    ->distinct()
    ->orderBy('fecha_pago', 'desc')
    ->limit(5)
    ->get();

echo "Últimas 5 fechas distintas en la tabla:\n";
foreach ($fechasDistintas as $index => $fecha) {
    echo ($index + 1) . ". {$fecha->fecha_pago}\n";
}

echo "\n=== FIN DE LA DEPURACIÓN ===\n";
