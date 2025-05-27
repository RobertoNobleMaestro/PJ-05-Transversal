<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Chofer;
use App\Models\Solicitud;
use Carbon\Carbon;

// Usuario para la prueba (chofer.barcelona1@carflow.com)
$choferEmail = 'chofer.barcelona1@carflow.com';

echo "=========== DIAGNÓSTICO DE SOLICITUDES ===========\n\n";

// 1. Verificar si el usuario existe
$user = User::where('email', $choferEmail)->first();

if (!$user) {
    echo "ERROR: El usuario con email {$choferEmail} no existe.\n";
    exit(1);
} else {
    echo "OK: Usuario encontrado. ID: {$user->id_usuario}, Nombre: {$user->nombre}\n\n";
}

// 2. Verificar si el usuario es un chofer
$chofer = Chofer::where('id_usuario', $user->id_usuario)->first();

if (!$chofer) {
    echo "ERROR: El usuario no tiene un registro de chofer asociado.\n";
    exit(1);
} else {
    echo "OK: Chofer encontrado. ID: {$chofer->id}\n\n";
}

// 3. Verificar si hay solicitudes para este chofer (sin filtrar)
$todasSolicitudes = DB::table('solicitudes')
    ->where('id_chofer', $chofer->id)
    ->get();

echo "Total de solicitudes para este chofer (sin filtrar): " . $todasSolicitudes->count() . "\n\n";
if ($todasSolicitudes->isEmpty()) {
    echo "ALERTA: No hay solicitudes asociadas a este chofer.\n";
}

// 4. Verificar solicitudes pendientes
$solicitudesPendientes = DB::table('solicitudes')
    ->where('id_chofer', $chofer->id)
    ->where('solicitudes.estado', 'pendiente')
    ->get();

echo "Total de solicitudes pendientes: " . $solicitudesPendientes->count() . "\n\n";

if ($solicitudesPendientes->isEmpty()) {
    echo "ALERTA: No hay solicitudes pendientes para este chofer.\n";
} else {
    echo "Listado de solicitudes pendientes:\n";
    echo str_repeat('-', 100) . "\n";
    echo sprintf("%-5s | %-10s | %-12s | %-25s | %-10s | %-15s\n", 
        "ID", "ID Cliente", "ID Chofer", "Estado", "Precio", "Fecha Creación");
    echo str_repeat('-', 100) . "\n";
    
    foreach ($solicitudesPendientes as $solicitud) {
        echo sprintf("%-5s | %-10s | %-12s | %-25s | %-10s | %-15s\n", 
            $solicitud->id, 
            $solicitud->id_cliente, 
            $solicitud->id_chofer, 
            $solicitud->estado, 
            $solicitud->precio,
            $solicitud->created_at);
    }
    echo str_repeat('-', 100) . "\n\n";
}

// 5. Verificar si hay algún problema con los datos en la tabla de solicitudes
$solicitudesIncompletas = DB::table('solicitudes')
    ->where('id_chofer', $chofer->id)
    ->whereNull('id_cliente')
    ->orWhereNull('estado')
    ->orWhereNull('precio')
    ->get();

if ($solicitudesIncompletas->count() > 0) {
    echo "ALERTA: Se encontraron " . $solicitudesIncompletas->count() . " solicitudes con datos incompletos.\n\n";
}

// 6. Verificar la estructura de la tabla solicitudes
echo "Estructura de la tabla 'solicitudes':\n";
$columnas = DB::select('SHOW COLUMNS FROM solicitudes');
echo str_repeat('-', 70) . "\n";
echo sprintf("%-20s | %-15s | %-8s | %-15s\n", "Campo", "Tipo", "Nulo", "Default");
echo str_repeat('-', 70) . "\n";
foreach ($columnas as $columna) {
    echo sprintf("%-20s | %-15s | %-8s | %-15s\n", 
        $columna->Field, 
        $columna->Type, 
        $columna->Null, 
        $columna->Default ?? 'NULL');
}
echo str_repeat('-', 70) . "\n\n";

// 7. Verificar las rutas relacionadas
echo "Rutas relacionadas con solicitudes de choferes:\n";
$rutasApi = DB::table('routes')
    ->where('uri', 'like', '%solicitudes%')
    ->orWhere('uri', 'like', '%chofer%')
    ->get();

if (isset($rutasApi) && !$rutasApi->isEmpty()) {
    foreach ($rutasApi as $ruta) {
        echo "- {$ruta->uri} ({$ruta->method})\n";
    }
} else {
    echo "No se pudo obtener información de rutas desde la base de datos.\n";
    echo "Revisar manualmente en routes/web.php y routes/api.php\n";
}

echo "\n=========== FIN DEL DIAGNÓSTICO ===========\n";
