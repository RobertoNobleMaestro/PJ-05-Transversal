<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero añadimos la columna dia_cobro
        Schema::table('asalariados', function (Blueprint $table) {
            if (!Schema::hasColumn('asalariados', 'dia_cobro')) {
                $table->integer('dia_cobro')->default(5)->after('salario');
            }
        });
        
        // Luego actualizamos sus valores basados en hiredate (si existe)
        if (Schema::hasColumn('asalariados', 'hiredate') && Schema::hasColumn('asalariados', 'dia_cobro')) {
            DB::statement('UPDATE asalariados SET dia_cobro = DAY(hiredate) WHERE hiredate IS NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asalariados', function (Blueprint $table) {
            if (Schema::hasColumn('asalariados', 'dia_cobro')) {
                $table->dropColumn('dia_cobro');
            }
        });
    }
};
