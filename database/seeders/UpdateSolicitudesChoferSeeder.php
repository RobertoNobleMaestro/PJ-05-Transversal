<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateSolicitudesChoferSeeder extends Seeder
{
    /**
     * Run the database seeds para actualizar las solicitudes pendientes de choferes.
     * Asegura que todas las solicitudes estén en estado 'aceptada' o 'rechazada', nunca 'pendiente'.
     */
    public function run(): void
    {
        $this->command->info('Actualizando solicitudes pendientes de choferes...');
        
        // Verificar si la tabla existe
        if (!Schema::hasTable('solicitudes_chofer')) {
            $this->command->warn('La tabla solicitudes_chofer no existe. No se realizarán cambios.');
            return;
        }
        
        // Contar solicitudes pendientes
        $pendientes = DB::table('solicitudes_chofer')
            ->where('estado', 'pendiente')
            ->count();
            
        if ($pendientes === 0) {
            $this->command->info('No hay solicitudes pendientes para choferes. No se requieren cambios.');
            return;
        }
        
        // Actualizar todas las solicitudes pendientes a 'aceptada'
        $actualizadas = DB::table('solicitudes_chofer')
            ->where('estado', 'pendiente')
            ->update([
                'estado' => 'aceptada',
                'updated_at' => now()
            ]);
            
        $this->command->info("Se actualizaron {$actualizadas} solicitudes de 'pendiente' a 'aceptada'.");
    }
}
