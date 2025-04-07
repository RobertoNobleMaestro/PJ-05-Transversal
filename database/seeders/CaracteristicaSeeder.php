<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaracteristicaSeeder extends Seeder
{
    public function run(): void
    {
        // Las características se añadirán después de crear los vehículos
        $this->call([
            VehiculoSeeder::class,
        ]);

        DB::table('caracteristicas')->insert([
            [
                'techo' => false,
                'transmision' => 'Automática',
                'id_vehiculos' => 1,
                'num_puertas' => 5,
                'etiqueta_medioambiental' => 'ECO',
                'aire_acondicionado' => true,
                'capacidad_maletero' => 450,
            ],
            [
                'techo' => false,
                'transmision' => 'Manual',
                'id_vehiculos' => 2,
                'num_puertas' => 0,
                'etiqueta_medioambiental' => 'C',
                'aire_acondicionado' => false,
                'capacidad_maletero' => 0,
            ],
            [
                'techo' => false,
                'transmision' => 'Manual',
                'id_vehiculos' => 3,
                'num_puertas' => 5,
                'etiqueta_medioambiental' => 'B',
                'aire_acondicionado' => true,
                'capacidad_maletero' => 1200,
            ],
        ]);
    }
}
