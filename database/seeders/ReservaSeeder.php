<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {

        for ($i = 1; $i <= 80; $i++) {

            $transmisiones = ['Automática', 'Manual'];
            $etiquetas = ['Sin etiqueta', 'B', 'C', 'ECO', '0'];
            $puertas = [0, 3, 4, 5, 7];
            $maleteros = [200, 300, 450, 700];

            $reservas[] = [
                'fecha_reserva' => '2024-04-07',
                'total_precio' => 150.00,
                'estado' => 'completada',
                'id_lugar' => 1,
                'id_usuario' => 2,

                'techo' => false,
                'transmision' => $transmisiones[array_rand($transmisiones)],
                'id_vehiculos' => $i,
                'num_puertas' => $puertas[array_rand($puertas)],
                'etiqueta_medioambiental' => $etiquetas[array_rand($etiquetas)],
                'aire_acondicionado' => true,
                'capacidad_maletero' => $maleteros[array_rand($maleteros)],
            ];
        }

        DB::table('reservas')->insert([
            [
                'fecha_reserva' => '2024-04-07',
                'total_precio' => 150.00,
                'estado' => 'completada',
                'id_lugar' => 1,
                'id_usuario' => 2,
            ],
            [
                'fecha_reserva' => '2024-04-07',
                'total_precio' => 90.00,
                'estado' => 'completada',
                'id_lugar' => 2,
                'id_usuario' => 2,
            ],
        ]);
    }
}
