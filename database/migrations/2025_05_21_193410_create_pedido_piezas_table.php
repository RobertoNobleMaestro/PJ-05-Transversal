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
        Schema::create('pedido_piezas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculo_id'); // Vehículo asociado
            $table->unsignedBigInteger('pieza_id');    // Pieza comprada
            $table->integer('cantidad')->default(1);   // Cantidad de piezas
            $table->decimal('precio_unitario', 10, 2); // Precio unitario de la pieza
            $table->decimal('total', 10, 2);           // Total = cantidad * precio_unitario
            $table->date('fecha_pedido');              // Fecha de compra
            $table->unsignedBigInteger('factura_id')->nullable(); // Relación con factura (opcional)
            $table->timestamps();

            // Foreign keys
            $table->foreign('vehiculo_id')->references('id_vehiculos')->on('vehiculos')->onDelete('cascade');
            $table->foreign('pieza_id')->references('id')->on('piezas')->onDelete('cascade');
            $table->foreign('factura_id')->references('id')->on('facturas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedido_piezas');
    }
};
