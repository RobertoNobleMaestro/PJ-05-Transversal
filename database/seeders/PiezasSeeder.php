<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PiezasSeeder extends Seeder
{
    public function run()
    {
        DB::table('piezas')->insert([
            // Coche
            ['nombre' => 'Filtro de aceite', 'tipo_vehiculo' => 'coche', 'precio' => 15.50],
            ['nombre' => 'Pastillas de freno', 'tipo_vehiculo' => 'coche', 'precio' => 45.00],
            ['nombre' => 'Batería', 'tipo_vehiculo' => 'coche', 'precio' => 80.00],
            // Moto
            ['nombre' => 'Cadena', 'tipo_vehiculo' => 'moto', 'precio' => 25.00],
            ['nombre' => 'Filtro de aire', 'tipo_vehiculo' => 'moto', 'precio' => 12.00],
            ['nombre' => 'Neumático', 'tipo_vehiculo' => 'moto', 'precio' => 60.00],
            // Furgoneta
            ['nombre' => 'Amortiguador', 'tipo_vehiculo' => 'furgoneta', 'precio' => 95.00],
            ['nombre' => 'Correa de distribución', 'tipo_vehiculo' => 'furgoneta', 'precio' => 120.00],
            ['nombre' => 'Radiador', 'tipo_vehiculo' => 'furgoneta', 'precio' => 110.00],
        ]);
    }
}
