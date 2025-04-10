
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('pago', function (Blueprint $table) {
            $table->id('id_pago');
            $table->string('estado_pago');
            $table->date('fecha_pago');
            $table->string('referencia_externa');
            $table->decimal('total_precio', 10, 2);
            $table->string('moneda');
            $table->unsignedBigInteger('id_usuario');
            $table->unsignedBigInteger('id_reservas');
            $table->foreign('id_usuario')->references('id_usuario')->on('users');
            $table->foreign('id_reservas')->references('id_reservas')->on('reservas');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('pago');
    }
};
