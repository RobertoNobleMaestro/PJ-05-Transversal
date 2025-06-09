<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asalariado;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateWorkedDaysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asalariados:update-worked-days';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the "dias_trabajados" field for active employees based on their hire date (6 days per week).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting update of worked days for active employees...');

        $today = Carbon::today();
        $processedCount = 0;
        $skippedCount = 0;

        // Fetch active asalariados chunk by chunk to conserve memory
        Asalariado::where('estado', 'alta')
            ->chunkById(100, function ($asalariados) use ($today, &$processedCount, &$skippedCount) {
                DB::beginTransaction();
                try {
                    foreach ($asalariados as $asalariado) {
                        if (!$asalariado->hiredate) {
                            $this->warn("Skipping Asalariado ID: {$asalariado->id} (User ID: {$asalariado->id_usuario}) due to missing hire date.");
                            $skippedCount++;
                            continue;
                        }

                        $hireDate = Carbon::parse($asalariado->hiredate)->startOfDay();

                        if ($hireDate->isAfter($today)) {
                            $this->warn("Skipping Asalariado ID: {$asalariado->id} (User ID: {$asalariado->id_usuario}) as hire date ({$hireDate->toDateString()}) is in the future. Setting worked days to 0.");
                            $asalariado->dias_trabajados = 0;
                            $skippedCount++;
                        } else {
                            // Calculate total calendar days from hire date to today (inclusive)
                            $calendarDays = $hireDate->diffInDays($today) + 1;

                            $fullWeeks = floor($calendarDays / 7);
                            $remainingDaysInLastWeek = $calendarDays % 7;
                            
                            $calculatedWorkedDays = ($fullWeeks * 6) + min($remainingDaysInLastWeek, 6);

                            if ($calculatedWorkedDays < 0) $calculatedWorkedDays = 0; // Safeguard

                            $this->line("Processing Asalariado ID: {$asalariado->id} (User ID: {$asalariado->id_usuario}), Hire: {$hireDate->toDateString()}, Calendar Days: {$calendarDays}, Original Worked: {$asalariado->dias_trabajados}, New Worked: {$calculatedWorkedDays}");
                            $asalariado->dias_trabajados = $calculatedWorkedDays;
                        }
                        
                        $asalariado->save();
                        $processedCount++;
                    }
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->error("An error occurred during a chunk processing Asalariado ID: {$asalariado->id}: " . $e->getMessage());
                    // This will stop the command on the first error encountered in a chunk.
                    // You might want to log the error and continue with other records/chunks depending on requirements.
                    throw $e; 
                }
            });

        if ($processedCount > 0) {
            $this->info("Successfully processed {$processedCount} active employees.");
        } else if ($skippedCount > 0 && $processedCount == 0) {
             $this->info("No employees were updated. {$skippedCount} employees were skipped.");
        }
        else {
            $this->info('No active employees found or no updates were needed.');
        }
        if ($skippedCount > 0) {
            $this->warn("Skipped a total of {$skippedCount} employees due to missing/future hire dates or other issues.");
        }

        $this->info('Finished updating worked days.');
        return Command::SUCCESS;
    }
}
