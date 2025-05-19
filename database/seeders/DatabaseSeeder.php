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
        // 1. Primero las tablas base sin dependencias
        $this->call([
            RoleSeeder::class,           // Roles para usuarios
            LugarSeeder::class,          // Lugares/ubicaciones
            TipoSeeder::class,           // Tipos de vehículos
        ]);
        
        // 2. Luego las tablas que dependen de las bases
        $this->call([
            UserSeeder::class,           // Usuarios (dependen de roles)
            ParkingSeeder::class,        // Parkings (dependen de usuarios y lugares)
            VehiculoSeeder::class,       // Vehículos (dependen de tipos, lugares y parkings)
        ]);
        
        // 3. Luego las tablas que dependen de vehículos y usuarios
        $this->call([
            ImagenVehiculoSeeder::class, // Imágenes de vehículos
            CaracteristicaSeeder::class, // Características de vehículos
            ReservaSeeder::class,        // Reservas (dependen de usuarios)
        ]);
        
        // 4. Finalmente las tablas con múltiples dependencias
        $this->call([
            VehiculosReservasSeeder::class, // Relación entre vehículos y reservas
            PagoSeeder::class,              // Pagos (dependen de reservas)
            MetodoPagoSeeder::class,        // Métodos de pago
            ValoracionSeeder::class,        // Valoraciones (dependen de usuarios y vehículos)
            CarritoSeeder::class,           // Carritos de compra
            ReservaCompletaSeeder::class,   // Reservas completas
        ]);
        
        // 5. Subtipo de asalariados (depende de usuarios y parkings)
        $this->call([
            AsalariadoSeeder::class,         // Asalariados (trabajadores con salario)
        ]);
    }
}
