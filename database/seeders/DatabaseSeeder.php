<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tablas base sin dependencias
        $this->call([
            RoleSeeder::class,
            LugarSeeder::class,
            TipoSeeder::class,
        ]);

        // 2. Crear usuarios (después de roles)
        User::create([
            'nombre' => 'Admin',
            'email' => 'admin@example.com',
            'DNI' => '12345678A',
            'fecha_nacimiento' => '1990-01-01',
            'direccion' => 'Calle Admin 1',
            'licencia_conducir' => 'B',
            'id_roles' => 1,
        ]);

        User::create([
            'nombre' => 'Usuario Regular',
            'email' => 'usuario@example.com',
            'DNI' => '87654321B',
            'fecha_nacimiento' => '1995-01-01',
            'direccion' => 'Calle Usuario 1',
            'licencia_conducir' => 'B',
            'id_roles' => 2,
        ]);

        // 3. Vehículos y sus relaciones
        $this->call([
            VehiculoSeeder::class,
            ImagenVehiculoSeeder::class,
            CaracteristicaSeeder::class,
        ]);

        // 4. Reservas y pagos
        $this->call([
            ReservaSeeder::class,
            VehiculosReservasSeeder::class,
            PagoSeeder::class,
            MetodoPagoSeeder::class,
            ValoracionSeeder::class,
        ]);
    }
}