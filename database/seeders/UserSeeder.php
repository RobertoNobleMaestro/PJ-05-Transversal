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

        // Choferes por sede
        // Choferes Barcelona
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 1',
            'email' => 'chofer.barcelona1@carflow.com',
            'DNI' => '80111111A',
            'telefono' => '600000001',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 2',
            'email' => 'chofer.barcelona2@carflow.com',
            'DNI' => '80111112B',
            'telefono' => '600000002',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-02-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 3',
            'email' => 'chofer.barcelona3@carflow.com',
            'DNI' => '80111113C',
            'telefono' => '600000003',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-03-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 4',
            'email' => 'chofer.barcelona4@carflow.com',
            'DNI' => '80111114D',
            'telefono' => '600000004',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-04-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 5',
            'email' => 'chofer.barcelona5@carflow.com',
            'DNI' => '80111115E',
            'telefono' => '600000005',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-05-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 6',
            'email' => 'chofer.barcelona6@carflow.com',
            'DNI' => '80111116F',
            'telefono' => '600000006',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-06-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Barcelona 7',
            'email' => 'chofer.barcelona7@carflow.com',
            'DNI' => '80111117G',
            'telefono' => '600000007',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1990-07-01',
            'direccion' => 'Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Choferes Madrid
        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 1',
            'email' => 'chofer.madrid1@carflow.com',
            'DNI' => '80211111A',
            'telefono' => '600000101',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-01-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 2',
            'email' => 'chofer.madrid2@carflow.com',
            'DNI' => '80211112B',
            'telefono' => '600000102',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-02-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 3',
            'email' => 'chofer.madrid3@carflow.com',
            'DNI' => '80211113C',
            'telefono' => '600000103',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-03-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 4',
            'email' => 'chofer.madrid4@carflow.com',
            'DNI' => '80211114D',
            'telefono' => '600000104',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-04-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 5',
            'email' => 'chofer.madrid5@carflow.com',
            'DNI' => '80211115E',
            'telefono' => '600000105',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-05-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 6',
            'email' => 'chofer.madrid6@carflow.com',
            'DNI' => '80211116F',
            'telefono' => '600000106',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-06-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 7',
            'email' => 'chofer.madrid7@carflow.com',
            'DNI' => '80211117G',
            'telefono' => '600000107',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-07-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 8',
            'email' => 'chofer.madrid8@carflow.com',
            'DNI' => '80211118H',
            'telefono' => '600000108',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-08-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 9',
            'email' => 'chofer.madrid9@carflow.com',
            'DNI' => '80211119I',
            'telefono' => '600000109',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-09-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Madrid 10',
            'email' => 'chofer.madrid10@carflow.com',
            'DNI' => '80211120J',
            'telefono' => '600000110',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1989-10-01',
            'direccion' => 'Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // Choferes Valencia (6)
        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 1',
            'email' => 'chofer.valencia1@carflow.com',
            'DNI' => '80311111A',
            'telefono' => '600000201',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-01-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 2',
            'email' => 'chofer.valencia2@carflow.com',
            'DNI' => '80311112B',
            'telefono' => '600000202',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-02-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 3',
            'email' => 'chofer.valencia3@carflow.com',
            'DNI' => '80311113C',
            'telefono' => '600000203',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-03-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 4',
            'email' => 'chofer.valencia4@carflow.com',
            'DNI' => '80311114D',
            'telefono' => '600000204',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-04-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 5',
            'email' => 'chofer.valencia5@carflow.com',
            'DNI' => '80311115E',
            'telefono' => '600000205',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-05-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('users')->insert([
            'nombre' => 'Chofer Valencia 6',
            'email' => 'chofer.valencia6@carflow.com',
            'DNI' => '80311116F',
            'telefono' => '600000206',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1991-06-01',
            'direccion' => 'Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 6,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mecánicos Adicionales - Especialistas
        
        // Mecánico Jefe Barcelona
        DB::table('users')->insert([
            'nombre' => 'MecanicoBCN',
            'email' => 'mecanicobcn@carflow.com',
            'DNI' => '50000001J',
            'telefono' => '620000001',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1975-05-15',
            'direccion' => 'Calle Industria 123, Barcelona',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 4, // Rol de mecánico
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mecánico Jefe Madrid
        DB::table('users')->insert([
            'nombre' => 'MecanicoMAD',
            'email' => 'jefe.mecanico.mad@carflow.com',
            'DNI' => '60000001J',
            'telefono' => '630000001',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1978-07-22',
            'direccion' => 'Avenida de los Talleres 45, Madrid',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 4, // Rol de mecánico
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mecánico Jefe Valencia
        DB::table('users')->insert([
            'nombre' => 'MecanicoVAL',
            'email' => 'jefe.mecanico.val@carflow.com',
            'DNI' => '70000001J',
            'telefono' => '640000001',
            'password' => Hash::make('asdASD123'),
            'fecha_nacimiento' => '1980-03-10',
            'direccion' => 'Calle del Taller 78, Valencia',
            'foto_perfil' => null,
            'licencia_conducir' => 'B',
            'id_roles' => 4, // Rol de mecánico
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Mecánicos Especialistas por ciudad
        $especialidades = [
            'Electricidad' => 'Especialista en Sistemas Eléctricos',
            'Motor' => 'Especialista en Motores',
            'Transmisión' => 'Especialista en Transmisiones',
            'Híbridos' => 'Especialista en Vehículos Híbridos',
            'Diagnóstico' => 'Especialista en Diagnóstico',
            'Suspensión' => 'Especialista en Suspensión'
        ];
        
        $ciudades = ['Barcelona' => '5', 'Madrid' => '6', 'Valencia' => '7'];
        $contador = 1;
        
        foreach ($especialidades as $clave => $especialidad) {
            foreach ($ciudades as $ciudad => $prefijo) {
                $contador_str = str_pad($contador, 6, '0', STR_PAD_LEFT);
                DB::table('users')->insert([
                    'nombre' => "Mecánico {$especialidad} - {$ciudad}",
                    'email' => "mecanico." . strtolower(str_replace(' ', '', $clave)) . ".{$ciudad}@carflow.com",
                    'DNI' => "{$prefijo}{$contador_str}E",
                    'telefono' => "6{$prefijo}" . $contador_str,
                    'password' => Hash::make('asdASD123'),
                    'fecha_nacimiento' => '198' . rand(0, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'direccion' => "Taller de {$ciudad}, Zona {$contador}",
                    'foto_perfil' => null,
                    'licencia_conducir' => 'B',
                    'id_roles' => 4, // Rol de mecánico
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $contador++;
            }
        }
    }
}
