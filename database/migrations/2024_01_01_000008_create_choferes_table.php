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
        Schema::create('choferes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_usuario')->references('id_usuario')->on('users');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);
            $table->enum('estado', ['disponible', 'ocupado', 'inactivo']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('choferes');
    }
};
