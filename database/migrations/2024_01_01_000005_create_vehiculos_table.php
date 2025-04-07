
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id('id_vehiculos');
            $table->string('marca');
            $table->string('modelo');
            $table->integer('kilometraje');
            $table->boolean('seguro_incluido');
            $table->year('año');
            $table->unsignedBigInteger('id_lugar');
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_lugar')->references('id_lugar')->on('lugares');
            $table->foreign('id_tipo')->references('id_tipo')->on('tipo');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehiculos');
    }
};
