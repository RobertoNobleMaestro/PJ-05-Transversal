<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ParkingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar parkings de Barcelona (4)
    DB::table('parking')->insert([
    // Barcelona
    [
        'id' => 1,
        'nombre' => 'Parking Barcelona Centro',
        'plazas' => 120,
        'latitud' => 41.3851,
        'longitud' => 2.1734,
        'id_usuario' => 1,
        'id_lugar' => 2,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 2,
        'nombre' => 'Parking Barcelona Sants',
        'plazas' => 85,
        'latitud' => 41.3780,
        'longitud' => 2.1408,
        'id_usuario' => 1,
        'id_lugar' => 2,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 3,
        'nombre' => 'Parking Barcelona Sagrada Familia',
        'plazas' => 150,
        'latitud' => 41.4036,
        'longitud' => 2.1744,
        'id_usuario' => 1,
        'id_lugar' => 2,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 4,
        'nombre' => 'Parking Barcelona Diagonal',
        'plazas' => 200,
        'latitud' => 41.3917,
        'longitud' => 2.1469,
        'id_usuario' => 1,
        'id_lugar' => 2,
        'created_at' => now(),
        'updated_at' => now()
    ],

    // Madrid
    [
        'id' => 5,
        'nombre' => 'Parking Madrid Sol',
        'plazas' => 100,
        'latitud' => 40.4168,
        'longitud' => -3.7038,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 6,
        'nombre' => 'Parking Madrid Atocha',
        'plazas' => 180,
        'latitud' => 40.4066,
        'longitud' => -3.6892,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 7,
        'nombre' => 'Parking Madrid Chamartín',
        'plazas' => 130,
        'latitud' => 40.4722,
        'longitud' => -3.6827,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 8,
        'nombre' => 'Parking Madrid Bernabéu',
        'plazas' => 220,
        'latitud' => 40.4531,
        'longitud' => -3.6883,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 9,
        'nombre' => 'Parking Madrid Retiro',
        'plazas' => 90,
        'latitud' => 40.4153,
        'longitud' => -3.6844,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 10,
        'nombre' => 'Parking Madrid Gran Vía',
        'plazas' => 160,
        'latitud' => 40.4203,
        'longitud' => -3.7058,
        'id_usuario' => 1,
        'id_lugar' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ],

    // Valencia
    [
        'id' => 11,
        'nombre' => 'Parking Valencia Puerto',
        'plazas' => 110,
        'latitud' => 39.4510,
        'longitud' => -0.3198,
        'id_usuario' => 1,
        'id_lugar' => 3,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 12,
        'nombre' => 'Parking Valencia Ciudad de las Artes',
        'plazas' => 170,
        'latitud' => 39.4541,
        'longitud' => -0.3535,
        'id_usuario' => 1,
        'id_lugar' => 3,
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'id' => 13,
        'nombre' => 'Parking Valencia Centro',
        'plazas' => 140,
        'latitud' => 39.4699,
        'longitud' => -0.3763,
        'id_usuario' => 1,
        'id_lugar' => 3,
        'created_at' => now(),
        'updated_at' => now()
    ],
]);

}
}
