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
        Schema::create('parking', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->integer('plazas');
            $table->decimal('latitud', 10, 8);
            $table->decimal('longitud', 11, 8);

            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')->references('id_usuario')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('id_lugar');
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares')->onDelete('cascade');

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking');
    }
};
