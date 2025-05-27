<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PagosChoferesSeeder extends Seeder
{
    /**
     * Run the database seeds para crear registros de pagos a choferes por servicios de taxi.
     */
    public function run(): void
    {
        $this->command->info('Creando pagos de servicios de taxi...');
        $faker = Faker::create('es_ES');
        
        // Obtener todos los usuarios con rol de chofer, si existe la tabla users
        try {
            $choferes = DB::table('users')
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->where('roles.name', 'chofer')
                ->select('users.id')
                ->get();
                
            if ($choferes->isEmpty()) {
                // Si no hay choferes en el sistema, creamos registros simulados
                $choferes = collect();
                for ($i = 1; $i <= 10; $i++) {
                    $choferes->push((object)['id' => $i]);
                }
            }
        } catch (\Exception $e) {
            // Si hay un error o la tabla no existe, creamos datos simulados
            $choferes = collect();
            for ($i = 1; $i <= 10; $i++) {
                $choferes->push((object)['id' => $i]);
            }
        }
        
        // Obtener solicitudes (reservas) existentes en lugar de solicitud_taxi
        try {
            // Obtener IDs reales de la tabla solicitudes o de reservas
            if (Schema::hasTable('solicitudes')) {
                $solicitudes = DB::table('solicitudes')->select('id')->get();
                $this->command->info('Se encontraron ' . $solicitudes->count() . ' solicitudes.');
            } else if (Schema::hasTable('reservas')) {
                // Si no existe 'solicitudes', usar reservas como alternativa
                $solicitudes = DB::table('reservas')->select('id_reservas as id')->get();
                $this->command->info('No se encontró tabla solicitudes. Usando ' . $solicitudes->count() . ' reservas.');
            } else {
                // Si no hay ni solicitudes ni reservas, no podemos crear pagos de choferes
                $this->command->error('No se encontraron tablas de solicitudes o reservas. No se pueden crear pagos de choferes.');
                return; // Salir del seeder
            }
            
            if ($solicitudes->isEmpty()) {
                $this->command->warn('No hay solicitudes o reservas disponibles. Se crearán pagos de choferes con IDs simulados.');
                // Crear una colección simulada de solicitudes
                $solicitudes = collect();
                for ($i = 1; $i <= 50; $i++) {
                    $solicitudes->push((object)['id' => $i]);
                }
            }
        } catch (\Exception $e) {
            $this->command->error('Error al consultar solicitudes: ' . $e->getMessage());
            return; // Salir del seeder
        }
        
        // Generar pagos para los últimos 12 meses
        $fechaInicio = Carbon::now()->subMonths(12);
        $fechaFin = Carbon::now();
        
        // Calcular número de días en el rango para distribuir pagos
        $diasRango = $fechaFin->diffInDays($fechaInicio);
        
        // Determinar cantidad de pagos a generar (entre 100-150 para 12 meses)
        $cantidadPagos = rand(100, 150);
        
        // Truncar la tabla antes de insertar nuevos registros
        try {
            DB::table('pagos_choferes')->truncate();
        } catch (\Exception $e) {
            $this->command->error('No se pudo truncar la tabla pagos_choferes: ' . $e->getMessage());
            // Continuamos con el proceso aunque no se pueda truncar
        }
        
        $this->command->info("Generando {$cantidadPagos} pagos de servicios de taxi.");
        
        // Contador de éxitos
        $registrosCreados = 0;
        
        // Generar registros de pagos
        for ($i = 0; $i < $cantidadPagos; $i++) {
            try {
                // Seleccionar un chofer aleatorio
                $chofer = $choferes->random();
                
                // Seleccionar una solicitud aleatoria
                $solicitud = $solicitudes->random();
                
                // Generar fecha aleatoria dentro del rango
                $fechaPago = $fechaInicio->copy()->addDays(rand(0, $diasRango));
                
                // Generar importes realistas
                // La tarifa media de un servicio de taxi ronda los 15-50€
                $importeTotal = $faker->randomFloat(2, 15, 50);
                
                // La empresa se queda con un porcentaje (típicamente 20-30%)
                $porcentajeEmpresa = $faker->randomFloat(2, 0.2, 0.3);
                $importeEmpresa = round($importeTotal * $porcentajeEmpresa, 2);
                $importeChofer = round($importeTotal - $importeEmpresa, 2);
                
                // Insertar el registro
                DB::table('pagos_choferes')->insert([
                    'chofer_id' => $chofer->id,
                    'solicitud_id' => $solicitud->id,
                    'importe_total' => $importeTotal,
                    'importe_empresa' => $importeEmpresa,
                    'importe_chofer' => $importeChofer,
                    'estado_pago' => 'pagado',
                    'fecha_pago' => $fechaPago,
                    'created_at' => $fechaPago,
                    'updated_at' => $fechaPago
                ]);
                
                $registrosCreados++;
            } catch (\Exception $e) {
                // Si falla un registro, continuamos con el siguiente
                $this->command->warn("Error al crear registro #{$i}: " . $e->getMessage());
            }
        }
        
        $this->command->info('Pagos de servicios de taxi creados correctamente: ' . $registrosCreados . ' de ' . $cantidadPagos . ' registros.');
    }
}
