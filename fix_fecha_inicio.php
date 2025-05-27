<?php
// Script para corregir el problema de columna fecha_inicio en AdminFinancieroController.php

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminFinancieroController.php';
$content = file_get_contents($controllerPath);

// Buscar y reemplazar las referencias a fecha_inicio por fecha_reserva
$search = "->whereBetween('fecha_inicio', [\$fechaInicio, \$fechaFin])";
$replace = "->whereBetween('fecha_reserva', [\$fechaInicio, \$fechaFin])";

// Realizar el reemplazo
$newContent = str_replace($search, $replace, $content);

// Guardar los cambios
if ($newContent !== $content) {
    // Hacer una copia de seguridad
    file_put_contents($controllerPath . '.bak3', $content);
    
    // Guardar el archivo modificado
    file_put_contents($controllerPath, $newContent);
    
    echo "¡Corrección de campo fecha_inicio aplicada correctamente!\n";
    echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak3' . "\n";
} else {
    echo "No se pudo aplicar la corrección. El patrón de búsqueda no se encontró exactamente.\n";
}
