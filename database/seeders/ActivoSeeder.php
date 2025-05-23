<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activo;
use App\Models\Lugar;
use Carbon\Carbon;

class ActivoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Obtener todos los lugares (sedes) para asignar activos
        $lugares = Lugar::all();
        
        if ($lugares->count() == 0) {
            $this->command->info('No hay lugares disponibles para crear activos. Ejecuta LugarSeeder primero.');
            return;
        }
        
        // Categorías de activos
        $categorias = ['Circulante', 'Fijo', 'Intangible', 'Diferido', 'Inversiones'];
        
        // Para cada lugar, crear varios activos
        foreach ($lugares as $lugar) {
            // Activos circulantes (efectivo, cuentas por cobrar, etc.)
            Activo::create([
                'nombre' => 'Efectivo en caja',
                'descripcion' => 'Dinero disponible para operaciones diarias',
                'categoria' => 'Circulante',
                'valor' => rand(5000, 15000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 30)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Activo::create([
                'nombre' => 'Cuentas por cobrar',
                'descripcion' => 'Deudas de clientes pendientes de cobro',
                'categoria' => 'Circulante',
                'valor' => rand(8000, 25000),
                'fecha_registro' => Carbon::now()->subDays(rand(1, 30)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            // Activos fijos (inmuebles, vehículos propios, etc.)
            Activo::create([
                'nombre' => 'Local comercial',
                'descripcion' => 'Propiedad donde opera la sede',
                'categoria' => 'Fijo',
                'valor' => rand(200000, 500000),
                'fecha_registro' => Carbon::now()->subYears(rand(1, 5)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Activo::create([
                'nombre' => 'Flota de vehículos propia',
                'descripcion' => 'Vehículos en propiedad (no de alquiler)',
                'categoria' => 'Fijo',
                'valor' => rand(100000, 350000),
                'fecha_registro' => Carbon::now()->subMonths(rand(6, 24)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Activo::create([
                'nombre' => 'Mobiliario y equipamiento',
                'descripcion' => 'Muebles, ordenadores y equipos de oficina',
                'categoria' => 'Fijo',
                'valor' => rand(15000, 40000),
                'fecha_registro' => Carbon::now()->subMonths(rand(1, 12)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            // Activos intangibles
            Activo::create([
                'nombre' => 'Software de gestión',
                'descripcion' => 'Licencias de software para gestión de flota y reservas',
                'categoria' => 'Intangible',
                'valor' => rand(5000, 25000),
                'fecha_registro' => Carbon::now()->subMonths(rand(1, 12)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            Activo::create([
                'nombre' => 'Marca registrada',
                'descripcion' => 'Valor de la marca Carflow en el mercado',
                'categoria' => 'Intangible',
                'valor' => rand(50000, 150000),
                'fecha_registro' => Carbon::now()->subYears(2),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
            
            // Inversiones
            Activo::create([
                'nombre' => 'Inversiones a largo plazo',
                'descripcion' => 'Fondos de inversión para crecimiento',
                'categoria' => 'Inversiones',
                'valor' => rand(30000, 80000),
                'fecha_registro' => Carbon::now()->subMonths(rand(3, 18)),
                'fecha_actualizacion' => Carbon::now(),
                'id_lugar' => $lugar->id_lugar
            ]);
        }
        
        $this->command->info('Activos creados correctamente.');
    }
}
