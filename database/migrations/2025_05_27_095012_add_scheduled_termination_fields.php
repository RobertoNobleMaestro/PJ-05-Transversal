<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('asalariados', function (Blueprint $table) {
            // Fecha programada para la baja del asalariado
            $table->date('fecha_baja_programada')->nullable();
            // Estado de la baja programada (pendiente, completada, cancelada)
            $table->string('estado_baja_programada', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asalariados', function (Blueprint $table) {
            $table->dropColumn('fecha_baja_programada');
            $table->dropColumn('estado_baja_programada');
        });
    }
};
