<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CaracteristicaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('caracteristicas')->insert([
            // Características 30 coches
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 1],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 2],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 3],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 4],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 5],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 6],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 7],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 8],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 9],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 10],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 11],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 12],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 13],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 14],
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'Sin etiqueta', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 15],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 16],
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 17],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 18],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 19],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 20],
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 3, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 21],
            ['techo' => false, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 22],
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'Sin etiqueta', 'aire_acondicionado' => true, 'capacidad_maletero' => 700, 'id_vehiculos' => 23],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'Sin etiqueta', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 24],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 25],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 26],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'Sin etiqueta', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 27],
            ['techo' => false, 'transmision' => 'Manual', 'num_puertas' => 5, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 450, 'id_vehiculos' => 28],
            ['techo' => true, 'transmision' => 'Manual', 'num_puertas' => 3, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 29],
            ['techo' => true, 'transmision' => 'Automática', 'num_puertas' => 4, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 300, 'id_vehiculos' => 30],
            // Características 20 motos
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 31, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 50],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 32, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 40],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 33, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 34, 'num_puertas' => 0, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => false, 'capacidad_maletero' => 40],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 35, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 45],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 36, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 50],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 37, 'num_puertas' => 0, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => false, 'capacidad_maletero' => 35],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 38, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 39, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 60],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 40, 'num_puertas' => 0, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => false, 'capacidad_maletero' => 35],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 41, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 42, 'num_puertas' => 0, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => false, 'capacidad_maletero' => 45],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 43, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 50],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 44, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 45, 'num_puertas' => 0, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => false, 'capacidad_maletero' => 35],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 46, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => false, 'capacidad_maletero' => 45],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 47, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 40],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 48, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 49, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => false, 'capacidad_maletero' => 30],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 50, 'num_puertas' => 0, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => false, 'capacidad_maletero' => 35],
            // Características 15 furgonetas
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 51, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 1000],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 52, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 950],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 53, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 1050],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 54, 'num_puertas' => 4, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 980],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 55, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 920],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 56, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 1100],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 57, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 960],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 58, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 1020],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 59, 'num_puertas' => 4, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 970],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 60, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 990],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 61, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 950],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 62, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 1000],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 63, 'num_puertas' => 4, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 1040],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 64, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 1010],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 65, 'num_puertas' => 4, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 970],
            // Características 10 camiones
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 66, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 3000],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 67, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 3200],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 68, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 3100],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 69, 'num_puertas' => 2, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 3300],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 70, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 3000],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 71, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 3400],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 72, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'ECO', 'aire_acondicionado' => true, 'capacidad_maletero' => 3050],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 73, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'C', 'aire_acondicionado' => true, 'capacidad_maletero' => 3250],
            ['techo' => false, 'transmision' => 'Manual', 'id_vehiculos' => 74, 'num_puertas' => 2, 'etiqueta_medioambiental' => '0', 'aire_acondicionado' => true, 'capacidad_maletero' => 3350],
            ['techo' => false, 'transmision' => 'Automática', 'id_vehiculos' => 75, 'num_puertas' => 2, 'etiqueta_medioambiental' => 'B', 'aire_acondicionado' => true, 'capacidad_maletero' => 3100]
        ]);
    }
}