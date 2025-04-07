<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class VehiculosReservasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'fecha_ini' => Carbon::now()->addDays(1)->toDateString(),
                'fecha_final' => Carbon::now()->addDays(2)->toDateString(),
                'precio_unitario' => 75.00,
                'id_reservas' => 1,
                'id_vehiculos' => 1,
            ],
            [
                'fecha_ini' => Carbon::now()->addDays(3)->toDateString(),
                'fecha_final' => Carbon::now()->addDays(4)->toDateString(),
                'precio_unitario' => 45.00,
                'id_reservas' => 2,
                'id_vehiculos' => 2,
            ],
            [
                'fecha_ini' => Carbon::now()->addDays(2)->toDateString(),
                'fecha_final' => Carbon::now()->addDays(3)->toDateString(),
                'precio_unitario' => 90.00,
                'id_reservas' => 1,
                'id_vehiculos' => 3,
            ],
        ];

        foreach ($data as $item) {
            DB::table('vehiculos_reservas')->insert([
                ...$item,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}