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
        Schema::table('vehiculos', function (Blueprint $table) {
            if (!Schema::hasColumn('vehiculos', 'año')) {
                $table->integer('año')->after('modelo')->nullable();
            }
            if (!Schema::hasColumn('vehiculos', 'kilometraje')) {
                $table->integer('kilometraje')->after('año')->nullable();
            }
            if (!Schema::hasColumn('vehiculos', 'seguro_incluido')) {
                $table->boolean('seguro_incluido')->after('kilometraje')->default(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            $table->dropColumn(['año', 'kilometraje', 'seguro_incluido']);
        });
    }
};
