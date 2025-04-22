<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        // Insertar reservas base
        DB::table('reservas')->insert([
            [
                'id_reservas' => 1,
                'fecha_reserva' => '2024-04-07',
                'total_precio' => 150.00,
                'estado' => 'completada',
                'id_lugar' => 1,
                'id_usuario' => 2,
            ],
            [
                'id_reservas' => 2,
                'fecha_reserva' => '2024-04-10',
                'total_precio' => 90.00,
                'estado' => 'completada',
                'id_lugar' => 2,
                'id_usuario' => 2,
            ],
            [
                'id_reservas' => 3,
                'fecha_reserva' => '2024-04-15',
                'total_precio' => 220.00,
                'estado' => 'pendiente',
                'id_lugar' => 1,
                'id_usuario' => 3,
            ],
            [
                'id_reservas' => 4,
                'fecha_reserva' => '2024-04-20',
                'total_precio' => 180.00,
                'estado' => 'confirmada',
                'id_lugar' => 3,
                'id_usuario' => 4,
            ],
            [
                'id_reservas' => 5,
                'fecha_reserva' => '2024-04-22',
                'total_precio' => 300.00,
                'estado' => 'cancelada',
                'id_lugar' => 2,
                'id_usuario' => 5,
            ],
            [
                'id_reservas' => 6,
                'fecha_reserva' => '2024-04-25',
                'total_precio' => 450.00,
                'estado' => 'pendiente',
                'id_lugar' => 1,
                'id_usuario' => 3,
            ],
        ]);
        
        // Insertar las relaciones entre vehículos y reservas
        DB::table('vehiculos_reservas')->insert([
            // Vehículos para la reserva 1
            [
                'id_vehiculos' => 1,
                'id_reservas' => 1,
                'fecha_ini' => '2024-04-07',
                'fecha_final' => '2024-04-09',
                'precio_unitario' => 150.00,
            ],
            
            // Vehículos para la reserva 2
            [
                'id_vehiculos' => 2,
                'id_reservas' => 2,
                'fecha_ini' => '2024-04-10',
                'fecha_final' => '2024-04-11',
                'precio_unitario' => 90.00,
            ],
            
            // Vehículos para la reserva 3
            [
                'id_vehiculos' => 3,
                'id_reservas' => 3,
                'fecha_ini' => '2024-04-15',
                'fecha_final' => '2024-04-18',
                'precio_unitario' => 220.00,
            ],
            
            // Vehículos para la reserva 4
            [
                'id_vehiculos' => 4,
                'id_reservas' => 4,
                'fecha_ini' => '2024-04-20',
                'fecha_final' => '2024-04-22',
                'precio_unitario' => 180.00,
            ],
            
            // Vehículos para la reserva 5 (múltiples vehículos)
            [
                'id_vehiculos' => 1,
                'id_reservas' => 5,
                'fecha_ini' => '2024-04-22',
                'fecha_final' => '2024-04-24',
                'precio_unitario' => 150.00,
            ],
            [
                'id_vehiculos' => 2,
                'id_reservas' => 5,
                'fecha_ini' => '2024-04-22',
                'fecha_final' => '2024-04-24',
                'precio_unitario' => 150.00,
            ],
            
            // Vehículos para la reserva 6 (múltiples vehículos)
            [
                'id_vehiculos' => 3,
                'id_reservas' => 6,
                'fecha_ini' => '2024-04-25',
                'fecha_final' => '2024-04-28',
                'precio_unitario' => 225.00,
            ],
            [
                'id_vehiculos' => 4,
                'id_reservas' => 6,
                'fecha_ini' => '2024-04-25',
                'fecha_final' => '2024-04-28',
                'precio_unitario' => 225.00,
            ],
        ]);
    }
}
