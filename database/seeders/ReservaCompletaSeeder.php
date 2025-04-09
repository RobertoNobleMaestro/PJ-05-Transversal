<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservaCompletaSeeder extends Seeder
{
    public function run()
    {
        // LUGARES
        DB::table('lugares')->insertOrIgnore([
            ['id_lugar' => 1, 'nombre' => 'Madrid Centro', 'direccion' => 'Calle Gran Vía 1', 'latitud' => '40.41680000', 'longitud' => '-3.70380000'],
            ['id_lugar' => 2, 'nombre' => 'Barcelona Puerto', 'direccion' => 'Passeig de Colom 22', 'latitud' => '41.37510000', 'longitud' => '2.17690000'],
        ]);

        // TIPOS
        DB::table('tipo')->insertOrIgnore([
            ['id_tipo' => 1, 'nombre' => 'Coche'],
            ['id_tipo' => 2, 'nombre' => 'Moto'],
        ]);

        // VEHÍCULOS
        DB::table('vehiculos')->insertOrIgnore([
            ['id_vehiculos' => 1, 'marca' => 'Toyota', 'modelo' => 'Corolla', 'kilometraje' => 15000, 'seguro_incluido' => 1, 'año' => '2023', 'id_lugar' => 1, 'id_tipo' => 1],
            ['id_vehiculos' => 2, 'marca' => 'Honda', 'modelo' => 'CBR 600', 'kilometraje' => 8000, 'seguro_incluido' => 1, 'año' => '2022', 'id_lugar' => 2, 'id_tipo' => 2],
        ]);

        // IMÁGENES
        DB::table('imagen_vehiculo')->insertOrIgnore([
            ['id_imagen_vehiculo' => 1, 'nombre_archivo' => 'corolla_1.jpg', 'id_vehiculo' => 1],
            ['id_imagen_vehiculo' => 2, 'nombre_archivo' => 'corolla_2.jpg', 'id_vehiculo' => 1],
            ['id_imagen_vehiculo' => 3, 'nombre_archivo' => 'cbr600_1.jpg', 'id_vehiculo' => 2],
        ]);

        // CARACTERÍSTICAS
        DB::table('caracteristicas')->insertOrIgnore([
            [
                'id_caracteristicas' => 1,
                'id_vehiculos' => 1,
                'transmision' => 'Automática',
                'num_puertas' => 5,
                'etiqueta_medioambiental' => 'ECO',
                'aire_acondicionado' => 1,
                'capacidad_maletero' => 450,
                'techo' => 0
            ],
            [
                'id_caracteristicas' => 2,
                'id_vehiculos' => 2,
                'transmision' => 'Manual',
                'num_puertas' => 0,
                'etiqueta_medioambiental' => 'C',
                'aire_acondicionado' => 0,
                'capacidad_maletero' => 0,
                'techo' => 0
            ]
        ]);

        // RESERVA
        DB::table('reservas')->insertOrIgnore([
            [
                'id_reservas' => 3,
                'id_usuario' => 1,
                'estado' => 'pendiente',
                'fecha_reserva' => now()->toDateString()
            ]
        ]);

        // VEHÍCULOS_RESERVAS
        DB::table('vehiculos_reservas')->insertOrIgnore([
            [
                'id_vehiculos_reservas' => 1,
                'id_reservas' => 3,
                'id_vehiculos' => 1,
                'fecha_ini' => now()->addDays(1)->toDateString(),
                'fecha_final' => now()->addDays(3)->toDateString(),
                'precio_unitario' => 75.00
            ],
            [
                'id_vehiculos_reservas' => 2,
                'id_reservas' => 3,
                'id_vehiculos' => 2,
                'fecha_ini' => now()->addDays(2)->toDateString(),
                'fecha_final' => now()->addDays(4)->toDateString(),
                'precio_unitario' => 50.00
            ],
        ]);

        // PAGO
        DB::table('pago')->insertOrIgnore([
            [
                'id_pago' => 1,
                'estado_pago' => 'no pagado',
                'fecha_pago' => now()->toDateString(),
                'referencia_externa' => 'ABC123XYZ',
                'monto_pagado' => 125.00,
                'total_precio' => 125.00,
                'moneda' => 'EUR',
                'id_usuario' => 1,
                'id_reservas' => 3
            ]
        ]);

        // MÉTODO DE PAGO
        DB::table('metodos_de_pago')->insertOrIgnore([
            [
                'id_metodoPago' => 1,
                'nombre' => 'Tarjeta de crédito',
                'id_pago' => 1
            ]
        ]);
    }
}
