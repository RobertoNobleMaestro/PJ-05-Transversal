<?php

// Script para recopilar todas las migraciones en un solo archivo de texto
$migrationsDir = __DIR__ . '/database/migrations';
$outputFile = __DIR__ . '/todas_las_migraciones.txt';

// Iniciar archivo de salida
file_put_contents($outputFile, "# TODAS LAS MIGRACIONES DEL PROYECTO CARFLOW\n\nEste archivo contiene todas las migraciones del proyecto organizadas por nombre de archivo.\n\n");

// Obtener todos los archivos de migración
$migrationFiles = glob($migrationsDir . '/*.php');
sort($migrationFiles); // Ordenar por nombre (que incluye fecha)

// Procesar cada archivo
foreach ($migrationFiles as $migrationFile) {
    $fileName = basename($migrationFile);
    $content = file_get_contents($migrationFile);
    
    // Añadir la información al archivo de salida
    file_put_contents(
        $outputFile, 
        "## " . $fileName . "\n\n```php\n" . $content . "\n```\n\n", 
        FILE_APPEND
    );
    
    echo "Procesado: " . $fileName . "\n";
}

echo "\nCompletado. Se han procesado " . count($migrationFiles) . " migraciones.\n";
echo "El resultado se encuentra en: " . $outputFile . "\n";
