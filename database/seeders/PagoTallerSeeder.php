<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PagoTallerSeeder extends Seeder
{
    /**
     * Run the database seeds to create payment records for mechanic repairs.
     */
    public function run(): void
    {
        $this->command->info('Creando pagos de reparaciones mecánicas...');

        // Obtener todos los mantenimientos
        $mantenimientos = DB::table('mantenimientos')->get();
        
        // Obtener algunas averías si existen
        try {
            $averias = DB::table('averias')->get();
        } catch (\Exception $e) {
            $averias = collect([]);
        }
        
        // Crear pagos para un porcentaje de los mantenimientos
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
        
        // Crear pagos para un porcentaje de las averías
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
        
        $this->command->info('Pagos de reparaciones mecánicas creados correctamente.');
    }
}
