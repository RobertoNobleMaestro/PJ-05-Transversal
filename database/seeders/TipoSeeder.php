<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipo')->insert([
            ['nombre' => 'Coche'],
            ['nombre' => 'Moto'],
            ['nombre' => 'Furgoneta'],
            ['nombre' => 'Camión'],
        ]);
    }
}
