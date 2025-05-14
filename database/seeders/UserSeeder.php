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

        // Gestores por sede
        // Gestor Barcelona
        DB::table('users')->insert([
            'nombre' => 'Gestor Barcelona',
            'email' => 'gestor.barcelona@carflow.com',
            'DNI' => '23456789B',
            'telefono' => '615449359',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Calle Barcelona 123',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Gestor Madrid
        DB::table('users')->insert([
            'nombre' => 'Gestor Madrid',
            'email' => 'gestor.madrid@carflow.com',
            'DNI' => '34567890C',
            'telefono' => '615449360',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1988-05-15',
            'direccion' => 'Calle Madrid 456',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Gestor Valencia
        DB::table('users')->insert([
            'nombre' => 'Gestor Valencia',
            'email' => 'gestor.valencia@carflow.com',
            'DNI' => '45678901D',
            'telefono' => '615449361',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1992-08-20',
            'direccion' => 'Calle Valencia 789',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 3,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mecánicos por sede (4 por cada sede)
        // Mecánicos Barcelona
        for ($i = 1; $i <= 4; $i++) {
            DB::table('users')->insert([
                'nombre' => "Mecánico Barcelona $i",
                'email' => "mecanico.barcelona$i@carflow.com",
                'DNI' => '5' . str_pad($i, 7, '0', STR_PAD_LEFT) . 'E',
                'telefono' => '62' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('asdASD123'),
                'fecha_nacimiento' => '199' . $i . '-01-01',
                'direccion' => "Taller Barcelona $i",
                'foto_perfil' => null,
                'licencia_conducir' => 'B',
                'id_roles' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Mecánicos Madrid
        for ($i = 1; $i <= 4; $i++) {
            DB::table('users')->insert([
                'nombre' => "Mecánico Madrid $i",
                'email' => "mecanico.madrid$i@carflow.com",
                'DNI' => '6' . str_pad($i, 7, '0', STR_PAD_LEFT) . 'F',
                'telefono' => '63' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('asdASD123'),
                'fecha_nacimiento' => '198' . $i . '-06-15',
                'direccion' => "Taller Madrid $i",
                'foto_perfil' => null,
                'licencia_conducir' => 'B',
                'id_roles' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Mecánicos Valencia
        for ($i = 1; $i <= 4; $i++) {
            DB::table('users')->insert([
                'nombre' => "Mecánico Valencia $i",
                'email' => "mecanico.valencia$i@carflow.com",
                'DNI' => '7' . str_pad($i, 7, '0', STR_PAD_LEFT) . 'G',
                'telefono' => '64' . str_pad($i, 7, '0', STR_PAD_LEFT),
                'password' => Hash::make('asdASD123'),
                'fecha_nacimiento' => '199' . $i . '-09-20',
                'direccion' => "Taller Valencia $i",
                'foto_perfil' => null,
                'licencia_conducir' => 'B',
                'id_roles' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Administradores financieros por sede
        // Administrador financiero Barcelona
        DB::table('users')->insert([
            'nombre' => 'Admin Financiero Barcelona',
            'email' => 'finanzas.barcelona@carflow.com',
            'DNI' => '87654321H',
            'telefono' => '655001122',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1985-03-15',
            'direccion' => 'Oficina Financiera Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Administrador financiero Madrid
        DB::table('users')->insert([
            'nombre' => 'Admin Financiero Madrid',
            'email' => 'finanzas.madrid@carflow.com',
            'DNI' => '76543210I',
            'telefono' => '655112233',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1983-07-22',
            'direccion' => 'Oficina Financiera Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Administrador financiero Valencia
        DB::table('users')->insert([
            'nombre' => 'Admin Financiero Valencia',
            'email' => 'finanzas.valencia@carflow.com',
            'DNI' => '65432109J',
            'telefono' => '655334455',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1987-11-05',
            'direccion' => 'Oficina Financiera Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 5,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 20 Clientes
        for ($i = 1; $i <= 20; $i++) {
            DB::table('users')->insert([
                'nombre' => "Cliente $i",
                'email' => "cliente$i@carflow.com",
                'DNI' => str_pad($i, 8, '0', STR_PAD_LEFT) . 'C',
                'telefono' => '615449359',
                'password' => Hash::make('asdASD123'),
                'fecha_nacimiento' => '1995-01-01',
                'direccion' => "Calle Cliente $i",
                'foto_perfil' => '/default.png',
                'licencia_conducir' => 'B',
                'id_roles' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Cliente 21 con email específico
        DB::table('users')->insert([
            'nombre' => "Cliente 21",
            'email' => "alegofe04@gmail.com",
            'DNI' => '00000021C',
            'telefono' => '615449359',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1995-01-01',
            'direccion' => "Calle Cliente 21",
            'foto_perfil' => '/default.png',
            'licencia_conducir' => 'B',
            'id_roles' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Cliente con correo gofe007@outlook.com
        DB::table('users')->insert([
            'nombre' => "Cliente Outlook",
            'email' => "gofe007@outlook.com",
            'DNI' => '00000022C',
            'telefono' => '615449359',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1995-01-01',
            'direccion' => "Calle Cliente Outlook",
            'foto_perfil' => '/default.png',
            'licencia_conducir' => 'B',
            'id_roles' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}