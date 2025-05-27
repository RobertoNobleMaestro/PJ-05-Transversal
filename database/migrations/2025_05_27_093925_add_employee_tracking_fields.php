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
            // Añadir campo para guardar historial de días acumulados
            $table->integer('dias_acumulados_historico')->nullable()->default(0);
            // Añadir campo para guardar la fecha de la última reactivación
            $table->timestamp('fecha_ultima_reactivacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asalariados', function (Blueprint $table) {
            $table->dropColumn('dias_acumulados_historico');
            $table->dropColumn('fecha_ultima_reactivacion');
        });
    }
};
