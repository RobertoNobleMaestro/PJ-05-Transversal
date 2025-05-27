<?php
// Script para corregir problemas similares en el método repararVehiculoAmortizado

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminFinancieroController.php';
$content = file_get_contents($controllerPath);

// Array de búsqueda y reemplazo para aplicar todas las correcciones necesarias
$fixes = [
    // Corregir campo importe_total en repararVehiculoAmortizado
    "->sum('importe_total')" => "->sum('total_precio')",
    
    // Corregir campo fecha_inicio en repararVehiculoAmortizado
    "->whereBetween('fecha_inicio'" => "->whereBetween('fecha_reserva'",
];

// Aplicar todas las correcciones
$newContent = $content;
foreach ($fixes as $search => $replace) {
    $newContent = str_replace($search, $replace, $newContent);
}

// Guardar los cambios si se encontraron problemas
if ($newContent !== $content) {
    // Hacer una copia de seguridad
    file_put_contents($controllerPath . '.bak4', $content);
    
    // Guardar el archivo modificado
    file_put_contents($controllerPath, $newContent);
    
    echo "¡Correcciones adicionales aplicadas correctamente!\n";
    echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak4' . "\n";
} else {
    echo "No se encontraron más problemas que corregir.\n";
}
