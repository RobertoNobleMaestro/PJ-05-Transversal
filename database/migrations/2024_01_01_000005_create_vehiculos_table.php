<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiculosTable extends Migration
{
    public function up()
    {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id('id_vehiculos');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('kilometraje');
            $table->year('año');
            $table->decimal('precio_dia', 8, 2);

            $table->unsignedBigInteger('id_lugar');
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares');
            $table->foreign('id_tipo')->references('id_tipo')->on('tipo');

            $table->unsignedBigInteger('parking_id');
            $table->foreign('parking_id')->references('id')->on('parking')->onDelete('cascade');

            $table->date('ultima_fecha_mantenimiento')->nullable();
            $table->date('proxima_fecha_mantenimiento')->nullable();

            $table->timestamps();
        });


    }

    public function down()
    {
        Schema::dropIfExists('vehiculos');
    }
}
