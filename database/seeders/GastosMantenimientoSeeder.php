<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastosMantenimientoSeeder extends Seeder
{
    /**
     * Run the database seeds to create maintenance expenses for vehicles and parkings.
     */
    public function run(): void
    {
        $this->command->info('Creando gastos de mantenimiento...');

        // Obtener todos los vehículos
        $vehiculos = DB::table('vehiculos')->get();
        
        // Obtener todos los parkings
        $parkings = DB::table('parking')->get();

        // Fechas para el mes actual (para que aparezcan en el período actual)
        $fechaInicio = Carbon::now()->startOfMonth();
        $fechaFin = Carbon::now()->endOfMonth();
        
        // 1. Crear gastos de mantenimiento para vehículos
        foreach ($vehiculos as $index => $vehiculo) {
            // Solo creamos gastos para un porcentaje de vehículos (70%)
            if (rand(1, 100) <= 70) {
                // Determinar el tipo de mantenimiento
                $tiposMantenimiento = [
                    'Cambio de aceite y filtros',
                    'Revisión general',
                    'Cambio de neumáticos',
                    'Reparación de frenos',
                    'Mantenimiento de aire acondicionado',
                    'Reemplazo de piezas',
                    'Revisión de suspensión'
                ];
                
                $tipoMantenimiento = $tiposMantenimiento[array_rand($tiposMantenimiento)];
                
                // Calcular un costo de mantenimiento basado en el tipo y precio del vehículo
                $costoBase = 300; // Costo base
                $precioPorcentaje = $vehiculo->precio * 0.01; // 1% del valor del vehículo
                $variacion = rand(-100, 200); // Variación aleatoria
                
                $costo = round($costoBase + $precioPorcentaje + $variacion, 2);
                
                // Generar una fecha aleatoria dentro del mes actual
                $fecha = Carbon::create($fechaInicio->year, $fechaInicio->month, rand(1, $fechaFin->day));
                
                // Buscar si existe un mantenimiento para este vehículo
                $mantenimiento = DB::table('mantenimientos')
                    ->where('vehiculo_id', $vehiculo->id_vehiculos)
                    ->first();
                
                // Guardar el gasto de mantenimiento
                DB::table('gastos')->insert([
                    'concepto' => "Mantenimiento: $tipoMantenimiento",
                    'descripcion' => "Mantenimiento $tipoMantenimiento para vehículo {$vehiculo->marca} {$vehiculo->modelo}",
                    'tipo' => 'mantenimiento',
                    'importe' => $costo,
                    'fecha' => $fecha,
                    'id_vehiculo' => $vehiculo->id_vehiculos,
                    'id_mantenimiento' => $mantenimiento ? $mantenimiento->id : null,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        
        // 2. Crear gastos de mantenimiento para parkings
        foreach ($parkings as $index => $parking) {
            // Determinar varios tipos de mantenimiento por parking
            $tiposMantenimientoParking = [
                'Limpieza general',
                'Mantenimiento eléctrico',
                'Reparación de señalización',
                'Mantenimiento de seguridad',
                'Reparación de suelo',
                'Pintura de plazas',
                'Mantenimiento de ascensores'
            ];
            
            // Crear entre 1 y 3 gastos de mantenimiento por parking
            $numGastos = rand(1, 3);
            
            for ($i = 0; $i < $numGastos; $i++) {
                $tipoMantenimiento = $tiposMantenimientoParking[array_rand($tiposMantenimientoParking)];
                
                // Calcular costo basado en el tamaño del parking
                $costoBase = 150; // Costo base
                $costoPorPlaza = $parking->plazas * 2; // 2€ por plaza
                $variacion = rand(-50, 100); // Variación aleatoria
                
                $costo = round($costoBase + $costoPorPlaza + $variacion, 2);
                
                // Generar una fecha aleatoria dentro del mes actual
                $fecha = Carbon::create($fechaInicio->year, $fechaInicio->month, rand(1, $fechaFin->day));
                
                // Guardar el gasto de mantenimiento
                DB::table('gastos')->insert([
                    'concepto' => "Mantenimiento Parking: $tipoMantenimiento",
                    'descripcion' => "Mantenimiento $tipoMantenimiento para {$parking->nombre}",
                    'tipo' => 'parking',
                    'importe' => $costo,
                    'fecha' => $fecha,
                    'id_parking' => $parking->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        
        $this->command->info('Gastos de mantenimiento creados correctamente.');
    }
}
