<?php
// Script para diagnosticar problemas con la carga de ingresos

// Cargar el entorno Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

echo "=== DIAGNÓSTICO DE CARGA DE INGRESOS ===\n\n";

// 1. Verificar estructura de las tablas
echo "=== VERIFICACIÓN DE ESTRUCTURA DE TABLAS ===\n";

// Tabla pagos
echo "\nTABLA PAGOS:\n";
if (Schema::hasTable('pagos')) {
    $columnasPagos = Schema::getColumnListing('pagos');
    echo "Columnas existentes: " . implode(", ", $columnasPagos) . "\n";
    
    // Verificar columnas específicas
    echo "¿Tiene estado_pago? " . (in_array('estado_pago', $columnasPagos) ? 'SÍ' : 'NO') . "\n";
    echo "¿Tiene fecha_pago? " . (in_array('fecha_pago', $columnasPagos) ? 'SÍ' : 'NO') . "\n";
    echo "¿Tiene total_precio? " . (in_array('total_precio', $columnasPagos) ? 'SÍ' : 'NO') . "\n";
} else {
    echo "La tabla 'pagos' no existe en la base de datos.\n";
}

// Tabla pagos_choferes
echo "\nTABLA PAGOS_CHOFERES:\n";
if (Schema::hasTable('pagos_choferes')) {
    $columnasPagosChoferes = Schema::getColumnListing('pagos_choferes');
    echo "Columnas existentes: " . implode(", ", $columnasPagosChoferes) . "\n";
    
    // Verificar columnas específicas
    echo "¿Tiene estado_pago? " . (in_array('estado_pago', $columnasPagosChoferes) ? 'SÍ' : 'NO') . "\n";
    echo "¿Tiene fecha_pago? " . (in_array('fecha_pago', $columnasPagosChoferes) ? 'SÍ' : 'NO') . "\n";
    echo "¿Tiene importe_empresa? " . (in_array('importe_empresa', $columnasPagosChoferes) ? 'SÍ' : 'NO') . "\n";
} else {
    echo "La tabla 'pagos_choferes' no existe en la base de datos.\n";
}

// Tabla pago_taller
echo "\nTABLA PAGO_TALLER:\n";
if (Schema::hasTable('pago_taller')) {
    $columnasPagoTaller = Schema::getColumnListing('pago_taller');
    echo "Columnas existentes: " . implode(", ", $columnasPagoTaller) . "\n";
    
    // Verificar columnas específicas
    echo "¿Tiene total? " . (in_array('total', $columnasPagoTaller) ? 'SÍ' : 'NO') . "\n";
    echo "¿Tiene created_at? " . (in_array('created_at', $columnasPagoTaller) ? 'SÍ' : 'NO') . "\n";
} else {
    echo "La tabla 'pago_taller' no existe en la base de datos.\n";
}

// 2. Intentar consultas básicas para verificar datos
echo "\n=== CONSULTAS DE PRUEBA ===\n";

// Fechas para filtrado
$fechaInicio = Carbon::now()->subMonths(3)->startOfMonth();
$fechaFin = Carbon::now()->endOfMonth();
echo "\nPeriodo de prueba: " . $fechaInicio->format('Y-m-d') . " a " . $fechaFin->format('Y-m-d') . "\n";

// Consulta a la tabla pagos
if (Schema::hasTable('pagos')) {
    try {
        $totalPagos = DB::table('pagos')->count();
        echo "\nTotal de registros en 'pagos': $totalPagos\n";
        
        // Probar consulta con filtros
        if (in_array('estado_pago', $columnasPagos) && in_array('fecha_pago', $columnasPagos)) {
            $pagosFiltrados = DB::table('pagos')
                ->where('estado_pago', 'completado')
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->count();
            echo "Pagos filtrados por periodo y estado: $pagosFiltrados\n";
        }
        
        // Probar suma de total_precio
        if (in_array('total_precio', $columnasPagos)) {
            $sumaPagos = DB::table('pagos')->sum('total_precio');
            echo "Suma de total_precio en 'pagos': $sumaPagos\n";
        }
    } catch (\Exception $e) {
        echo "Error en consulta a 'pagos': " . $e->getMessage() . "\n";
    }
}

// Consulta a pagos_choferes
if (Schema::hasTable('pagos_choferes')) {
    try {
        $totalPagosChoferes = DB::table('pagos_choferes')->count();
        echo "\nTotal de registros en 'pagos_choferes': $totalPagosChoferes\n";
        
        // Probar consulta con filtros
        if (in_array('estado_pago', $columnasPagosChoferes) && in_array('fecha_pago', $columnasPagosChoferes)) {
            $pagosChoferesFiltrados = DB::table('pagos_choferes')
                ->where('estado_pago', 'pagado')
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->count();
            echo "Pagos choferes filtrados por periodo y estado: $pagosChoferesFiltrados\n";
        }
        
        // Probar suma de importe_empresa
        if (in_array('importe_empresa', $columnasPagosChoferes)) {
            $sumaPagosChoferes = DB::table('pagos_choferes')->sum('importe_empresa');
            echo "Suma de importe_empresa en 'pagos_choferes': $sumaPagosChoferes\n";
        }
    } catch (\Exception $e) {
        echo "Error en consulta a 'pagos_choferes': " . $e->getMessage() . "\n";
    }
}

// Consulta a pago_taller
if (Schema::hasTable('pago_taller')) {
    try {
        $totalPagoTaller = DB::table('pago_taller')->count();
        echo "\nTotal de registros en 'pago_taller': $totalPagoTaller\n";
        
        // Probar suma de total
        if (in_array('total', $columnasPagoTaller)) {
            $sumaPagoTaller = DB::table('pago_taller')->sum('total');
            echo "Suma de total en 'pago_taller': $sumaPagoTaller\n";
        }
    } catch (\Exception $e) {
        echo "Error en consulta a 'pago_taller': " . $e->getMessage() . "\n";
    }
}

// 3. Consultas para verificar si los datos existen en la tabla de reservas
echo "\n=== VERIFICACIÓN DE DATOS EN RESERVAS ===\n";
if (Schema::hasTable('reservas')) {
    $columnasReservas = Schema::getColumnListing('reservas');
    echo "Columnas en 'reservas': " . implode(", ", $columnasReservas) . "\n";
    
    try {
        $totalReservas = DB::table('reservas')->count();
        echo "Total de registros en 'reservas': $totalReservas\n";
        
        // Probar consulta con filtros
        if (in_array('fecha_reserva', $columnasReservas)) {
            $reservasFiltradas = DB::table('reservas')
                ->whereBetween('fecha_reserva', [$fechaInicio, $fechaFin])
                ->count();
            echo "Reservas filtradas por periodo: $reservasFiltradas\n";
        }
        
        // Probar suma de total_precio si existe
        if (in_array('total_precio', $columnasReservas)) {
            $sumaReservas = DB::table('reservas')->sum('total_precio');
            echo "Suma de total_precio en 'reservas': $sumaReservas\n";
        }
    } catch (\Exception $e) {
        echo "Error en consulta a 'reservas': " . $e->getMessage() . "\n";
    }
}

echo "\n=== FIN DEL DIAGNÓSTICO ===\n";
