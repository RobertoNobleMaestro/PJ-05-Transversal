<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CarritoSeeder extends Seeder
{
    public function run(): void
    {
        $totalPrecio = 75.00 + 50.00;

        // Crear la reserva con id_lugar obligatorio
        $reservaId = DB::table('reservas')->insertGetId([
            'id_usuario' => 3,
            'id_lugar' => 1, // <- Asegúrate que el lugar con ID 1 existe en tu tabla lugares
            'estado' => 'pendiente',
            'fecha_reserva' => Carbon::now(),
            'total_precio' => $totalPrecio,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Asociar vehículos a esa reserva
        // DB::table('vehiculos_reservas')->insert([
        //     [
        //         'id_reservas' => $reservaId,
        //         'id_vehiculos' => 1,
        //         'fecha_ini' => Carbon::now()->addDays(1),
        //         'fecha_final' => Carbon::now()->addDays(3),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        //     [
        //         'id_reservas' => $reservaId,
        //         'id_vehiculos' => 2,
        //         'fecha_ini' => Carbon::now()->addDays(4),
        //         'fecha_final' => Carbon::now()->addDays(6),
        //         'created_at' => now(),
        //         'updated_at' => now(),
        //     ],
        // ]);
    }
}
