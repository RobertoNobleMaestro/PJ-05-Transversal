<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vehiculos')->insert([
            [
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'anio' => 2023,
                'kilometraje' => 15000,
                'seguro_incluido' => true,
                'id_lugar' => 1,
                'id_tipo' => 1, // Coche
            ],
            [
                'marca' => 'Honda',
                'modelo' => 'CBR 600',
                'anio' => 2022,
                'kilometraje' => 8000,
                'seguro_incluido' => true,
                'id_lugar' => 2,
                'id_tipo' => 2, // Moto
            ],
            [
                'marca' => 'Mercedes',
                'modelo' => 'Sprinter',
                'anio' => 2023,
                'kilometraje' => 25000,
                'seguro_incluido' => true,
                'id_lugar' => 3,
                'id_tipo' => 3, // Furgoneta
            ],
        ]);
    }
}
