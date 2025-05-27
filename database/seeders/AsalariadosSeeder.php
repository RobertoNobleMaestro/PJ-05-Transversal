<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsalariadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existen usuarios para asignar
        $users = DB::table('users')->whereNotIn('id_roles', [1])->limit(3)->get();
        
        if ($users->isEmpty()) {
            $this->command->error('No hay usuarios disponibles para asignar como asalariados.');
            return;
        }
        
        // Obtener lugares disponibles
        $lugares = DB::table('lugares')->get();
        if ($lugares->isEmpty()) {
            $this->command->error('No hay lugares disponibles para asignar a los asalariados.');
            return;
        }
        
        // Obtener parkings disponibles
        $parkings = DB::table('parking')->get();
        if ($parkings->isEmpty()) {
            $this->command->error('No hay parkings disponibles para asignar a los asalariados.');
            return;
        }
        
        // Crear asalariados de prueba
        foreach ($users as $index => $user) {
            // Verificar si ya existe un asalariado para este usuario
            $existingAsalariado = DB::table('asalariados')
                ->where('id_usuario', $user->id_usuario)
                ->exists();
                
            if (!$existingAsalariado) {
                $lugar = $lugares->random();
                $parking = $parkings->random();
                
                DB::table('asalariados')->insert([
                    'id_usuario' => $user->id_usuario,
                    'salario' => rand(1200, 2500),
                    'dia_cobro' => rand(1, 28),
                    'hiredate' => Carbon::now()->subMonths(rand(1, 12)), // Fecha de contratación aleatoria en los últimos 12 meses
                    'estado' => 'alta',
                    'dias_trabajados' => min(rand(15, 22), Carbon::now()->day),
                    'id_lugar' => $lugar->id_lugar,
                    'parking_id' => $parking->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                $this->command->info("Asalariado creado para usuario {$user->nombre}");
            }
        }
    }
}
