
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('metodos_de_pago', function (Blueprint $table) {
            $table->id('id_metodoPago');
            $table->string('nombre');
            $table->unsignedBigInteger('id_pago');
            $table->foreign('id_pago')->references('id_pago')->on('pago');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('metodos_de_pago');
    }
};
