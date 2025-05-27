<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Parking;
use App\Models\Lugar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateAsalariadosFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Definir salarios estándar por rol
        $salariosPorRol = [
            'admin_financiero' => 3200,
            'gestor' => 2800,
            'mecanico' => 2500,
            'chofer' => 2200,
            // Añadir otros roles según sea necesario
            'admin' => 3500,
            'default' => 2000 // Valor por defecto para roles no definidos
        ];
        
        // Obtener todos los asalariados
        $asalariados = Asalariado::with('usuario', 'parking')->get();
        
        foreach ($asalariados as $asalariado) {
            // Inicializar rol como desconocido por defecto
            $rolNombre = 'desconocido';
            
            // 1. Establecer el estado como 'alta' para todos los asalariados existentes
            $asalariado->estado = 'alta';
            
            // 2. Actualizar días trabajados con valores realistas
            $asalariado->dias_trabajados = min(rand(15, 22), Carbon::now()->day);
            
            // 3. Establecer id_lugar basado en el parking_id
            if ($asalariado->parking) {
                $asalariado->id_lugar = $asalariado->parking->id_lugar;
            }
            
            // 4. Convertir dia_cobro a hiredate si es necesario
            // Si el campo hiredate está vacío y dia_cobro existe, crear una fecha de contratación 
            // estimada (primer día del mes actual con tres meses de antigüedad)
            if (!$asalariado->hiredate) {
                // Crear una fecha aleatoria en los últimos 2 años
                $diasAtras = rand(30, 730); // Entre 1 mes y 2 años
                $asalariado->hiredate = Carbon::now()->subDays($diasAtras)->format('Y-m-d');
            }
            
            // 5. Actualizar salario según el rol
            if ($asalariado->usuario && $asalariado->usuario->roles) {
                $rolNombre = $asalariado->usuario->roles->nombre_rol;
                
                if (isset($salariosPorRol[$rolNombre])) {
                    $asalariado->salario = $salariosPorRol[$rolNombre];
                } else {
                    $asalariado->salario = $salariosPorRol['default'];
                }
            } else {
                // Si no tiene rol asignado, usar el salario por defecto
                $asalariado->salario = $salariosPorRol['default'];
            }
            
            // Guardar cambios
            $asalariado->save();
            
            $this->command->info("Actualizado asalariado ID: {$asalariado->id} - Rol: {$rolNombre} - Salario: {$asalariado->salario}");
        }
        
        $this->command->info("Se han actualizado " . $asalariados->count() . " asalariados");
    }
}
