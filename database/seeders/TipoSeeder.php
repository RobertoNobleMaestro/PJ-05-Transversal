<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos')->insert([
            ['nombre_tipo' => 'Coche', 'descripcion' => 'Vehículo de cuatro ruedas para transporte de personas'],
            ['nombre_tipo' => 'Moto', 'descripcion' => 'Vehículo de dos ruedas'],
            ['nombre_tipo' => 'Furgoneta', 'descripcion' => 'Vehículo para transporte de mercancías ligeras'],
            ['nombre_tipo' => 'Camión', 'descripcion' => 'Vehículo de gran tamaño para transporte de mercancías pesadas'],
        ]);
    }
}
