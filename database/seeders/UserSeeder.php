<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        DB::table('users')->insert([
            'nombre' => 'Admin',
            'email' => 'admin@carflow.com',
            'DNI' => '12345678A',
            'telefono' => '615449359',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1985-01-01',
            'direccion' => 'Calle Admin 1',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Gestor
        DB::table('users')->insert([
            'nombre' => 'Gestor',
            'email' => 'gestor@carflow.com',
            'DNI' => '23456789B',
            'telefono' => '615449359',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Calle Gestor 2',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 5 Clientes
        for ($i = 1; $i <= 5; $i++) {
            DB::table('users')->insert([
                'nombre' => "Cliente $i",
                'email' => "cliente$i@carflow.com",
                'DNI' => "0000000{$i}C",
                'telefono' => '615449359',
                'password' => Hash::make('asdASD123'),
                'fecha_nacimiento' => '1995-01-01',
                'direccion' => "Calle Cliente $i",
                'foto_perfil' => null,
                'licencia_conducir' => 'B',
                'id_roles' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}