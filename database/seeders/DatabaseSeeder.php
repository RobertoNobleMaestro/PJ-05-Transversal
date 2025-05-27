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
            ChoferSeeder::class,         // Choferes (dependen de usuarios)
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
            PiezasSeeder::class,            // Piezas de vehículos
            AveriasSeeder::class,           // Averías de vehículos
        ]);
        
        // 5. Subtipo de asalariados (depende de usuarios y parkings)
        $this->call([
            AsalariadoSeeder::class,         // Asalariados (trabajadores con salario)
            AsalariadosSeeder::class,        // Nuevos asalariados de prueba
        ]);
        
        // 6. Actualización de precios para parkings (más realistas)
        $this->call([
            UpdateParkingPricesSeeder::class, // Actualiza precios por m² con variaciones realistas
        ]);
        
        // 6. Datos financieros (activos y pasivos)
        $this->call([
            ActivoSeeder::class,             // Activos financieros
            PasivoSeeder::class,            // Pasivos financieros
            GastosMantenimientoSeeder::class, // Gastos de mantenimiento para vehículos y parkings
            PagoTallerSeeder::class,        // Ingresos por reparaciones mecánicas
        ]);
    }
}
