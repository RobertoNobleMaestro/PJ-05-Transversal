<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateMantenimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculo_id');
            $table->unsignedBigInteger('taller_id');
            $table->date('fecha_programada');
            $table->time('hora_programada');
            $table->string('estado')->default('pendiente'); // pendiente, completado, cancelado
            $table->timestamps();

            $table->foreign('vehiculo_id')->references('id_vehiculos')->on('vehiculos')->onDelete('cascade');
            $table->foreign('taller_id')->references('id')->on('talleres')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mantenimientos');
    }
}