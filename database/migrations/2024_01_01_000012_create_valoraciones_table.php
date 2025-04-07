
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id('id_valoraciones');
            $table->text('comentario');
            $table->tinyInteger('valoracion');
            $table->unsignedBigInteger('id_reservas');
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_reservas')->references('id_reservas')->on('reservas');
            $table->foreign('id_usuario')->references('id_usuario')->on('users');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('valoraciones');
    }
};
