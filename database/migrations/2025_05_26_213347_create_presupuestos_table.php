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
        Schema::create('presupuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_lugar');
            $table->string('categoria'); // Categoría de gasto (Salarios, Mantenimiento Parkings, etc.)
            $table->decimal('monto', 10, 2); // Monto asignado como presupuesto
            $table->decimal('gasto_real', 10, 2)->nullable(); // Gasto real (para comparar)
            $table->date('fecha_inicio'); // Fecha inicio del período
            $table->date('fecha_fin'); // Fecha fin del período
            $table->string('periodo_tipo'); // mensual, trimestral, anual
            $table->unsignedBigInteger('creado_por'); // ID del usuario que creó el presupuesto
            $table->text('notas')->nullable();
            $table->timestamps();
            
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares');
            $table->foreign('creado_por')->references('id_usuario')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presupuestos');
    }
};
