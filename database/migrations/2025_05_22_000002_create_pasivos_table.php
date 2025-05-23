<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePasivosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasivos', function (Blueprint $table) {
            $table->id('id_pasivo');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria'); // circulante, fijo, largo plazo, etc.
            $table->decimal('valor', 10, 2);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->timestamp('fecha_vencimiento')->nullable();
            $table->timestamp('fecha_actualizacion')->nullable();
            $table->unsignedBigInteger('id_lugar');
            $table->timestamps();

            $table->foreign('id_lugar')->references('id_lugar')->on('lugares')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pasivos');
    }
}
