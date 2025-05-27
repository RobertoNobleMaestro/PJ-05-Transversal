<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TestParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar si ya existen parkings
        $existingParkings = DB::table('parking')->count();
        
        if ($existingParkings > 0) {
            $this->command->info('Ya existen parkings en la base de datos. No se crearán parkings adicionales.');
            return;
        }
        
        // Obtener lugares disponibles
        $lugares = DB::table('lugares')->get();
        
        if ($lugares->isEmpty()) {
            // Si no hay lugares, crear uno
            $lugarId = DB::table('lugares')->insertGetId([
                'nombre' => 'Parking Central',
                'direccion' => 'Calle Principal 123',
                'latitud' => 40.416775,
                'longitud' => -3.703790,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            $lugares = collect([
                (object)['id_lugar' => $lugarId]
            ]);
            $this->command->info('Se ha creado un lugar porque no existía ninguno.');
        }
        
        // Crear al menos 2 parkings
        foreach ($lugares->take(2) as $index => $lugar) {
            DB::table('parking')->insert([
                'nombre' => 'Parking ' . ($index + 1),
                'descripcion' => 'Parking de prueba ' . ($index + 1),
                'plazas_totales' => rand(10, 50),
                'plazas_ocupadas' => 0,
                'id_lugar' => $lugar->id_lugar,
                'metros_cuadrados' => rand(500, 2000),
                'precio_plaza' => rand(50, 150),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
            
            $parkingNum = $index + 1;
            $this->command->info("Parking {$parkingNum} creado correctamente.");
        }
    }
}
