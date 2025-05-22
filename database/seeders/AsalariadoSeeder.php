<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Parking;

class AsalariadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todos los usuarios que son asalariados (roles 3, 4 y 5)
        $usuarios = User::whereIn('id_roles', [3, 4, 5, 6])->get();
        
        // Obtener todos los parkings disponibles
        $parkings = Parking::all();
        
        foreach ($usuarios as $usuario) {
            // Asignar un parking según la lógica de negocio (por ejemplo, por ciudad)
            // Si el usuario ya tiene parkings asignados, usar el primero
            $parkingAsignado = null;
            
            if ($usuario->parkings->count() > 0) {
                $parkingAsignado = $usuario->parkings->first()->id;
            } else {
                // Asignar uno aleatorio si no tiene
                $parkingAsignado = $parkings->random()->id;
            }
            
            // Generar salario base según el rol
            $salarioBase = 0;
            switch ($usuario->id_roles) {
                case 3: // Gestor
                    $salarioBase = 1800.00;
                    break;
                case 4: // Mecánico
                    $salarioBase = 1600.00;
                    break;
                case 5: // Admin Financiero
                    $salarioBase = 2200.00;
                    break;
            }
            
            // Añadir variación aleatoria al salario base (±200€)
            $salario = $salarioBase + rand(-200, 200);
            
            // Día de cobro aleatorio entre 1 y 10
            $diaCobro = rand(1, 10);
            
            // Insertar el registro
            DB::table('asalariados')->insert([
                'id_usuario' => $usuario->id_usuario,
                'salario' => $salario,
                'dia_cobro' => $diaCobro,
                'parking_id' => $parkingAsignado,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
