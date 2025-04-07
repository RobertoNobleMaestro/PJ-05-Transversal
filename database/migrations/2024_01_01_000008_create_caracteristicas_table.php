
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('caracteristicas', function (Blueprint $table) {
            $table->id('id_caracteristicas');
            $table->boolean('techo');
            $table->string('transmision');
            $table->unsignedBigInteger('id_vehiculos');
            $table->integer('num_puertas');
            $table->string('etiqueta_medioambiental');
            $table->boolean('aire_acondicionado');
            $table->integer('capacidad_maletero');
            $table->foreign('id_vehiculos')->references('id_vehiculos')->on('vehiculos');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('caracteristicas');
    }
};
