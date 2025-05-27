<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('parking', function (Blueprint $table) {
            $table->decimal('metros_cuadrados', 10, 2)->default(0);
            $table->decimal('precio_metro_cuadrado', 10, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking', function (Blueprint $table) {
            $table->dropColumn(['metros_cuadrados', 'precio_metro_cuadrado']);
        });
    }
};
