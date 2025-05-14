<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VehiculosReservasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('vehiculos_reservas')->insert([
            // Reserva 1 (Madrid) - Vehículo de Madrid
            [
                'id_vehiculos_reservas' => 1,
                'id_vehiculos' => 6, // Hyundai i30 de Madrid
                'id_reservas' => 1,
                'fecha_ini' => '2024-04-07',
                'fecha_final' => '2024-04-09',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 2 (Madrid) - Vehículo de Madrid
            [
                'id_vehiculos_reservas' => 2,
                'id_vehiculos' => 9, // Mazda 3 de Madrid
                'id_reservas' => 2,
                'fecha_ini' => '2024-04-15',
                'fecha_final' => '2024-04-18',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 3 (Madrid) - Múltiples vehículos de Madrid
            [
                'id_vehiculos_reservas' => 3,
                'id_vehiculos' => 12, // Citroen C4 de Madrid
                'id_reservas' => 3,
                'fecha_ini' => '2024-04-25',
                'fecha_final' => '2024-04-28',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 4,
                'id_vehiculos' => 13, // Honda Civic de Madrid
                'id_reservas' => 3,
                'fecha_ini' => '2024-04-25',
                'fecha_final' => '2024-04-28',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 4 (Madrid) - Vehículo de Madrid
            [
                'id_vehiculos_reservas' => 5,
                'id_vehiculos' => 17, // Suzuki Swift de Madrid
                'id_reservas' => 4,
                'fecha_ini' => '2024-05-10',
                'fecha_final' => '2024-05-12',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 5 (Barcelona) - Vehículo de Barcelona
            [
                'id_vehiculos_reservas' => 6,
                'id_vehiculos' => 3, // Ford Focus de Barcelona
                'id_reservas' => 5,
                'fecha_ini' => '2024-04-10',
                'fecha_final' => '2024-04-11',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 6 (Barcelona) - Múltiples vehículos de Barcelona
            [
                'id_vehiculos_reservas' => 7,
                'id_vehiculos' => 5, // Peugeot 308 de Barcelona
                'id_reservas' => 6,
                'fecha_ini' => '2024-04-22',
                'fecha_final' => '2024-04-24',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 8,
                'id_vehiculos' => 8, // Seat Leon de Barcelona
                'id_reservas' => 6,
                'fecha_ini' => '2024-04-22',
                'fecha_final' => '2024-04-24',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 7 (Barcelona) - Vehículo de Barcelona
            [
                'id_vehiculos_reservas' => 9,
                'id_vehiculos' => 11, // Opel Astra de Barcelona
                'id_reservas' => 7,
                'fecha_ini' => '2024-05-15',
                'fecha_final' => '2024-05-18',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 8 (Valencia) - Vehículo de Valencia
            [
                'id_vehiculos_reservas' => 10,
                'id_vehiculos' => 1, // Toyota Corolla de Valencia
                'id_reservas' => 8,
                'fecha_ini' => '2024-04-20',
                'fecha_final' => '2024-04-22',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 9 (Valencia) - Vehículo de Valencia
            [
                'id_vehiculos_reservas' => 11,
                'id_vehiculos' => 4, // Renault Clio de Valencia
                'id_reservas' => 9,
                'fecha_ini' => '2024-05-01',
                'fecha_final' => '2024-05-04',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Reserva 10 (Valencia) - Múltiples vehículos de Valencia
            [
                'id_vehiculos_reservas' => 12,
                'id_vehiculos' => 7, // Kia Ceed de Valencia
                'id_reservas' => 10,
                'fecha_ini' => '2024-05-20',
                'fecha_final' => '2024-05-23',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 13,
                'id_vehiculos' => 10, // Skoda Octavia de Valencia
                'id_reservas' => 10,
                'fecha_ini' => '2024-05-20',
                'fecha_final' => '2024-05-23',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Añadimos algunas reservas de motos
            [
                'id_vehiculos_reservas' => 14,
                'id_vehiculos' => 31, // Yamaha MT-07 de Madrid
                'id_reservas' => 1,
                'fecha_ini' => '2024-04-07',
                'fecha_final' => '2024-04-09',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 15,
                'id_vehiculos' => 32, // Honda CBR500R de Barcelona
                'id_reservas' => 5,
                'fecha_ini' => '2024-04-10',
                'fecha_final' => '2024-04-11',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 16,
                'id_vehiculos' => 34, // BMW G310R de Valencia
                'id_reservas' => 8,
                'fecha_ini' => '2024-04-20',
                'fecha_final' => '2024-04-22',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Añadimos algunas reservas de furgonetas
            [
                'id_vehiculos_reservas' => 17,
                'id_vehiculos' => 51, // Mercedes-Benz Sprinter de Madrid
                'id_reservas' => 2,
                'fecha_ini' => '2024-04-15',
                'fecha_final' => '2024-04-18',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 18,
                'id_vehiculos' => 52, // Ford Transit de Barcelona
                'id_reservas' => 7,
                'fecha_ini' => '2024-05-15',
                'fecha_final' => '2024-05-18',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 19,
                'id_vehiculos' => 53, // Renault Master de Valencia
                'id_reservas' => 9,
                'fecha_ini' => '2024-05-01',
                'fecha_final' => '2024-05-04',
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Añadimos algunas reservas de camiones
            [
                'id_vehiculos_reservas' => 20,
                'id_vehiculos' => 66, // Volvo FL de Madrid
                'id_reservas' => 3,
                'fecha_ini' => '2024-04-25',
                'fecha_final' => '2024-04-28',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 21,
                'id_vehiculos' => 67, // Mercedes-Benz Atego de Barcelona
                'id_reservas' => 6,
                'fecha_ini' => '2024-04-22',
                'fecha_final' => '2024-04-24',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id_vehiculos_reservas' => 22,
                'id_vehiculos' => 68, // DAF LF de Valencia
                'id_reservas' => 10,
                'fecha_ini' => '2024-05-20',
                'fecha_final' => '2024-05-23',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}