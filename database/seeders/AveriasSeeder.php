<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class AveriasSeeder extends Seeder
{
    public function run()
    {
        // Ejemplo de averías para tres vehículos distintos
        DB::table('averias')->insert([
            [
                'vehiculo_id' => 1,
                'descripcion' => 'Fallo en el sistema de frenos',
                'fecha' => Carbon::now()->subDays(10),
            ],
            [
                'vehiculo_id' => 2,
                'descripcion' => 'Problema con la batería',
                'fecha' => Carbon::now()->subDays(25),
            ],
            [
                'vehiculo_id' => 3,
                'descripcion' => 'Cadena desgastada',
                'fecha' => Carbon::now()->subDays(5),
            ],
        ]);
    }
}
