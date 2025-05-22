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
            $table->foreignId('id_usuario_solicitado')->references('id_usuario')->on('users');
            $table->decimal('latitud_solicitante', 10, 8);
            $table->decimal('longitud_solicitante', 11, 8);
            $table->foreignId('chofer_id')->nullable()->constrained('choferes');
            $table->enum('estado_solicitud', ['pendiente', 'aceptada', 'en_progreso', 'completada', 'cancelada']);
            $table->decimal('latitud_destino', 10, 8);
            $table->decimal('longitud_destino', 11, 8);
            $table->unsignedBigInteger('id_lugar')->nullable();
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares');
            $table->decimal('precio', 10, 2);
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
