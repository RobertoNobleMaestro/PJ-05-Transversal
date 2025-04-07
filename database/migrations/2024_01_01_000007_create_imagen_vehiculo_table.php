
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('imagen_vehiculo', function (Blueprint $table) {
            $table->id('id_imagen_vehiculo');
            $table->string('nombre_archivo');
            $table->unsignedBigInteger('id_vehiculo');
            $table->foreign('id_vehiculo')->references('id_vehiculos')->on('vehiculos');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('imagen_vehiculo');
    }
};
