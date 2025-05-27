<?php

/**
 * SCRIPT DE DIAGNÓSTICO PARA LA API DE SOLICITUDES DE CHOFERES
 * Este script simula una llamada a la API para obtener solicitudes de choferes
 * y muestra detalladamente el resultado.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Chofer;
use App\Http\Controllers\ChoferController;
use App\Http\Controllers\SolicitudController;

// Función para imprimir bien formateado
function printJson($data) {
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
}

echo "=========== DIAGNÓSTICO DE API DE SOLICITUDES ===========\n\n";

// 1. Verificar usuario
$email = 'chofer.barcelona1@carflow.com';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "ERROR: Usuario no encontrado con email $email\n";
    exit(1);
}

echo "Usuario encontrado: ID {$user->id_usuario}, Nombre: {$user->nombre}\n\n";

// 2. Verificar chofer
$chofer = Chofer::where('id_usuario', $user->id_usuario)->first();
if (!$chofer) {
    echo "ERROR: Chofer no encontrado para el usuario {$user->id_usuario}\n";
    exit(1);
}

echo "Chofer encontrado: ID {$chofer->id}\n\n";

// 3. Intentar autenticarse como el chofer
Auth::loginUsingId($user->id_usuario);
if (!Auth::check()) {
    echo "ERROR: No se pudo autenticar como el usuario {$user->id_usuario}\n";
    exit(1);
}

echo "Autenticación exitosa como {$user->nombre} (ID: {$user->id_usuario})\n";
echo "Roles: " . $user->id_roles . "\n\n";

// 4. Crear una instancia del controlador y llamar directamente al método
echo "Llamando directamente al método getSolicitudesChofer()...\n";

try {
    // Intentar con ChoferController
    $controller = new ChoferController();
    $response = $controller->getSolicitudesChofer();
    
    echo "Código de estado: " . $response->getStatusCode() . "\n";
    echo "Respuesta del controlador:\n";
    
    $content = json_decode($response->getContent(), true);
    printJson($content);
    
    if (isset($content['success']) && $content['success'] === true) {
        echo "Número de solicitudes encontradas: " . count($content['solicitudes']) . "\n\n";
        
        if (count($content['solicitudes']) > 0) {
            echo "Primera solicitud:\n";
            printJson($content['solicitudes'][0]);
        }
    }
} catch (\Exception $e) {
    echo "ERROR al llamar al método: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n\n";
}

// 5. Consultar directamente la base de datos
echo "Consultando directamente la base de datos...\n";
try {
    $solicitudes = \App\Models\Solicitud::with(['cliente', 'chofer'])
        ->where('id_chofer', $chofer->id)
        ->where('solicitudes.estado', 'pendiente')
        ->orderBy('solicitudes.created_at', 'desc')
        ->get();
    
    echo "Número de solicitudes encontradas en la BD: " . $solicitudes->count() . "\n\n";
    
    if ($solicitudes->count() > 0) {
        echo "Primera solicitud (datos crudos):\n";
        printJson($solicitudes->first()->toArray());
    }
} catch (\Exception $e) {
    echo "ERROR al consultar la BD: " . $e->getMessage() . "\n";
}

// 6. Verificar las rutas
echo "Verificando rutas...\n";
$routes = Route::getRoutes();
foreach ($routes as $route) {
    $uri = $route->uri();
    if (strpos($uri, 'solicitudes/chofer') !== false) {
        echo "Ruta encontrada: " . $route->methods()[0] . " " . $uri . "\n";
        echo "  Acción: " . $route->getActionName() . "\n";
    }
}

echo "\n=========== FIN DEL DIAGNÓSTICO ===========\n";
