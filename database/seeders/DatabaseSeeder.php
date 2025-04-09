<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

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
            UserSeeder::class,
            VehiculoSeeder::class,
            ImagenVehiculoSeeder::class,
            CaracteristicaSeeder::class,
            ReservaSeeder::class,
            VehiculosReservasSeeder::class,
            PagoSeeder::class,
            MetodoPagoSeeder::class,
            ValoracionSeeder::class,
            CarritoSeeder::class,
            ReservaCompletaSeeder::class,
        ]);
    }
}
