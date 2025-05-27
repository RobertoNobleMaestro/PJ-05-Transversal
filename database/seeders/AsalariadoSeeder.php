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
        // Obtener todos los usuarios asalariados (roles 3, 4, 5, 6)
        $usuarios = User::whereIn('id_roles', [3, 4, 5, 6])->get();
        $parkings = Parking::all();

        // Mapear ciudades a id_lugar
        $lugares = [
            'Barcelona' => 2,
            'Madrid' => 1,
            'Valencia' => 3,
        ];

        foreach ($usuarios as $usuario) {
            $parkingAsignado = null;
            $ciudad = null;

            // Detectar ciudad por nombre del usuario
            if (stripos($usuario->nombre, 'Barcelona') !== false) {
                $ciudad = 'Barcelona';
            } elseif (stripos($usuario->nombre, 'Madrid') !== false) {
                $ciudad = 'Madrid';
            } elseif (stripos($usuario->nombre, 'Valencia') !== false) {
                $ciudad = 'Valencia';
            }

            if ($ciudad && isset($lugares[$ciudad])) {
                // Buscar el primer parking de la ciudad correspondiente
                $parkingCiudad = $parkings->where('id_lugar', $lugares[$ciudad])->first();
                if ($parkingCiudad) {
                    $parkingAsignado = $parkingCiudad->id;
                }
            }

            // Si no se detecta ciudad, asignar uno random (fallback)
            if (!$parkingAsignado) {
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
                case 6: // Chofer
                    $salarioBase = 1400.00;
                    break;
            }

            // Añadir variación aleatoria al salario base (±200€)
            $salario = $salarioBase + rand(-200, 200);
            
            // Fecha de contratación aleatoria entre 30 y 365 días atrás
            $hiredate = now()->subDays(rand(30, 365));
            
            // Insertar el registro
            DB::table('asalariados')->insert([
                'id_usuario' => $usuario->id_usuario,
                'salario' => $salario,
                'hiredate' => $hiredate,
                'parking_id' => $parkingAsignado,
                'estado' => 'alta',
                'dias_trabajados' => min(rand(15, 22), now()->day),
                'id_lugar' => $parkings->random()->id_lugar,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
