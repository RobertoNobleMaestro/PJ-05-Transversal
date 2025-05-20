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
        Schema::create('grupo_usuario', function (Blueprint $table) {
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('id_usuario');
            $table->timestamps();
            
            // Claves primarias compuestas
            $table->primary(['grupo_id', 'id_usuario']);
            
            // Claves foráneas
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupo_usuario');
    }
};
