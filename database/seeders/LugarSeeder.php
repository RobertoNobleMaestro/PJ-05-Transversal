<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LugarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lugares')->insert([
            [
                'nombre' => 'Madrid Centro',
                'direccion' => 'Calle Gran Vía 1',
                'latitud' => 40.4168,
                'longitud' => -3.7038,
            ],
            [
                'nombre' => 'Barcelona Puerto',
                'direccion' => 'Passeig de Colom 22',
                'latitud' => 41.3751,
                'longitud' => 2.1769,
            ],
            [
                'nombre' => 'Valencia Ciudad',
                'direccion' => 'Avenida del Puerto 15',
                'latitud' => 39.4699,
                'longitud' => -0.3763,
            ],
        ]);
    }
}
