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
        
        // Verificar si las tablas necesarias existen
        if (!Schema::hasTable('solicitudes')) {
            $this->command->error('La tabla de solicitudes no existe. No se pueden crear pagos de choferes.');
            return;
        }
        
        if (!Schema::hasTable('pagos_choferes')) {
            $this->command->error('La tabla de pagos_choferes no existe. No se pueden crear pagos de choferes.');
            return;
        }
        
        // Obtener choferes disponibles
        try {
            // Obtener directamente de la tabla choferes usando el ID primario correcto
            if (Schema::hasTable('choferes')) {
                $choferes = DB::table('choferes')->select('id')->get();
                $this->command->info('Se encontraron ' . $choferes->count() . ' choferes.');
            } else {
                $this->command->error('La tabla choferes no existe. No se pueden crear solicitudes.');
                return;
            }
            
            if ($choferes->isEmpty()) {
                // Si no hay choferes en el sistema, creamos registros simulados
                $choferes = collect();
                for ($i = 1; $i <= 10; $i++) {
                    $choferes->push((object)['id' => $i]);
                }
                $this->command->warn('No se encontraron choferes. Se usarán ' . $choferes->count() . ' choferes simulados.');
            }
        } catch (\Exception $e) {
            $this->command->error('Error al consultar choferes: ' . $e->getMessage());
            return;
        }
        
        // Obtener clientes disponibles
        try {
            $clientes = DB::table('users')
                ->where('id_roles', '!=', 6)  // No seleccionar usuarios que sean choferes
                ->select('id_usuario as id')
                ->get();
                
            if ($clientes->isEmpty()) {
                // Si no hay clientes, creamos algunos simulados
                $clientes = collect();
                for ($i = 101; $i <= 120; $i++) {  // IDs distintos de los choferes
                    $clientes->push((object)['id' => $i]);
                }
                $this->command->warn('No se encontraron clientes. Se usarán ' . $clientes->count() . ' clientes simulados.');
            } else {
                $this->command->info('Se encontraron ' . $clientes->count() . ' clientes.');
            }
        } catch (\Exception $e) {
            $this->command->error('Error al consultar clientes: ' . $e->getMessage());
            return;
        }
        
        // Limpiamos la tabla de pagos de choferes
        try {
            DB::table('pagos_choferes')->truncate();
        } catch (\Exception $e) {
            // Si no se puede truncar, intentamos eliminar los registros
            try {
                DB::table('pagos_choferes')->delete();
            } catch (\Exception $ex) {
                // Continuamos si falla
            }
        }
        
        // Generar fechas para los últimos 12 meses
        $fechaInicio = Carbon::now()->subMonths(12);
        $fechaFin = Carbon::now();
        $diasRango = $fechaFin->diffInDays($fechaInicio);
        
        // Determinar cantidad de pagos/solicitudes a generar
        $cantidadPagos = rand(50, 100);  // Reducido para evitar demasiados errores
        $this->command->info("Se generarán {$cantidadPagos} solicitudes y pagos de taxi.");
        
        // Contadores
        $solicitudesCreadas = 0;
        $pagosCreados = 0;
        
        // Colección para almacenar las solicitudes creadas
        $solicitudesNuevas = collect();
        
        // Generar solicitudes
        for ($i = 0; $i < $cantidadPagos; $i++) {
            try {
                // Seleccionar un chofer aleatorio
                $chofer = $choferes->random();
                
                // Seleccionar un cliente aleatorio
                $cliente = $clientes->random();
                
                // Generar coordenadas de origen (España)
                $latitudOrigen = $faker->latitude(36.0, 43.5);  // Aproximadamente España
                $longitudOrigen = $faker->longitude(-9.0, 3.0);
                
                // Generar coordenadas de destino (cerca del origen, pero distinto)
                $latitudDestino = $latitudOrigen + $faker->randomFloat(6, -0.05, 0.05);
                $longitudDestino = $longitudOrigen + $faker->randomFloat(6, -0.05, 0.05);
                
                // Generar precio
                $precio = $faker->randomFloat(2, 15, 50);
                
                // Generar fecha aleatoria
                $fechaSolicitud = $fechaInicio->copy()->addDays(rand(0, $diasRango));
                
                // Insertar solicitud con estado 'completada' (no pendiente)
                $idSolicitud = DB::table('solicitudes')->insertGetId([
                    'id_chofer' => $chofer->id,
                    'id_cliente' => $cliente->id,
                    'latitud_origen' => $latitudOrigen,
                    'longitud_origen' => $longitudOrigen,
                    'latitud_destino' => $latitudDestino,
                    'longitud_destino' => $longitudDestino,
                    'precio' => $precio,
                    'estado' => 'completada',  // Importante: no pendiente
                    'created_at' => $fechaSolicitud,
                    'updated_at' => $fechaSolicitud
                ]);
                
                // Añadir a nuestra colección
                $solicitudesNuevas->push((object)['id' => $idSolicitud]);
                $solicitudesCreadas++;
                
                // Generar el pago asociado (fecha de pago poco después de la solicitud)
                $fechaPago = $fechaSolicitud->copy()->addHours(rand(1, 24));
                
                // Calcular importes
                $importeTotal = $precio;  // Mismo precio que la solicitud
                $porcentajeEmpresa = $faker->randomFloat(2, 0.2, 0.3);  // 20-30% para la empresa
                $importeEmpresa = round($importeTotal * $porcentajeEmpresa, 2);
                $importeChofer = round($importeTotal - $importeEmpresa, 2);
                
                // Insertar pago
                DB::table('pagos_choferes')->insert([
                    'chofer_id' => $chofer->id,
                    'solicitud_id' => $idSolicitud,
                    'importe_total' => $importeTotal,
                    'importe_empresa' => $importeEmpresa,
                    'importe_chofer' => $importeChofer,
                    'estado_pago' => 'pagado',
                    'fecha_pago' => $fechaPago,
                    'created_at' => $fechaPago,
                    'updated_at' => $fechaPago
                ]);
                
                $pagosCreados++;
                
                // Mostrar progreso cada 10 registros
                if ($i % 10 == 0 && $i > 0) {
                    $this->command->info("Progreso: {$i}/{$cantidadPagos} solicitudes y pagos creados.");
                }
                
            } catch (\Exception $e) {
                $this->command->warn("Error al crear solicitud/pago #{$i}: " . $e->getMessage());
            }
        }
        
        $this->command->info("Proceso completado: {$solicitudesCreadas} solicitudes y {$pagosCreados} pagos creados correctamente.");
    }
}
