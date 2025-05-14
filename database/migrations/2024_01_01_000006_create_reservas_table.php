<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('id_reservas');
            $table->date('fecha_reserva');
            $table->decimal('total_precio', 10, 2);
            $table->string('estado');

            $table->unsignedBigInteger('id_lugar');
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares')->onDelete('cascade');

            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');

            $table->string('referencia_pago')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('reservas');
    }
};