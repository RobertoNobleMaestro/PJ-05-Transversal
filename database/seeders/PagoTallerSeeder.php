<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Faker\Factory as Faker;

class PagoTallerSeeder extends Seeder
{
    /**
     * Run the database seeds to create payment records for mechanic repairs.
     */
    public function run(): void
    {
        $this->command->info('Creando pagos de reparaciones mecánicas...');
        
        // Truncar la tabla antes de insertar nuevos registros
        try {
            DB::table('pago_taller')->truncate();
        } catch (\Exception $e) {
            $this->command->error('No se pudo truncar la tabla pago_taller: ' . $e->getMessage());
        }
        
        // Obtener mantenimientos reales de la base de datos (no ids simulados)
        try {
            // Verificar si existe la tabla mantenimientos
            if (Schema::hasTable('mantenimientos')) {
                $mantenimientos = DB::table('mantenimientos')->get();
                $this->command->info('Se encontraron ' . $mantenimientos->count() . ' registros de mantenimientos.');
            } else {
                $mantenimientos = collect([]);
                $this->command->warn('La tabla mantenimientos no existe. No se generarán registros vinculados a mantenimientos.');
            }
        } catch (\Exception $e) {
            $mantenimientos = collect([]);
            $this->command->error('Error al consultar mantenimientos: ' . $e->getMessage());
        }
        
        // Obtener averías reales de la base de datos (no ids simulados)
        try {
            // Verificar si existe la tabla averías
            if (Schema::hasTable('averias')) {
                $averias = DB::table('averias')->get();
                $this->command->info('Se encontraron ' . $averias->count() . ' registros de averías.');
            } else {
                $averias = collect([]);
                $this->command->warn('La tabla averias no existe. No se generarán registros vinculados a averías.');
            }
        } catch (\Exception $e) {
            $averias = collect([]);
            $this->command->error('Error al consultar averías: ' . $e->getMessage());
        }
        
        // Si no hay mantenimientos ni averías, crear pagos de taller independientes
        $crearRegistrosIndependientes = $mantenimientos->isEmpty() && $averias->isEmpty();
        
        if ($crearRegistrosIndependientes) {
            $this->command->warn('No se encontraron mantenimientos ni averías. Se crearán pagos de taller independientes.');
        }
        
        $faker = Faker::create('es_ES');
        
        // Si tenemos mantenimientos, crear pagos basados en ellos
        if ($mantenimientos->isNotEmpty()) {
            foreach ($mantenimientos as $index => $mantenimiento) {
                // Solo creamos pagos para el 80% de los mantenimientos para simular realismo
                if (rand(1, 100) <= 80) {
                    // Generar importes realistas
                    $precioPiezas = rand(50, 800); // Entre 50€ y 800€ en piezas
                    $precioRevisiones = rand(75, 500); // Entre 75€ y 500€ en mano de obra
                    $total = $precioPiezas + $precioRevisiones;
                    
                    // Detalle del pago (JSON)
                    $detalle = json_encode([
                        'piezas' => [
                            ['nombre' => 'Filtro de aceite', 'precio' => rand(15, 30)],
                            ['nombre' => 'Aceite motor', 'precio' => rand(40, 80)],
                            ['nombre' => 'Otros componentes', 'precio' => $precioPiezas - rand(55, 110)]
                        ],
                        'servicios' => [
                            ['nombre' => 'Mano de obra', 'precio' => $precioRevisiones * 0.8],
                            ['nombre' => 'Diagnóstico', 'precio' => $precioRevisiones * 0.2]
                        ]
                    ]);
                    
                    // Crear el registro de pago
                    DB::table('pago_taller')->insert([
                        'mantenimiento_id' => $mantenimiento->id,
                        'averia_id' => null,
                        'precio_piezas' => $precioPiezas,
                        'precio_revisiones' => $precioRevisiones,
                        'total' => $total,
                        'detalle' => $detalle,
                        'created_at' => Carbon::now()->subDays(rand(1, 30)), // Para que caigan en el mes actual
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
        
        // Si tenemos averías, crear pagos basados en ellas
        if ($averias->isNotEmpty()) {
            foreach ($averias as $index => $averia) {
                // Solo creamos pagos para el 70% de las averías
                if (rand(1, 100) <= 70) {
                    // Las averías suelen ser más caras que los mantenimientos
                    $precioPiezas = rand(200, 1500); // Entre 200€ y 1500€ en piezas
                    $precioRevisiones = rand(150, 800); // Entre 150€ y 800€ en mano de obra
                    $total = $precioPiezas + $precioRevisiones;
                    
                    // Detalle del pago (JSON)
                    $detalle = json_encode([
                        'piezas' => [
                            ['nombre' => 'Componentes de motor', 'precio' => rand(150, 600)],
                            ['nombre' => 'Piezas de recambio', 'precio' => $precioPiezas - rand(150, 600)]
                        ],
                        'servicios' => [
                            ['nombre' => 'Reparación', 'precio' => $precioRevisiones * 0.7],
                            ['nombre' => 'Diagnóstico y pruebas', 'precio' => $precioRevisiones * 0.3]
                        ]
                    ]);
                    
                    // Crear el registro de pago
                    DB::table('pago_taller')->insert([
                        'mantenimiento_id' => null,
                        'averia_id' => $averia->id,
                        'precio_piezas' => $precioPiezas,
                        'precio_revisiones' => $precioRevisiones,
                        'total' => $total,
                        'detalle' => $detalle,
                        'created_at' => Carbon::now()->subDays(rand(1, 30)), // Para que caigan en el mes actual
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
        
        // Si no hay mantenimientos ni averías, crear registros independientes
        if ($crearRegistrosIndependientes) {
            // Crear entre 30 y 50 pagos de taller independientes
            $numPagos = rand(30, 50);
            $this->command->info("Creando {$numPagos} pagos de taller independientes.");
            
            // Generar registros para los últimos 12 meses
            $fechaInicio = Carbon::now()->subMonths(12);
            $fechaFin = Carbon::now();
            $diasRango = $fechaFin->diffInDays($fechaInicio);
            
            for ($i = 0; $i < $numPagos; $i++) {
                // Decidir aleatoriamente si es mantenimiento o avería (para el detalle)
                $tipoServicio = rand(0, 1) ? 'Mantenimiento' : 'Avería';
                
                // Generar datos diferentes según el tipo
                if ($tipoServicio == 'Mantenimiento') {
                    $precioPiezas = rand(50, 800);
                    $precioRevisiones = rand(75, 500);
                    $nombreServicio = $faker->randomElement(['Revisión periódica', 'Cambio de aceite', 'Revisión de frenos', 'Alineación y balanceo', 'Cambio de neumáticos']);
                } else {
                    $precioPiezas = rand(200, 1500);
                    $precioRevisiones = rand(150, 800);
                    $nombreServicio = $faker->randomElement(['Reparación de motor', 'Reparación de transmisión', 'Cambio de embrague', 'Reparación sistema eléctrico', 'Sustitución de bomba de agua']);
                }
                
                $total = $precioPiezas + $precioRevisiones;
                $fechaCreacion = $fechaInicio->copy()->addDays(rand(0, $diasRango));
                
                // Detalle del pago (JSON) - más descriptivo para pagos independientes
                $detalle = json_encode([
                    'tipo_servicio' => $tipoServicio,
                    'descripcion' => $nombreServicio,
                    'vehiculo' => $faker->randomElement(['Ford Focus', 'Seat Ibiza', 'Volkswagen Golf', 'Toyota Corolla', 'Renault Clio', 'Peugeot 208']),
                    'piezas' => [
                        ['nombre' => $faker->randomElement(['Filtro de aceite', 'Filtro de aire', 'Pastillas de freno', 'Amortiguadores', 'Correa distribución']), 'precio' => rand(30, 150)],
                        ['nombre' => $faker->randomElement(['Aceite motor', 'Líquido de frenos', 'Anticongelante', 'Bujías', 'Batería']), 'precio' => rand(20, 100)],
                        ['nombre' => 'Otras piezas y consumibles', 'precio' => $precioPiezas - rand(50, 250)]
                    ],
                    'servicios' => [
                        ['nombre' => 'Mano de obra', 'precio' => $precioRevisiones * 0.7],
                        ['nombre' => 'Diagnóstico', 'precio' => $precioRevisiones * 0.3]
                    ]
                ]);
                
                // Crear el registro de pago (sin referencias a mantenimientos o averías)
                DB::table('pago_taller')->insert([
                    'mantenimiento_id' => null,
                    'averia_id' => null,
                    'precio_piezas' => $precioPiezas,
                    'precio_revisiones' => $precioRevisiones,
                    'total' => $total,
                    'detalle' => $detalle,
                    'created_at' => $fechaCreacion,
                    'updated_at' => $fechaCreacion
                ]);
            }
        }
        
        $this->command->info('Pagos de reparaciones mecánicas creados correctamente.');
    }
}
