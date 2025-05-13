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
            [
                'id' => 1,
                'nombre' => 'Parking Barcelona Centro',
                'plazas' => 120,
                'id_usuario' => 1, // Asumiendo que existe un usuario administrador con id 1
                'id_lugar' => 2, // Barcelona Puerto (basado en el LugarSeeder)
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nombre' => 'Parking Barcelona Sants',
                'plazas' => 85,
                'id_usuario' => 1,
                'id_lugar' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 3,
                'nombre' => 'Parking Barcelona Sagrada Familia',
                'plazas' => 150,
                'id_usuario' => 1,
                'id_lugar' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 4,
                'nombre' => 'Parking Barcelona Diagonal',
                'plazas' => 200,
                'id_usuario' => 1,
                'id_lugar' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Insertar parkings de Madrid (6)
            [
                'id' => 5,
                'nombre' => 'Parking Madrid Sol',
                'plazas' => 100,
                'id_usuario' => 1,
                'id_lugar' => 1, // Madrid Centro (basado en el LugarSeeder)
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 6,
                'nombre' => 'Parking Madrid Atocha',
                'plazas' => 180,
                'id_usuario' => 1,
                'id_lugar' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 7,
                'nombre' => 'Parking Madrid Chamartín',
                'plazas' => 130,
                'id_usuario' => 1,
                'id_lugar' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 8,
                'nombre' => 'Parking Madrid Bernabéu',
                'plazas' => 220,
                'id_usuario' => 1,
                'id_lugar' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 9,
                'nombre' => 'Parking Madrid Retiro',
                'plazas' => 90,
                'id_usuario' => 1,
                'id_lugar' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 10,
                'nombre' => 'Parking Madrid Gran Vía',
                'plazas' => 160,
                'id_usuario' => 1,
                'id_lugar' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            
            // Insertar parkings de Valencia (3)
            [
                'id' => 11,
                'nombre' => 'Parking Valencia Puerto',
                'plazas' => 110,
                'id_usuario' => 1,
                'id_lugar' => 3, // Valencia Ciudad (basado en el LugarSeeder)
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 12,
                'nombre' => 'Parking Valencia Ciudad de las Artes',
                'plazas' => 170,
                'id_usuario' => 1,
                'id_lugar' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 13,
                'nombre' => 'Parking Valencia Centro',
                'plazas' => 140,
                'id_usuario' => 1,
                'id_lugar' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
