<?php

// Script para recopilar todos los seeders en un solo archivo de texto
$seedersDir = __DIR__ . '/database/seeders';
$outputFile = __DIR__ . '/todos_los_seeders.txt';

// Iniciar archivo de salida
file_put_contents($outputFile, "# TODOS LOS SEEDERS DEL PROYECTO CARFLOW\n\nEste archivo contiene todos los seeders del proyecto organizados por nombre de archivo.\n\n");

// Obtener todos los archivos de seeders
$seederFiles = glob($seedersDir . '/*.php');
sort($seederFiles); // Ordenar por nombre

// Procesar cada archivo
foreach ($seederFiles as $seederFile) {
    $fileName = basename($seederFile);
    $content = file_get_contents($seederFile);
    
    // Añadir la información al archivo de salida
    file_put_contents(
        $outputFile, 
        "## " . $fileName . "\n\n```php\n" . $content . "\n```\n\n", 
        FILE_APPEND
    );
    
    echo "Procesado: " . $fileName . "\n";
}

echo "\nCompletado. Se han procesado " . count($seederFiles) . " seeders.\n";
echo "El resultado se encuentra en: " . $outputFile . "\n";
