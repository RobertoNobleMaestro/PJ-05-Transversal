<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pasivo;
use App\Models\Lugar;
use Carbon\Carbon;

class PasivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los lugares (sedes) para asignar pasivos
        $lugares = Lugar::all();
        
        if ($lugares->count() == 0) {
            $this->command->info('No hay lugares disponibles para crear pasivos. Ejecuta LugarSeeder primero.');
            return;
        }
        
        // Categorías de pasivos
        $categorias = ['Circulante', 'Fijo', 'Largo Plazo', 'Diferido'];
        
        // Para cada lugar, crear varios pasivos
        foreach ($lugares as $lugar) {
            // Pasivos circulantes (corto plazo)
            Pasivo::create([
                'nombre' => 'Cuentas por pagar',
                'descripcion' => 'Pagos pendientes a proveedores',
                'categoria' => 'Circulante',
                'valor' => rand(3000, 12000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 30)),
                'fecha_vencimiento' => Carbon::now()->addDays(rand(15, 45)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Pasivo::create([
                'nombre' => 'Impuestos por pagar',
                'descripcion' => 'IVA y otros impuestos pendientes',
                'categoria' => 'Circulante',
                'valor' => rand(5000, 15000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 30)),
                'fecha_vencimiento' => Carbon::now()->addDays(rand(30, 60)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Pasivo::create([
                'nombre' => 'Salarios pendientes',
                'descripcion' => 'Nómina del mes en curso',
                'categoria' => 'Circulante',
                'valor' => rand(8000, 20000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 15)),
                'fecha_vencimiento' => Carbon::now()->addDays(rand(1, 15)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            // Pasivos fijos o a largo plazo
            Pasivo::create([
                'nombre' => 'Préstamo bancario',
                'descripcion' => 'Hipoteca del local comercial',
                'categoria' => 'Largo Plazo',
                'valor' => rand(150000, 350000),
                'fecha_registro' => Carbon::now()->subYears(rand(1, 3)),
                'fecha_vencimiento' => Carbon::now()->addYears(rand(1, 5)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Pasivo::create([
                'nombre' => 'Leasing vehículos',
                'descripcion' => 'Contratos de arrendamiento financiero de vehículos',
                'categoria' => 'Fijo',
                'valor' => rand(50000, 150000),
                'fecha_registro' => Carbon::now()->subMonths(rand(1, 12)),
                'fecha_vencimiento' => Carbon::now()->addYears(rand(1, 3)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            // Pasivos diferidos
            Pasivo::create([
                'nombre' => 'Ingresos cobrados por adelantado',
                'descripcion' => 'Reservas pagadas pendientes de servicio',
                'categoria' => 'Diferido',
                'valor' => rand(10000, 30000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 30)),
                'fecha_vencimiento' => Carbon::now()->addMonths(rand(1, 3)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
        }
        
        $this->command->info('Pasivos creados correctamente.');
    }
}
