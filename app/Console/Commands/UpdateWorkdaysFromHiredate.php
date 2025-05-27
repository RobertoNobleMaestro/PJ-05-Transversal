<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asalariado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateWorkdaysFromHiredate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-workdays-from-hiredate {--only-active : Actualizar solo asalariados activos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualiza los días trabajados de los asalariados basándose en su fecha de contratación';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando actualización de días trabajados...');
        
        try {
            // Determinar si solo actualizamos asalariados activos
            $onlyActive = $this->option('only-active');
            
            // Crear la consulta base
            $query = Asalariado::query();
            
            // Filtrar por estado si es necesario
            if ($onlyActive) {
                $query->where('estado', 'alta');
                $this->info('Actualizando solo asalariados con estado "alta".');
            } else {
                $this->info('Actualizando todos los asalariados independientemente de su estado.');
            }
            
            // Contar cuántos asalariados vamos a actualizar
            $total = $query->count();
            $this->info("Se encontraron {$total} asalariados para actualizar.");
            
            // Iniciar una barra de progreso
            $bar = $this->output->createProgressBar($total);
            $bar->start();
            
            // Obtener todos los asalariados según el filtro
            $asalariados = $query->get();
            
            // Variables para las estadísticas
            $actualizados = 0;
            $sinCambios = 0;
            $errores = 0;
            
            // Fecha actual para los cálculos
            $now = Carbon::now();
            
            // Procesar cada asalariado
            foreach ($asalariados as $asalariado) {
                try {
                    // Verificar que tiene fecha de contratación
                    if (!$asalariado->hiredate) {
                        $this->error("Asalariado ID {$asalariado->id} no tiene fecha de contratación definida. Saltando...");
                        $errores++;
                        $bar->advance();
                        continue;
                    }
                    
                    // Convertir la fecha de contratación a objeto Carbon si no lo es
                    $hiredate = $asalariado->hiredate instanceof Carbon ? 
                                $asalariado->hiredate : 
                                Carbon::parse($asalariado->hiredate);
                    
                    // Calcular días trabajados desde la contratación hasta hoy
                    $diasTrabajados = $hiredate->diffInDays($now);
                    
                    // Para asalariados inactivos, calculamos hasta su última fecha activa
                    if ($asalariado->estado === 'baja') {
                        // Si hay fecha de última reactivación, usamos esa para el cálculo
                        if ($asalariado->fecha_ultima_reactivacion) {
                            $fechaFinal = Carbon::parse($asalariado->fecha_ultima_reactivacion);
                            $diasTrabajados = $hiredate->diffInDays($fechaFinal);
                        }
                        // Si no hay fecha de reactivación, podemos usar la updated_at como aproximación
                        else {
                            $fechaFinal = $asalariado->updated_at instanceof Carbon ? 
                                       $asalariado->updated_at : 
                                       Carbon::parse($asalariado->updated_at);
                            $diasTrabajados = $hiredate->diffInDays($fechaFinal);
                        }
                    }
                    
                    // Guardar el valor anterior para comparar
                    $valorAnterior = $asalariado->dias_trabajados;
                    
                    // Actualizar el campo dias_trabajados
                    $asalariado->dias_trabajados = $diasTrabajados;
                    $asalariado->save();
                    
                    // Registrar el cambio
                    if ($valorAnterior != $diasTrabajados) {
                        $actualizados++;
                    } else {
                        $sinCambios++;
                    }
                    
                } catch (\Exception $e) {
                    $this->error("Error al procesar asalariado ID {$asalariado->id}: {$e->getMessage()}");
                    $errores++;
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            // Mostrar resumen
            $this->info("Actualización completada:");
            $this->info("- Asalariados actualizados: {$actualizados}");
            $this->info("- Asalariados sin cambios: {$sinCambios}");
            $this->info("- Errores: {$errores}");
            
            return 0; // Éxito
            
        } catch (\Exception $e) {
            $this->error("Error al ejecutar el comando: {$e->getMessage()}");
            return 1; // Error
        }
    }
}
