<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('pago')->insert([
            [
                'estado_pago' => 'completado',
                'fecha_pago' => '2024-04-07',
                'referencia_externa' => 'PAY-1234567890',
                'monto_pagado' => 150.00,
                'total_precio' => 150.00,
                'moneda' => 'EUR',
                'id_usuario' => 2, // Usuario Regular
                'id_reservas' => 1, // Primera reserva
            ],
            [
                'estado_pago' => 'completado',
                'fecha_pago' => '2024-04-07',
                'referencia_externa' => 'PAY-0987654321',
                'monto_pagado' => 90.00,
                'total_precio' => 90.00,
                'moneda' => 'EUR',
                'id_usuario' => 2, // Usuario Regular
                'id_reservas' => 2, // Segunda reserva
            ],
        ]);
    }
}
