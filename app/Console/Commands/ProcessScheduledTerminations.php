<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asalariado;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ProcessScheduledTerminations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-terminations {--force : Procesar bajas sin importar la fecha actual}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa las bajas programadas de asalariados cuando llega la fecha establecida';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando procesamiento de bajas programadas...');
        
        try {
            $today = Carbon::now()->startOfDay();
            $forceProcesar = $this->option('force');
            
            // Buscar asalariados con bajas programadas pendientes
            $query = Asalariado::where('estado_baja_programada', 'pendiente');
            
            // Si no estamos forzando, solo procesar las que ya han llegado a su fecha
            if (!$forceProcesar) {
                $query->whereDate('fecha_baja_programada', '<=', $today);
            }
            
            $asalariadosPendientes = $query->get();
            
            $this->info("Se encontraron {$asalariadosPendientes->count()} asalariados con bajas programadas para procesar.");
            
            if ($asalariadosPendientes->isEmpty()) {
                $this->info("No hay bajas programadas pendientes para procesar.");
                return 0;
            }
            
            // Iniciar una barra de progreso
            $bar = $this->output->createProgressBar($asalariadosPendientes->count());
            $bar->start();
            
            // Variables para las estadísticas
            $procesados = 0;
            $errores = 0;
            
            foreach ($asalariadosPendientes as $asalariado) {
                try {
                    // Calcular días trabajados desde su contratación o última alta hasta hoy
                    $fechaInicio = $asalariado->hiredate;
                    $fechaActual = Carbon::now();
                    $nuevosDiasTrabajados = $fechaInicio->diffInDays($fechaActual) + 1; // +1 para incluir el día actual
                    
                    // Sumar a los días trabajados que ya tenía acumulados (si hay)
                    $diasTrabajadosTotal = ($asalariado->dias_trabajados ?? 0) + $nuevosDiasTrabajados;
                    
                    // Registrar la fecha de baja para calcular el periodo inactivo cuando se reactive
                    $asalariado->fecha_baja = $fechaActual;
                    
                    // Actualizar estado y días trabajados
                    $asalariado->estado = 'baja';
                    $asalariado->dias_trabajados = $diasTrabajadosTotal;
                    
                    // Marcar la baja programada como completada
                    $asalariado->estado_baja_programada = 'completada';
                    
                    $asalariado->save();
                    
                    // Registrar la acción para auditoría
                    Log::info('Procesada baja programada para asalariado ID ' . $asalariado->id . '. Días trabajados acumulados: ' . $diasTrabajadosTotal);
                    
                    $procesados++;
                } catch (\Exception $e) {
                    $this->error("Error al procesar la baja del asalariado ID {$asalariado->id}: {$e->getMessage()}");
                    Log::error("Error al procesar la baja programada del asalariado ID {$asalariado->id}: {$e->getMessage()}");
                    $errores++;
                }
                
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine(2);
            
            // Mostrar resumen
            $this->info("Procesamiento completado:");
            $this->info("- Bajas procesadas correctamente: {$procesados}");
            $this->info("- Errores: {$errores}");
            
            return 0; // Éxito
            
        } catch (\Exception $e) {
            $this->error("Error al ejecutar el comando: {$e->getMessage()}");
            Log::error("Error al procesar bajas programadas: {$e->getMessage()}");
            return 1; // Error
        }
    }
}
