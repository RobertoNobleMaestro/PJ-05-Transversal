<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('averia_pieza', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('averia_id');
            $table->unsignedBigInteger('pieza_id');
            $table->integer('cantidad')->default(1);
            $table->timestamps();

            $table->foreign('averia_id')->references('id')->on('averias')->onDelete('cascade');
            $table->foreign('pieza_id')->references('id')->on('piezas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('averia_pieza');
    }
};
