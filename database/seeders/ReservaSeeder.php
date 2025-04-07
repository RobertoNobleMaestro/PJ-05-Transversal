<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
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
