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
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_chofer')->references('id')->on('choferes');
            $table->foreignId('id_cliente')->references('id_usuario')->on('users');
            $table->decimal('latitud_origen', 10, 8);
            $table->decimal('longitud_origen', 11, 8);
            $table->decimal('latitud_destino', 10, 8);
            $table->decimal('longitud_destino', 11, 8);
            $table->decimal('precio', 10, 2);
            $table->enum('estado', ['pendiente', 'aceptada', 'rechazada', 'completada', 'cancelada'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
