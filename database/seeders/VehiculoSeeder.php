<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 80; $i++) {
            $vehiculos[] = [
                'marca' => 'Marca ' . $i,
                'modelo' => 'Modelo ' . $i,
                'año' => rand(2018, 2024),
                'kilometraje' => rand(5000, 100000),
                'seguro_incluido' => (bool)rand(0, 1),
                'id_lugar' => rand(1, 3),
                'id_tipo' => rand(1, 4),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('vehiculos')->insert($vehiculos);
    }
}