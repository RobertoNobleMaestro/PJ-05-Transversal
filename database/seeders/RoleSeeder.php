<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['nombre' => 'admin'],       // ID: 1
            ['nombre' => 'usuario'],     // ID: 2
            ['nombre' => 'gestor'],      // ID: 3
            ['nombre' => 'mecanico'],    // ID: 4
            ['nombre' => 'admin_financiero'] // ID: 5
        ]);
    }
}
