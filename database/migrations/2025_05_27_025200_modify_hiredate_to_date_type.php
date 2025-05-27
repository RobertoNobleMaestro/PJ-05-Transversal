<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Enfoque más seguro: crear una nueva columna, migrar datos, eliminar la antigua
        
        // 1. Añadir columna temporal de tipo date
        Schema::table('asalariados', function (Blueprint $table) {
            $table->date('hiredate_new')->nullable()->after('hiredate');
        });
        
        // 2. Migrar datos - convertir enteros a fechas
        $asalariados = DB::table('asalariados')->get(['id', 'hiredate']);
        
        foreach ($asalariados as $asalariado) {
            // Si hiredate es un entero, construimos una fecha usando ese día del mes actual
            if (is_numeric($asalariado->hiredate)) {
                $day = min((int)$asalariado->hiredate, 28); // Aseguramos que el día sea válido para todos los meses
                // Construir una fecha con el año y mes actual, pero con el día del entero
                $fecha = Carbon::now()->setDay($day)->format('Y-m-d');
                
                DB::table('asalariados')
                    ->where('id', $asalariado->id)
                    ->update(['hiredate_new' => $fecha]);
            }
        }
        
        // 3. Eliminar columna antigua
        Schema::table('asalariados', function (Blueprint $table) {
            $table->dropColumn('hiredate');
        });
        
        // 4. Renombrar columna nueva a nombre original
        Schema::table('asalariados', function (Blueprint $table) {
            $table->renameColumn('hiredate_new', 'hiredate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Añadir columna temporal de tipo entero
        Schema::table('asalariados', function (Blueprint $table) {
            $table->integer('hiredate_old')->nullable()->after('hiredate');
        });
        
        // 2. Migrar datos - convertir fechas a enteros (días del mes)
        $asalariados = DB::table('asalariados')->get(['id', 'hiredate']);
        
        foreach ($asalariados as $asalariado) {
            if ($asalariado->hiredate) {
                try {
                    $fecha = Carbon::parse($asalariado->hiredate);
                    $dia = $fecha->day;
                    
                    DB::table('asalariados')
                        ->where('id', $asalariado->id)
                        ->update(['hiredate_old' => $dia]);
                } catch (\Exception $e) {
                    // Si hay error al parsear, usamos un valor por defecto
                    DB::table('asalariados')
                        ->where('id', $asalariado->id)
                        ->update(['hiredate_old' => 1]);
                }
            }
        }
        
        // 3. Eliminar columna de fecha
        Schema::table('asalariados', function (Blueprint $table) {
            $table->dropColumn('hiredate');
        });
        
        // 4. Renombrar columna temporal a nombre original
        Schema::table('asalariados', function (Blueprint $table) {
            $table->renameColumn('hiredate_old', 'hiredate');
        });
    }
};
