<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Faker\Factory as Faker;

class FinancialData2025Seeder extends Seeder
{
    /**
     * Generar datos financieros variados para todos los meses de 2025
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
        if (empty($vehiculosIds)) $vehiculosIds = [1, 2, 3, 4, 5];
        if (empty($parkingsIds)) $parkingsIds = [1, 2, 3];
        if (empty($asalariadosIds)) $asalariadosIds = [1, 2, 3, 4, 5];
        if (empty($lugaresIds)) $lugaresIds = [1, 2, 3];
        if (empty($usuariosIds)) $usuariosIds = [1, 2, 3, 4, 5];
        
        // Definir patrones estacionales para 2025
        $patronesEstacionales = [
            1 => 0.6,   // Enero (Bajo - post navidad)
            2 => 0.65,  // Febrero (Bajo)
            3 => 0.8,   // Marzo (Medio - semana santa)
            4 => 0.9,   // Abril (Medio-alto - puentes)
            5 => 1.1,   // Mayo (Alto - primavera)
            6 => 1.3,   // Junio (Muy alto - inicio verano)
            7 => 1.6,   // Julio (Pico - vacaciones)
            8 => 1.7,   // Agosto (Pico máximo - vacaciones)
            9 => 1.2,   // Septiembre (Alto - final verano)
            10 => 0.9,  // Octubre (Medio)
            11 => 0.7,  // Noviembre (Bajo)
            12 => 1.0   // Diciembre (Alto - navidad)
        ];
        
        // Definir eventos especiales que afectan los ingresos/gastos en 2025
        $eventosEspeciales = [
            '2025-01-15' => ['nombre' => 'Campaña promocional enero', 'ingresos' => 1.3, 'gastos' => 1.2],
            '2025-02-14' => ['nombre' => 'San Valentín', 'ingresos' => 1.4, 'gastos' => 1.1],
            '2025-03-28' => ['nombre' => 'Semana Santa', 'ingresos' => 1.5, 'gastos' => 1.2],
            '2025-05-01' => ['nombre' => 'Puente Mayo', 'ingresos' => 1.4, 'gastos' => 1.1],
            '2025-06-24' => ['nombre' => 'San Juan', 'ingresos' => 1.3, 'gastos' => 1.2],
            '2025-07-15' => ['nombre' => 'Promoción verano', 'ingresos' => 1.6, 'gastos' => 1.3],
            '2025-08-15' => ['nombre' => 'Pico de temporada', 'ingresos' => 1.8, 'gastos' => 1.4],
            '2025-09-11' => ['nombre' => 'Diada Catalunya', 'ingresos' => 1.3, 'gastos' => 1.1],
            '2025-10-12' => ['nombre' => 'Día Hispanidad', 'ingresos' => 1.3, 'gastos' => 1.1],
            '2025-11-01' => ['nombre' => 'Puente Todos Santos', 'ingresos' => 1.2, 'gastos' => 1.1],
            '2025-12-06' => ['nombre' => 'Puente Constitución', 'ingresos' => 1.3, 'gastos' => 1.1],
            '2025-12-22' => ['nombre' => 'Navidad', 'ingresos' => 1.5, 'gastos' => 1.3],
            '2025-12-31' => ['nombre' => 'Fin de Año', 'ingresos' => 1.4, 'gastos' => 1.2]
        ];
        
        // Generar gastos para cada mes de 2025 con variabilidad según estacionalidad
        $this->generateExpenses2025($faker, $vehiculosIds, $parkingsIds, $asalariadosIds, $patronesEstacionales, $eventosEspeciales);
        
        // Generar ingresos para cada mes de 2025 con variabilidad según estacionalidad
        $this->generateIncome2025($faker, $lugaresIds, $usuariosIds, $vehiculosIds, $patronesEstacionales, $eventosEspeciales);
        
        $this->command->info('Datos financieros 2025 con variabilidad estacional generados correctamente.');
    }
    
    /**
     * Generar gastos variados para todos los meses de 2025
     */
    private function generateExpenses2025($faker, $vehiculosIds, $parkingsIds, $asalariadosIds, $patronesEstacionales, $eventosEspeciales)
    {
        $startDate = Carbon::createFromDate(2025, 1, 1);
        $endDate = Carbon::createFromDate(2025, 12, 31);
        
        $expenseTypes = [
            [
                'tipo' => 'salario',
                'conceptos' => ['Nómina mensual', 'Pago extra', 'Compensación', 'Bonus trimestral', 'Incentivo'],
                'montoBase' => [1800, 2200],  // Rango base que será multiplicado por factor estacional
                'relacionados' => $asalariadosIds,
                'campoRelacion' => 'id_asalariado'
            ],
            [
                'tipo' => 'mantenimiento',
                'conceptos' => ['Mantenimiento rutinario', 'Reparación', 'Revisión técnica', 'Cambio de aceite', 'Cambio de neumáticos', 'Reparación motor', 'Chapa y pintura'],
                'montoBase' => [150, 950],
                'relacionados' => $vehiculosIds,
                'campoRelacion' => 'id_vehiculo'
            ],
            [
                'tipo' => 'parking',
                'conceptos' => ['Mantenimiento de instalaciones', 'Limpieza', 'Seguridad', 'Reparaciones', 'Servicios', 'Electricidad', 'Agua', 'Mejoras'],
                'montoBase' => [400, 1200],
                'relacionados' => $parkingsIds,
                'campoRelacion' => 'id_parking'
            ],
            [
                'tipo' => 'otros',
                'conceptos' => ['Suministros oficina', 'Seguros', 'Publicidad', 'Servicios públicos', 'Impuestos', 'Marketing', 'Software', 'Formación', 'Consultoría'],
                'montoBase' => [100, 3000],
                'relacionados' => null,
                'campoRelacion' => null
            ]
        ];
        
        $currentDate = clone $startDate;
        $batchInsert = [];
        $batchSize = 500;
        
        while ($currentDate->lte($endDate)) {
            $month = $currentDate->month;
            $factorEstacional = $patronesEstacionales[$month];
            
            // Para cada día del mes, generamos gastos con probabilidad variable
            $dia = 1;
            $fechaMes = clone $currentDate;
            
            while ($dia <= $fechaMes->daysInMonth) {
                $fechaActual = Carbon::createFromDate(2025, $month, $dia);
                $factorDiario = 1.0;
                
                // Verificar si hay evento especial en esta fecha
                $fechaKey = $fechaActual->format('Y-m-d');
                if (isset($eventosEspeciales[$fechaKey])) {
                    $factorDiario *= $eventosEspeciales[$fechaKey]['gastos'];
                }
                
                // Cada tipo de gasto tiene diferente probabilidad según el día del mes
                foreach ($expenseTypes as $expenseType) {
                    // Mayor probabilidad de gastos a principio y final de mes
                    $probabilidad = ($dia <= 5 || $dia >= 25) ? 0.8 : 0.4;
                    
                    // Salarios principalmente a principio de mes
                    if ($expenseType['tipo'] === 'salario' && $dia <= 5) {
                        $probabilidad = 0.9;
                    } elseif ($expenseType['tipo'] === 'salario') {
                        $probabilidad = 0.1; // Baja fuera de inicio de mes
                    }
                    
                    // Determinamos si hay gasto de este tipo hoy
                    if ($faker->boolean($probabilidad * 100)) {
                        // Calculamos un monto con variabilidad basada en el mes y factores
                        $montoBase = $faker->randomFloat(2, $expenseType['montoBase'][0], $expenseType['montoBase'][1]);
                        $montoFinal = $montoBase * $factorEstacional * $factorDiario;
                        
                        $gasto = [
                            'concepto' => $faker->randomElement($expenseType['conceptos']),
                            'descripcion' => $faker->sentence(),
                            'tipo' => $expenseType['tipo'],
                            'importe' => round($montoFinal, 2),
                            'fecha' => $fechaActual->format('Y-m-d'),
                            'id_vehiculo' => null,
                            'id_parking' => null,
                            'id_asalariado' => null,
                            'id_mantenimiento' => null,
                            'created_at' => $fechaActual->format('Y-m-d H:i:s'),
                            'updated_at' => $fechaActual->format('Y-m-d H:i:s')
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
                
                $dia++;
            }
            
            $currentDate->addMonth();
        }
        
        // Insertar el resto de gastos
        if (!empty($batchInsert)) {
            DB::table('gastos')->insert($batchInsert);
        }
    }
    
    /**
     * Generar ingresos variados para todos los meses de 2025
     */
    private function generateIncome2025($faker, $lugaresIds, $usuariosIds, $vehiculosIds, $patronesEstacionales, $eventosEspeciales)
    {
        $startDate = Carbon::createFromDate(2025, 1, 1);
        $endDate = Carbon::createFromDate(2025, 12, 31);
        
        $tiposIngresos = [
            'alquiler' => [
                'minPrecio' => 40,
                'maxPrecio' => 250,
                'duracionMinima' => 1,
                'duracionMaxima' => 14,
                'concepto' => 'Alquiler de vehículo',
                'pesoEstacional' => 1.2  // Mayor afectación por estacionalidad
            ],
            'taxi' => [
                'minPrecio' => 20,
                'maxPrecio' => 180,
                'duracionMinima' => 1,
                'duracionMaxima' => 1,
                'concepto' => 'Servicio de taxi',
                'pesoEstacional' => 1.0  // Afectación media por estacionalidad
            ],
            'reparacion' => [
                'minPrecio' => 100,
                'maxPrecio' => 1200,
                'duracionMinima' => 1,
                'duracionMaxima' => 7,
                'concepto' => 'Servicio de reparación',
                'pesoEstacional' => 0.8  // Menor afectación por estacionalidad
            ]
        ];
        
        $batchReservas = [];
        $batchVehiculosReservas = [];
        $batchSize = 500;
        $reservaId = DB::table('reservas')->max('id_reservas') ?? 0;
        $vehiculoReservaId = DB::table('vehiculos_reservas')->max('id_vehiculos_reservas') ?? 0;
        
        $currentDate = clone $startDate;
        
        while ($currentDate->lte($endDate)) {
            $month = $currentDate->month;
            $factorEstacional = $patronesEstacionales[$month];
            
            // Para cada día del mes, generamos ingresos con probabilidad variable
            $dia = 1;
            $fechaMes = clone $currentDate;
            
            while ($dia <= $fechaMes->daysInMonth) {
                $fechaActual = Carbon::createFromDate(2025, $month, $dia);
                $factorDiario = 1.0;
                $diaSemana = $fechaActual->dayOfWeek; // 0 (domingo) hasta 6 (sábado)
                
                // Mayor actividad en fines de semana
                if ($diaSemana == 5 || $diaSemana == 6) { // viernes y sábado
                    $factorDiario *= 1.4;
                } elseif ($diaSemana == 0) { // domingo
                    $factorDiario *= 1.2;
                }
                
                // Verificar si hay evento especial en esta fecha
                $fechaKey = $fechaActual->format('Y-m-d');
                if (isset($eventosEspeciales[$fechaKey])) {
                    $factorDiario *= $eventosEspeciales[$fechaKey]['ingresos'];
                }
                
                // Calculamos número de reservas para este día según factores
                $baseReservasDiarias = 3; // Base mínima de reservas diarias
                $reservasDiarias = round($baseReservasDiarias * $factorEstacional * $factorDiario);
                
                // Generamos las reservas del día
                for ($i = 0; $i < $reservasDiarias; $i++) {
                    $reservaId++;
                    
                    // Determinar tipo de reserva con probabilidades variables según mes
                    $probabilidadAlquiler = 50 * $factorEstacional;
                    $probabilidadTaxi = 30;
                    $probabilidadReparacion = 20;
                    
                    // En verano más alquileres, en invierno más reparaciones
                    if ($month >= 6 && $month <= 9) { // Verano
                        $probabilidadAlquiler += 20;
                        $probabilidadReparacion -= 10;
                        $probabilidadTaxi -= 10;
                    } elseif ($month <= 2 || $month >= 11) { // Invierno
                        $probabilidadAlquiler -= 20;
                        $probabilidadReparacion += 15;
                        $probabilidadTaxi += 5;
                    }
                    
                    // Asegurar que las probabilidades sumen 100
                    $total = $probabilidadAlquiler + $probabilidadTaxi + $probabilidadReparacion;
                    $probabilidadAlquiler = ($probabilidadAlquiler / $total) * 100;
                    $probabilidadTaxi = ($probabilidadTaxi / $total) * 100;
                    
                    // Determinar el tipo de reserva
                    $random = $faker->randomFloat(2, 0, 100);
                    if ($random <= $probabilidadAlquiler) {
                        $tipoReserva = 'alquiler';
                    } elseif ($random <= $probabilidadAlquiler + $probabilidadTaxi) {
                        $tipoReserva = 'taxi';
                    } else {
                        $tipoReserva = 'reparacion';
                    }
                    
                    $configuracion = $tiposIngresos[$tipoReserva];
                    
                    // Calcular precio con variabilidad según tipo, mes y día
                    $precioBaseMin = $configuracion['minPrecio'] * (1 + ($factorEstacional - 1) * $configuracion['pesoEstacional']);
                    $precioBaseMax = $configuracion['maxPrecio'] * (1 + ($factorEstacional - 1) * $configuracion['pesoEstacional']);
                    
                    $precioBase = $faker->randomFloat(2, $precioBaseMin, $precioBaseMax);
                    $duracion = $faker->numberBetween($configuracion['duracionMinima'], $configuracion['duracionMaxima']);
                    $precioTotal = round($precioBase * $duracion, 2);
                    
                    // Estado: completada si es fecha pasada, pendiente o completada si es futura
                    $estado = $fechaActual->lt(Carbon::now()) ? 
                        $faker->randomElement(['completada', 'completada', 'completada', 'cancelada']) : 
                        $faker->randomElement(['completada', 'pendiente', 'pendiente']);
                    
                    // Crear la reserva
                    $reserva = [
                        'id_reservas' => $reservaId,
                        'fecha_reserva' => $fechaActual->format('Y-m-d'),
                        'total_precio' => $precioTotal,
                        'estado' => $estado,
                        'id_lugar' => $faker->randomElement($lugaresIds),
                        'id_usuario' => $faker->randomElement($usuariosIds),
                        'referencia_pago' => $estado === 'completada' ? 'REF' . $faker->randomNumber(8) : null,
                        'created_at' => $fechaActual->format('Y-m-d H:i:s'),
                        'updated_at' => $fechaActual->format('Y-m-d H:i:s')
                    ];
                    
                    $batchReservas[] = $reserva;
                    
                    // Crear la relación con vehículos si no es "reparacion"
                    if ($tipoReserva !== 'reparacion' && !empty($vehiculosIds)) {
                        $vehiculoReservaId++;
                        $fechaIni = clone $fechaActual;
                        $fechaFin = clone $fechaIni;
                        $fechaFin->addDays($duracion);
                        
                        $vehiculoReserva = [
                            'id_vehiculos_reservas' => $vehiculoReservaId,
                            'fecha_ini' => $fechaIni->format('Y-m-d'),
                            'fecha_final' => $fechaFin->format('Y-m-d'),
                            'id_reservas' => $reservaId,
                            'id_vehiculos' => $faker->randomElement($vehiculosIds),
                            'created_at' => $fechaActual->format('Y-m-d H:i:s'),
                            'updated_at' => $fechaActual->format('Y-m-d H:i:s')
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
                
                $dia++;
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
