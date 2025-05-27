<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class HistoricalFinancialDataSeeder extends Seeder
{
    /**
     * Generar datos históricos financieros desde 2020 hasta 2025
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('es_ES');
        
        // Obtener IDs necesarios
        $vehiculosIds = DB::table('vehiculos')->pluck('id_vehiculos')->toArray();
        $parkingsIds = DB::table('parking')->pluck('id')->toArray();
        $asalariadosIds = DB::table('asalariados')->pluck('id')->toArray();
        $lugaresIds = DB::table('lugares')->pluck('id_lugar')->toArray();
        $usuariosIds = DB::table('users')->pluck('id_usuario')->toArray();
        
        // Si no hay datos, usar valores por defecto para el seeding
        if (empty($vehiculosIds)) $vehiculosIds = [1, 2, 3];
        if (empty($parkingsIds)) $parkingsIds = [1, 2, 3];
        if (empty($asalariadosIds)) $asalariadosIds = [1, 2, 3];
        if (empty($lugaresIds)) $lugaresIds = [1, 2, 3];
        if (empty($usuariosIds)) $usuariosIds = [1, 2, 3];
        
        // Generar datos históricos desde 2020 hasta la fecha actual (2025)
        $this->generateExpenseData($faker, $vehiculosIds, $parkingsIds, $asalariadosIds);
        $this->generateIncomeData($faker, $lugaresIds, $usuariosIds, $vehiculosIds);
        
        $this->command->info('Datos financieros históricos generados correctamente.');
    }
    
    /**
     * Generar datos históricos de gastos
     */
    private function generateExpenseData($faker, $vehiculosIds, $parkingsIds, $asalariadosIds)
    {
        $startDate = Carbon::createFromDate(2020, 1, 1);
        $endDate = Carbon::now(); // 2025
        
        $expenseTypes = [
            [
                'tipo' => 'salario',
                'conceptos' => ['Nómina mensual', 'Pago extra', 'Compensación'],
                'montoMinimo' => 800,
                'montoMaximo' => 2500,
                'relacionados' => $asalariadosIds,
                'campoRelacion' => 'id_asalariado'
            ],
            [
                'tipo' => 'mantenimiento',
                'conceptos' => ['Mantenimiento rutinario', 'Reparación', 'Revisión técnica', 'Cambio de aceite', 'Cambio de neumáticos'],
                'montoMinimo' => 150,
                'montoMaximo' => 800,
                'relacionados' => $vehiculosIds,
                'campoRelacion' => 'id_vehiculo'
            ],
            [
                'tipo' => 'parking',
                'conceptos' => ['Mantenimiento de instalaciones', 'Limpieza', 'Seguridad', 'Reparaciones', 'Servicios'],
                'montoMinimo' => 400,
                'montoMaximo' => 1000,
                'relacionados' => $parkingsIds,
                'campoRelacion' => 'id_parking'
            ],
            [
                'tipo' => 'otros',
                'conceptos' => ['Suministros oficina', 'Seguros', 'Publicidad', 'Servicios públicos', 'Impuestos'],
                'montoMinimo' => 100,
                'montoMaximo' => 2000,
                'relacionados' => null,
                'campoRelacion' => null
            ]
        ];
        
        $currentDate = clone $startDate;
        $batchInsert = [];
        $batchSize = 500;
        
        while ($currentDate->lte($endDate)) {
            // Para cada mes, generamos varios gastos
            $monthlyDate = clone $currentDate;
            
            // Cada tipo de gasto tiene su propia frecuencia y lógica
            foreach ($expenseTypes as $expenseType) {
                $entriesPerMonth = $expenseType['tipo'] === 'salario' ? count($expenseType['relacionados']) : rand(3, 10);
                
                for ($i = 0; $i < $entriesPerMonth; $i++) {
                    $date = clone $monthlyDate;
                    $date->day = rand(1, min(28, $date->daysInMonth));
                    
                    $gasto = [
                        'concepto' => $faker->randomElement($expenseType['conceptos']),
                        'descripcion' => $faker->sentence(),
                        'tipo' => $expenseType['tipo'],
                        'importe' => $faker->randomFloat(2, $expenseType['montoMinimo'], $expenseType['montoMaximo']),
                        'fecha' => $date->format('Y-m-d'),
                        'id_vehiculo' => null,
                        'id_parking' => null,
                        'id_asalariado' => null,
                        'id_mantenimiento' => null,
                        'created_at' => $date->format('Y-m-d H:i:s'),
                        'updated_at' => $date->format('Y-m-d H:i:s')
                    ];
                    
                    // Añadir relación si es necesario
                    if ($expenseType['campoRelacion'] !== null && !empty($expenseType['relacionados'])) {
                        $relatedId = $faker->randomElement($expenseType['relacionados']);
                        $gasto[$expenseType['campoRelacion']] = $relatedId;
                    }
                    
                    $batchInsert[] = $gasto;
                    
                    // Insertar en lotes para mejorar rendimiento
                    if (count($batchInsert) >= $batchSize) {
                        DB::table('gastos')->insert($batchInsert);
                        $batchInsert = [];
                    }
                }
            }
            
            $currentDate->addMonth();
        }
        
        // Insertar el resto de gastos
        if (!empty($batchInsert)) {
            DB::table('gastos')->insert($batchInsert);
        }
    }
    
    /**
     * Generar datos históricos de ingresos (en forma de reservas)
     */
    private function generateIncomeData($faker, $lugaresIds, $usuariosIds, $vehiculosIds)
    {
        $startDate = Carbon::createFromDate(2020, 1, 1);
        $endDate = Carbon::now(); // 2025
        
        $currentDate = clone $startDate;
        $batchReservas = [];
        $batchVehiculosReservas = [];
        $batchSize = 500;
        $reservaId = DB::table('reservas')->max('id_reservas') ?? 0;
        $vehiculoReservaId = DB::table('vehiculos_reservas')->max('id_vehiculos_reservas') ?? 0;
        
        $tiposReserva = [
            'alquiler' => [
                'minPrecio' => 40,
                'maxPrecio' => 200,
                'duracionMinima' => 1,
                'duracionMaxima' => 14
            ],
            'taxi' => [
                'minPrecio' => 20,
                'maxPrecio' => 150,
                'duracionMinima' => 1,
                'duracionMaxima' => 1
            ],
            'reparacion' => [
                'minPrecio' => 100,
                'maxPrecio' => 800,
                'duracionMinima' => 1,
                'duracionMaxima' => 7
            ]
        ];
        
        while ($currentDate->lte($endDate)) {
            // Número de reservas por mes varía según el año
            $year = $currentDate->year;
            // Más reservas a medida que pasan los años para simular crecimiento
            $reservasPerMonth = 15 + ($year - 2020) * 5;
            
            for ($i = 0; $i < $reservasPerMonth; $i++) {
                $reservaId++;
                $tipoReserva = $faker->randomElement(array_keys($tiposReserva));
                $configuracion = $tiposReserva[$tipoReserva];
                
                $fechaReserva = clone $currentDate;
                $fechaReserva->day = rand(1, min(28, $fechaReserva->daysInMonth));
                
                // Mayor probabilidad de reservas completadas en el pasado
                $estados = ['completada', 'cancelada', 'pendiente'];
                $estado = $fechaReserva->lt(Carbon::now()) ? 
                    $faker->randomElement(['completada', 'completada', 'completada', 'cancelada']) : 
                    $faker->randomElement($estados);
                
                $precioBase = $faker->randomFloat(2, $configuracion['minPrecio'], $configuracion['maxPrecio']);
                $duracion = rand($configuracion['duracionMinima'], $configuracion['duracionMaxima']);
                $precioTotal = $precioBase * $duracion;
                
                // Crear la reserva
                $reserva = [
                    'id_reservas' => $reservaId,
                    'fecha_reserva' => $fechaReserva->format('Y-m-d'),
                    'total_precio' => $precioTotal,
                    'estado' => $estado,
                    'id_lugar' => $faker->randomElement($lugaresIds),
                    'id_usuario' => $faker->randomElement($usuariosIds),
                    'referencia_pago' => $estado === 'completada' ? 'REF' . $faker->randomNumber(8) : null,
                    'created_at' => $fechaReserva->format('Y-m-d H:i:s'),
                    'updated_at' => $fechaReserva->format('Y-m-d H:i:s')
                ];
                
                $batchReservas[] = $reserva;
                
                // Crear la relación con vehículos si no es "reparación"
                if ($tipoReserva !== 'reparacion' && !empty($vehiculosIds)) {
                    $vehiculoReservaId++;
                    $fechaIni = clone $fechaReserva;
                    $fechaFin = clone $fechaIni;
                    $fechaFin->addDays($duracion);
                    
                    $vehiculoReserva = [
                        'id_vehiculos_reservas' => $vehiculoReservaId,
                        'fecha_ini' => $fechaIni->format('Y-m-d'),
                        'fecha_final' => $fechaFin->format('Y-m-d'),
                        'id_reservas' => $reservaId,
                        'id_vehiculos' => $faker->randomElement($vehiculosIds),
                        'created_at' => $fechaReserva->format('Y-m-d H:i:s'),
                        'updated_at' => $fechaReserva->format('Y-m-d H:i:s')
                    ];
                    
                    $batchVehiculosReservas[] = $vehiculoReserva;
                }
                
                // Insertar en lotes para mejorar rendimiento
                if (count($batchReservas) >= $batchSize) {
                    DB::table('reservas')->insert($batchReservas);
                    $batchReservas = [];
                    
                    if (!empty($batchVehiculosReservas)) {
                        DB::table('vehiculos_reservas')->insert($batchVehiculosReservas);
                        $batchVehiculosReservas = [];
                    }
                }
            }
            
            $currentDate->addMonth();
        }
        
        // Insertar las reservas restantes
        if (!empty($batchReservas)) {
            DB::table('reservas')->insert($batchReservas);
        }
        
        // Insertar las relaciones vehículos-reservas restantes
        if (!empty($batchVehiculosReservas)) {
            DB::table('vehiculos_reservas')->insert($batchVehiculosReservas);
        }
    }
}
