<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar reservas base
        DB::table('reservas')->insert([
            // Reservas en Madrid (id_lugar = 1)
            [
                'id_reservas' => 1,
                'fecha_reserva' => '2024-04-07',
                'total_precio' => 150.00,
                'estado' => 'completada',
                'id_lugar' => 1,
                'id_usuario' => 2,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 2,
                'fecha_reserva' => '2024-04-15',
                'total_precio' => 220.00,
                'estado' => 'pendiente',
                'id_lugar' => 1,
                'id_usuario' => 3,
                'referencia_pago' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 3,
                'fecha_reserva' => '2024-04-25',
                'total_precio' => 450.00,
                'estado' => 'confirmada',
                'id_lugar' => 1,
                'id_usuario' => 3,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 4,
                'fecha_reserva' => '2024-05-10',
                'total_precio' => 180.00,
                'estado' => 'pendiente',
                'id_lugar' => 1,
                'id_usuario' => 4,
                'referencia_pago' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reservas en Barcelona (id_lugar = 2)
            [
                'id_reservas' => 5,
                'fecha_reserva' => '2024-04-10',
                'total_precio' => 90.00,
                'estado' => 'completada',
                'id_lugar' => 2,
                'id_usuario' => 2,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 6,
                'fecha_reserva' => '2024-04-22',
                'total_precio' => 300.00,
                'estado' => 'cancelada',
                'id_lugar' => 2,
                'id_usuario' => 5,
                'referencia_pago' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 7,
                'fecha_reserva' => '2024-05-15',
                'total_precio' => 210.00,
                'estado' => 'confirmada',
                'id_lugar' => 2,
                'id_usuario' => 4,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reservas en Valencia (id_lugar = 3)
            [
                'id_reservas' => 8,
                'fecha_reserva' => '2024-04-20',
                'total_precio' => 180.00,
                'estado' => 'confirmada',
                'id_lugar' => 3,
                'id_usuario' => 4,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 9,
                'fecha_reserva' => '2024-05-01',
                'total_precio' => 275.00,
                'estado' => 'pendiente',
                'id_lugar' => 3,
                'id_usuario' => 3,
                'referencia_pago' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_reservas' => 10,
                'fecha_reserva' => '2024-05-20',
                'total_precio' => 320.00,
                'estado' => 'confirmada',
                'id_lugar' => 3,
                'id_usuario' => 5,
                'referencia_pago' => 'REF-'.random_int(10000, 99999),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
