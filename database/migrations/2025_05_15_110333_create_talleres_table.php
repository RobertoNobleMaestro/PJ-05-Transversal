<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        Schema::create('talleres', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('telefono')->nullable();
            $table->integer('capacidad_hora')->default(2);
            $table->timestamps();
        });

        // Insert some initial workshops
        DB::table('talleres')->insert([
            ['nombre' => 'Taller Central', 'direccion' => 'Calle Principal 123', 'telefono' => '912345678', 'capacidad_hora' => 2],
            ['nombre' => 'Taller Express', 'direccion' => 'Avenida Norte 456', 'telefono' => '923456789', 'capacidad_hora' => 2],
            ['nombre' => 'Taller Premium', 'direccion' => 'Plaza Mayor 789', 'telefono' => '934567890', 'capacidad_hora' => 2],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('talleres');
    }
};
