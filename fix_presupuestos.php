<?php
// Script para corregir el problema de columna fecha_reserva en la consulta de presupuestos

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminFinancieroController.php';
$content = file_get_contents($controllerPath);

// Buscar y reemplazar las referencias incorrectas en la tabla presupuestos
$search = "->where('periodo_tipo', 'mensual')\n            ->whereBetween('fecha_reserva', [\$fechaInicio, \$fechaFin])";
$replace = "->where('periodo_tipo', 'mensual')\n            ->whereBetween('fecha_inicio', [\$fechaInicio, \$fechaFin])";

// Realizar el reemplazo
$newContent = str_replace($search, $replace, $content);

// Guardar los cambios
if ($newContent !== $content) {
    // Hacer una copia de seguridad
    file_put_contents($controllerPath . '.bak5', $content);
    
    // Guardar el archivo modificado
    file_put_contents($controllerPath, $newContent);
    
    echo "¡Corrección de campo fecha_reserva en presupuestos aplicada correctamente!\n";
    echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak5' . "\n";
} else {
    echo "No se pudo aplicar la corrección. El patrón de búsqueda no se encontró exactamente.\n";
    
    // Intentar con otro formato de espaciado/indentación
    $search2 = "->where('periodo_tipo', 'mensual')->whereBetween('fecha_reserva', [\$fechaInicio, \$fechaFin])";
    $replace2 = "->where('periodo_tipo', 'mensual')->whereBetween('fecha_inicio', [\$fechaInicio, \$fechaFin])";
    
    $newContent = str_replace($search2, $replace2, $content);
    
    if ($newContent !== $content) {
        // Hacer una copia de seguridad
        file_put_contents($controllerPath . '.bak5', $content);
        
        // Guardar el archivo modificado
        file_put_contents($controllerPath, $newContent);
        
        echo "¡Corrección de campo fecha_reserva en presupuestos aplicada correctamente (formato alternativo)!\n";
        echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak5' . "\n";
    } else {
        echo "No se pudo encontrar el patrón con ninguno de los formatos. Se necesita una revisión manual.\n";
    }
}
