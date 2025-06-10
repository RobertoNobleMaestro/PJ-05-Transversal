<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Cambia 'licencia_conducir' a VARCHAR(20) y permite NULLs.
            // El método change() requiere el paquete doctrine/dbal.
            $table->string('licencia_conducir', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Revierte a un tamaño anterior si es necesario (ej. VARCHAR(1) si ese era el caso).
            // Ajusta esto si el estado original era diferente.
            $table->string('licencia_conducir', 1)->nullable()->change();
        });
    }
};
