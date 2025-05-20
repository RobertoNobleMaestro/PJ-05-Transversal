<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id_usuario')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('gestor_id')->nullable();
            $table->foreign('gestor_id')->references('id_usuario')->on('users')->onDelete('cascade');

            $table->enum('sender_type', ['user', 'gestor']);
            $table->text('message');

            $table->unsignedBigInteger('grupo_id')->nullable();
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('set null');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
