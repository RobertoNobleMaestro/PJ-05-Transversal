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
        Schema::create('asalariados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
            $table->decimal('salario', 10, 2); // Salario mensual
            $table->integer('dia_cobro')->default(5); // Día del mes para el pago (por defecto día 5)
            $table->unsignedBigInteger('parking_id')->nullable(); // Parking asignado al asalariado
            $table->foreign('parking_id')->references('id')->on('parking')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asalariados');
    }
};
