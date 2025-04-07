<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagenVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('imagen_vehiculo')->insert([
            [
                'nombre_archivo' => 'corolla_1.jpg',
                'id_vehiculo' => 1,
            ],
            [
                'nombre_archivo' => 'corolla_2.jpg',
                'id_vehiculo' => 1,
            ],
            [
                'nombre_archivo' => 'cbr600_1.jpg',
                'id_vehiculo' => 2,
            ],
            [
                'nombre_archivo' => 'sprinter_1.jpg',
                'id_vehiculo' => 3,
            ],
        ]);
    }
}
