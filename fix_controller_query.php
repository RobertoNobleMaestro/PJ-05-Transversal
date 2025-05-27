<?php
// Script para corregir el problema de columna importe_total en AdminFinancieroController.php

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminFinancieroController.php';
$content = file_get_contents($controllerPath);

// Buscar y reemplazar las referencias a importe_total por total_precio
$search = "->sum('importe_total')";
$replace = "->sum('total_precio')";

// Realizar el reemplazo
$newContent = str_replace($search, $replace, $content);

// Guardar los cambios
if ($newContent !== $content) {
    // Hacer una copia de seguridad
    file_put_contents($controllerPath . '.bak2', $content);
    
    // Guardar el archivo modificado
    file_put_contents($controllerPath, $newContent);
    
    echo "¡Corrección de consulta SQL aplicada correctamente!\n";
    echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak2' . "\n";
} else {
    echo "No se pudo aplicar la corrección. El patrón de búsqueda no se encontró exactamente.\n";
}
