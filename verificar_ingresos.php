<?php
// Script para verificar si se han creado los datos de ingresos correctamente

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== VERIFICACIÓN DE DATOS DE INGRESOS ===\n\n";

// 1. Verificar pagos de taller
echo "PAGOS DE TALLER:\n";
try {
    $pagosTaller = DB::table('pago_taller')->get();
    echo "Total de registros en pago_taller: " . $pagosTaller->count() . "\n";
    
    if ($pagosTaller->count() > 0) {
        $totalIngresos = DB::table('pago_taller')->sum('total');
        echo "Total de ingresos en taller: " . number_format($totalIngresos, 2) . "€\n";
        
        // Mostrar los últimos 5 registros
        echo "\nÚltimos 5 registros de pagos en taller:\n";
        $ultimosPagos = DB::table('pago_taller')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($ultimosPagos as $index => $pago) {
            echo ($index + 1) . ". Fecha: " . $pago->created_at . ", Total: " . $pago->total . "€\n";
        }
    }
} catch (\Exception $e) {
    echo "Error al verificar pagos de taller: " . $e->getMessage() . "\n";
}

// 2. Verificar pagos de choferes
echo "\nPAGOS DE CHOFERES (SERVICIOS DE TAXI):\n";
try {
    $pagosChoferes = DB::table('pagos_choferes')->get();
    echo "Total de registros en pagos_choferes: " . $pagosChoferes->count() . "\n";
    
    if ($pagosChoferes->count() > 0) {
        $totalIngresos = DB::table('pagos_choferes')->sum('importe_empresa');
        echo "Total de ingresos para la empresa por servicios de taxi: " . number_format($totalIngresos, 2) . "€\n";
        
        // Mostrar los últimos 5 registros
        echo "\nÚltimos 5 registros de pagos de choferes:\n";
        $ultimosPagos = DB::table('pagos_choferes')
            ->orderBy('fecha_pago', 'desc')
            ->limit(5)
            ->get();
            
        foreach ($ultimosPagos as $index => $pago) {
            echo ($index + 1) . ". Fecha: " . $pago->fecha_pago . ", Importe empresa: " . $pago->importe_empresa . "€\n";
        }
    }
} catch (\Exception $e) {
    echo "Error al verificar pagos de choferes: " . $e->getMessage() . "\n";
}

// 3. Probar la consulta del AdminFinancieroController
echo "\nPRUEBA DE CONSULTA DE INGRESOS (Simulando AdminFinancieroController):\n";
try {
    $fechaInicio = Carbon::now()->startOfMonth()->subMonths(1);
    $fechaFin = Carbon::now()->endOfMonth();
    
    echo "Periodo: " . $fechaInicio->format('Y-m-d') . " a " . $fechaFin->format('Y-m-d') . "\n";
    
    // Ingresos por reservas
    $ingresoReservas = DB::table('reservas')
        ->where('estado', 'completada')
        ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
        ->sum('total_precio');
    
    echo "Ingresos por reservas: " . number_format($ingresoReservas, 2) . "€\n";
    
    // Ingresos por servicios de taxi
    $ingresosChoferes = DB::table('pagos_choferes')
        ->where('estado_pago', 'pagado')
        ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
        ->sum('importe_empresa');
    
    echo "Ingresos por servicios de taxi: " . number_format($ingresosChoferes, 2) . "€\n";
    
    // Ingresos por taller
    $ingresosTaller = DB::table('pago_taller')
        ->whereBetween('created_at', [$fechaInicio, $fechaFin])
        ->sum('total');
    
    echo "Ingresos por reparaciones en taller: " . number_format($ingresosTaller, 2) . "€\n";
    
    // Total de ingresos
    $totalIngresos = $ingresoReservas + $ingresosChoferes + $ingresosTaller;
    echo "TOTAL DE INGRESOS EN EL PERÍODO: " . number_format($totalIngresos, 2) . "€\n";
    
} catch (\Exception $e) {
    echo "Error al simular consulta de AdminFinancieroController: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DE LA VERIFICACIÓN ===\n";
