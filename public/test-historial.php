<?php
// Script de prueba para depurar el endpoint del historial

// Configurar visualización de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Incluir el autoloader de Composer
require __DIR__.'/../vendor/autoload.php';

// Crear una aplicación Laravel básica
$app = require_once __DIR__.'/../bootstrap/app.php';

// Obtener el kernel HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Crear una petición falsa para simular una petición Ajax al historial
$request = Illuminate\Http\Request::create(
    '/admin/historial/data',
    'GET',
    [], // Sin parámetros
    [], // Sin cookies
    [], // Sin archivos
    ['HTTP_X-Requested-With' => 'XMLHttpRequest'] // Simular petición Ajax
);

// Procesar la petición a través del kernel de Laravel
try {
    $response = $kernel->handle($request);
    
    // Mostrar el código de estado
    echo "Código de estado: " . $response->getStatusCode() . "\n";
    
    // Mostrar los encabezados
    echo "Encabezados:\n";
    foreach ($response->headers->all() as $name => $values) {
        echo "  $name: " . implode(', ', $values) . "\n";
    }
    
    // Mostrar el contenido (posiblemente un mensaje de error JSON)
    echo "\nContenido:\n";
    echo $response->getContent();
    
} catch (Exception $e) {
    echo "Excepción capturada: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
}
