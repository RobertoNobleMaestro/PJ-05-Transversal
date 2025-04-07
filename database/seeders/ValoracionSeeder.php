<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ValoracionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('valoraciones')->insert([
            [
                'comentario' => 'Excelente servicio, muy satisfecho',
                'valoracion' => 5,
                'id_reservas' => 1,
                'id_usuario' => 2,
            ],
            [
                'comentario' => 'Muy buena experiencia',
                'valoracion' => 4,
                'id_reservas' => 2,
                'id_usuario' => 2,
            ],
        ]);
    }
}
