<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ImagenVehiculoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('imagen_vehiculo')->insert([
            // Coches
            [
                'nombre_archivo' => 'corolla.png',
                'id_vehiculo' => 1,
            ],
            [
                'nombre_archivo' => 'golf.png',
                'id_vehiculo' => 2,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 3,
            ],
            [
                'nombre_archivo' => 'clio.png',
                'id_vehiculo' => 4,
            ],
            [
                'nombre_archivo' => '308.png',
                'id_vehiculo' => 5,
            ],
            [
                'nombre_archivo' => 'i30.png',
                'id_vehiculo' => 6,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 7,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 8,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 9,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 10,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 11,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 12,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 13,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 14,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 15,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 16,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 17,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 18,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 19,
            ],
            [
                'nombre_archivo' => 'focus.png',
                'id_vehiculo' => 20,
            ]
        ]);
    }
}
