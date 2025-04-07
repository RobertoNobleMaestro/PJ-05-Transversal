
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('vehiculos_reservas', function (Blueprint $table) {
            $table->id('id_vehiculos_reservas');
            $table->date('fecha_ini');
            $table->decimal('precio_unitario', 10, 2);
            $table->date('fecha_final');
            $table->unsignedBigInteger('id_reservas');
            $table->unsignedBigInteger('id_vehiculos');
            $table->foreign('id_reservas')->references('id_reservas')->on('reservas');
            $table->foreign('id_vehiculos')->references('id_vehiculos')->on('vehiculos');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('vehiculos_reservas');
    }
};
