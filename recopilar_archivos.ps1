# Script para recopilar migraciones y seeders
$projectPath = Get-Location

# Recopilar migraciones
$migrationsPath = Join-Path -Path $projectPath -ChildPath "database\migrations"
$outputMigrationsFile = Join-Path -Path $projectPath -ChildPath "todas_las_migraciones_AdriV03.txt"

# Iniciar archivo de migraciones
"# TODAS LAS MIGRACIONES DEL PROYECTO CARFLOW (RAMA AdriV0.3)`n`nEste archivo contiene todas las migraciones del proyecto organizadas por nombre de archivo.`n" | Out-File -FilePath $outputMigrationsFile -Encoding utf8

# Procesar cada archivo de migración
Get-ChildItem -Path $migrationsPath -Filter "*.php" | Sort-Object Name | ForEach-Object {
    $filename = $_.Name
    $content = Get-Content -Path $_.FullName -Raw
    
    # Añadir al archivo de salida
    "`n## $filename`n`n```php`n$content`n````n" | Out-File -FilePath $outputMigrationsFile -Append -Encoding utf8
    
    Write-Host "Procesada migración: $filename"
}

# Recopilar seeders
$seedersPath = Join-Path -Path $projectPath -ChildPath "database\seeders"
$outputSeedersFile = Join-Path -Path $projectPath -ChildPath "todos_los_seeders_AdriV03.txt"

# Iniciar archivo de seeders
"# TODOS LOS SEEDERS DEL PROYECTO CARFLOW (RAMA AdriV0.3)`n`nEste archivo contiene todos los seeders del proyecto organizados por nombre de archivo.`n" | Out-File -FilePath $outputSeedersFile -Encoding utf8

# Procesar cada archivo de seeder
Get-ChildItem -Path $seedersPath -Filter "*.php" | Sort-Object Name | ForEach-Object {
    $filename = $_.Name
    $content = Get-Content -Path $_.FullName -Raw
    
    # Añadir al archivo de salida
    "`n## $filename`n`n```php`n$content`n````n" | Out-File -FilePath $outputSeedersFile -Append -Encoding utf8
    
    Write-Host "Procesado seeder: $filename"
}

Write-Host "`nProceso completado. Los archivos se encuentran en:`n$outputMigrationsFile`n$outputSeedersFile"
