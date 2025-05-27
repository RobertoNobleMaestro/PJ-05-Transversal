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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->string('concepto');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['mantenimiento', 'salario', 'parking', 'otros']);
            $table->decimal('importe', 10, 2);
            $table->date('fecha');
            $table->foreignId('id_vehiculo')->nullable()->constrained('vehiculos', 'id_vehiculos')->onDelete('set null');
            $table->foreignId('id_parking')->nullable()->references('id')->on('parking')->onDelete('set null');
            $table->foreignId('id_asalariado')->nullable()->constrained('asalariados')->onDelete('set null');
            $table->foreignId('id_mantenimiento')->nullable()->constrained('mantenimientos')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
