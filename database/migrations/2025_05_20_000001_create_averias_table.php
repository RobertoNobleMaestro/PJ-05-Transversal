<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('averias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehiculo_id');
            $table->string('descripcion');
            $table->timestamp('fecha')->nullable();
            $table->timestamps();

            $table->foreign('vehiculo_id')->references('id_vehiculos')->on('vehiculos')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('averias');
    }
};
