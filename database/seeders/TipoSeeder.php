<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar en la tabla "tipo" (singular) en lugar de "tipos" (plural)
        DB::table('tipo')->insert([
            ['id_tipo' => 1, 'nombre' => 'Coche', 'created_at' => now(), 'updated_at' => now()],
            ['id_tipo' => 2, 'nombre' => 'Moto', 'created_at' => now(), 'updated_at' => now()],
            ['id_tipo' => 3, 'nombre' => 'Furgoneta', 'created_at' => now(), 'updated_at' => now()],
            ['id_tipo' => 4, 'nombre' => 'Camión', 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        // Además, también insertar en la tabla "tipos" para mantener consistencia
        DB::table('tipos')->insert([
            ['nombre_tipo' => 'Coche', 'descripcion' => 'Vehículo de cuatro ruedas para transporte de personas'],
            ['nombre_tipo' => 'Moto', 'descripcion' => 'Vehículo de dos ruedas'],
            ['nombre_tipo' => 'Furgoneta', 'descripcion' => 'Vehículo para transporte de mercancías ligeras'],
            ['nombre_tipo' => 'Camión', 'descripcion' => 'Vehículo de gran tamaño para transporte de mercancías pesadas'],
        ]);
    }
}
