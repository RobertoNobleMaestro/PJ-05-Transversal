<?php

require_once __DIR__ . '/vendor/autoload.php';

// Cargar las variables de entorno
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Reserva;
use App\Models\VehiculosReservas;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

// Ver todas las reservas pendientes
echo "=== RESERVAS PENDIENTES ===\n";
$reservas = Reserva::where('estado', 'pendiente')->get();
foreach ($reservas as $reserva) {
    echo "ID: {$reserva->id_reservas}, Usuario: {$reserva->id_usuario}, Fecha: {$reserva->fecha_reserva}, Total: {$reserva->total_precio}\n";
    
    // Ver vehículos en esta reserva
    $vehiculosReservas = VehiculosReservas::where('id_reservas', $reserva->id_reservas)->get();
    echo "  Vehículos en esta reserva: " . count($vehiculosReservas) . "\n";
    
    foreach ($vehiculosReservas as $vr) {
        $vehiculo = Vehiculo::find($vr->id_vehiculos);
        if ($vehiculo) {
            echo "  - {$vehiculo->marca} {$vehiculo->modelo}, Del: {$vr->fecha_ini} al {$vr->fecha_final}\n";
        }
    }
    echo "\n";
}

// Verificar la estructura de la tabla reservas
echo "=== ESTRUCTURA DE LA TABLA RESERVAS ===\n";
$columns = DB::getSchemaBuilder()->getColumnListing('reservas');
print_r($columns);

echo "\n=== ESTRUCTURA DE LA TABLA VEHICULOS_RESERVAS ===\n";
$columns = DB::getSchemaBuilder()->getColumnListing('vehiculos_reservas');
print_r($columns);

// Verificar la ruta de pago.checkout
echo "\n=== RUTAS REGISTRADAS ===\n";
$routes = Route::getRoutes();
$checkoutRoute = null;

foreach ($routes as $route) {
    if ($route->getName() === 'pago.checkout') {
        $checkoutRoute = $route;
        echo "Ruta 'pago.checkout' encontrada:\n";
        echo "  URI: " . $route->uri() . "\n";
        echo "  Método: " . implode('|', $route->methods()) . "\n";
        echo "  Acción: " . $route->getActionName() . "\n";
        break;
    }
}

if (!$checkoutRoute) {
    echo "¡ADVERTENCIA! No se encontró la ruta 'pago.checkout'\n";
}

echo "\nDebug completado.\n";
