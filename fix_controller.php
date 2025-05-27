<?php
// Script para corregir el método presupuestosAmortizado en AdminFinancieroController.php

$controllerPath = __DIR__ . '/app/Http/Controllers/AdminFinancieroController.php';
$content = file_get_contents($controllerPath);

// Buscar y reemplazar el fragmento problemático
$search = "// Obtener la sede actual
        \$usuario = auth()->user();
        \$sede = \$usuario->lugar;
        \$sedeId = \$sede->id_lugar;";

$replace = "// Obtener la sede actual
        \$usuario = auth()->user();
        
        // Comprobar si el usuario tiene lugar asignado
        if (!\$usuario->lugar) {
            // Si el usuario no tiene lugar, usar el lugar del vehículo
            \$sede = \$vehiculo->lugar;
            
            if (!\$sede) {
                // Si el vehículo tampoco tiene lugar, redirigir con error
                return redirect()->route('admin.financiero.balance.activos')
                    ->with('error', 'No se pudo determinar la sede. Por favor, contacte con el administrador.');
            }
        } else {
            \$sede = \$usuario->lugar;
        }
        
        \$sedeId = \$sede->id_lugar;";

// Realizar el reemplazo
$newContent = str_replace($search, $replace, $content);

// Guardar los cambios
if ($newContent !== $content) {
    // Hacer una copia de seguridad
    file_put_contents($controllerPath . '.bak', $content);
    
    // Guardar el archivo modificado
    file_put_contents($controllerPath, $newContent);
    
    echo "¡Corrección aplicada correctamente!\n";
    echo "Se ha creado una copia de seguridad en: " . $controllerPath . '.bak' . "\n";
} else {
    echo "No se pudo aplicar la corrección. El patrón de búsqueda no se encontró exactamente.\n";
}
