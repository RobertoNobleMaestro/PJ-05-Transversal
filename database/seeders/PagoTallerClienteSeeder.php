<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoTallerClienteSeeder extends Seeder
{
    /**
     * Simula el pago de un cliente por un mantenimiento
     */
    public function run(): void
    {
        $this->command->info('Simulando pago de cliente por mantenimiento...');

        // Buscar mantenimientos de vehículos de Valencia Ciudad (ID lugar = 1)
        $mantenimiento = DB::table('mantenimientos')
            ->join('vehiculos', 'mantenimientos.vehiculo_id', '=', 'vehiculos.id_vehiculos')
            ->where('vehiculos.id_lugar', 1) // Valencia Ciudad
            ->where('mantenimientos.estado', 'completado')
            ->first();

        if (!$mantenimiento) {
            // Si no hay mantenimientos, buscamos cualquier vehículo de Valencia para crear uno ficticio
            $vehiculo = DB::table('vehiculos')
                ->where('id_lugar', 1)
                ->first();
                
            if ($vehiculo) {
                // Crear un mantenimiento ficticio para este vehículo
                $mantenimientoId = DB::table('mantenimientos')->insertGetId([
                    'vehiculo_id' => $vehiculo->id_vehiculos,
                    'taller_id' => 1, // Asumiendo que hay un taller con ID 1
                    'fecha_programada' => Carbon::now()->subDays(5),
                    'hora_programada' => '10:00:00',
                    'estado' => 'completado',
                    'created_at' => Carbon::now()->subDays(10),
                    'updated_at' => Carbon::now()->subDays(5)
                ]);
            } else {
                $this->command->error('No se encontraron vehículos en Valencia Ciudad para asociar un mantenimiento.');
                return;
            }
        } else {
            $mantenimientoId = $mantenimiento->id;
        }

        // Generar datos del pago
        $precioPiezas = rand(250, 500);
        $precioManoObra = rand(150, 300);
        $total = $precioPiezas + $precioManoObra;
        
        // Detalle del pago (JSON con descripción detallada)
        $detalle = json_encode([
            'piezas' => [
                ['nombre' => 'Filtro de aceite premium', 'precio' => 35.50],
                ['nombre' => 'Aceite motor sintético 5L', 'precio' => 85.75],
                ['nombre' => 'Filtro de aire', 'precio' => 29.99],
                ['nombre' => 'Filtro de combustible', 'precio' => 49.95],
                ['nombre' => 'Bujías de encendido (juego)', 'precio' => $precioPiezas - 201.19]
            ],
            'servicios' => [
                ['nombre' => 'Cambio de aceite y filtros', 'precio' => 80.00],
                ['nombre' => 'Revisión general', 'precio' => 65.00],
                ['nombre' => 'Diagnóstico electrónico', 'precio' => 45.00],
                ['nombre' => 'Mano de obra adicional', 'precio' => $precioManoObra - 190.00]
            ]
        ]);
        
        // Insertar el pago en la tabla pago_taller
        DB::table('pago_taller')->insert([
            'mantenimiento_id' => $mantenimientoId,
            'averia_id' => null,
            'precio_piezas' => $precioPiezas,
            'precio_revisiones' => $precioManoObra,
            'total' => $total,
            'detalle' => $detalle,
            'created_at' => Carbon::now()->subDays(3),
            'updated_at' => Carbon::now()
        ]);
        
        $this->command->info("Pago de taller registrado con éxito por un total de {$total}€");
    }
}
