<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('gastos_taller', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pieza_id');
            $table->integer('cantidad');
            $table->decimal('precio_pieza', 10, 2);
            $table->unsignedBigInteger('mantenimiento_id')->nullable();
            $table->unsignedBigInteger('averia_id')->nullable();
            $table->unsignedBigInteger('factura_id')->nullable();
            $table->timestamps();

            $table->foreign('pieza_id')->references('id')->on('piezas')->onDelete('cascade');
            $table->foreign('mantenimiento_id')->references('id')->on('mantenimientos')->onDelete('set null');
            $table->foreign('averia_id')->references('id')->on('averias')->onDelete('set null');
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos_taller');
    }
}; 