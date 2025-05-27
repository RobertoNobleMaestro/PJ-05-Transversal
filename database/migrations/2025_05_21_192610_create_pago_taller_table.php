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
        Schema::create('pago_taller', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('factura_id')->nullable(); // Relación con facturas
            $table->unsignedBigInteger('mantenimiento_id')->nullable(); // Relación con mantenimientos
            $table->unsignedBigInteger('averia_id')->nullable(); // Relación con averías
            $table->decimal('precio_piezas', 10, 2)->default(0); // Suma de precios de piezas
            $table->decimal('precio_revisiones', 10, 2)->default(0); // Suma de precios de revisiones/servicios
            $table->decimal('total', 10, 2); // Total final (piezas + revisiones)
            $table->json('detalle')->nullable(); // Detalle completo (puede incluir desglose)
            $table->timestamps();

            // Foreign keys
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('set null');
            $table->foreign('mantenimiento_id')->references('id')->on('mantenimientos')->onDelete('set null');
            $table->foreign('averia_id')->references('id')->on('averias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_taller');
    }
};
