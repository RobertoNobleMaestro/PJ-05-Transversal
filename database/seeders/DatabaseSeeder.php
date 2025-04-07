<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TipoSeeder;
use Database\Seeders\LugarSeeder;
use Database\Seeders\MetodoPagoSeeder;
use Database\Seeders\CaracteristicaSeeder;
use Database\Seeders\VehiculoSeeder;
use Database\Seeders\ReservaSeeder;
use Database\Seeders\ValoracionSeeder;
use Database\Seeders\PagoSeeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seeders básicos (sin dependencias)
        $this->call([
            RoleSeeder::class,
            TipoSeeder::class,
            LugarSeeder::class,
            MetodoPagoSeeder::class,
            CaracteristicaSeeder::class,
        ]);

        // Create admin user
        User::factory()->create([
            'nombre' => 'Admin',
            'email' => 'admin@example.com',
            'DNI' => '12345678A',
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Calle Admin 1',
            'licencia_conducir' => 'B',
            'id_roles' => 1, // admin role
        ]);

        // Create some regular users
        User::factory()->create([
            'nombre' => 'Usuario Regular',
            'email' => 'usuario@example.com',
            'DNI' => '87654321B',
            'fecha_nacimiento' => '1995-01-01',
            'direccion' => 'Calle Usuario 1',
            'licencia_conducir' => 'B',
            'id_roles' => 2, // usuario role
        ]);

        // Seeders con dependencias
        $this->call([
            VehiculoSeeder::class,
            ReservaSeeder::class,
            ValoracionSeeder::class,
            PagoSeeder::class,
        ]);
    }
}
