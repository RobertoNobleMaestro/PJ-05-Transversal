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
            // Comprobar si la columna precio_dia ya existe
            if (!Schema::hasColumn('vehiculos', 'precio_dia')) {
                $table->decimal('precio_dia', 8, 2)->after('seguro_incluido')->nullable();
            }
            
            // Agregar disponibilidad solo si no existe
            if (!Schema::hasColumn('vehiculos', 'disponibilidad')) {
                $table->boolean('disponibilidad')->after('precio_dia')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehiculos', function (Blueprint $table) {
            if (Schema::hasColumn('vehiculos', 'disponibilidad')) {
                $table->dropColumn('disponibilidad');
            }
            
            // Solo eliminar precio_dia si existe y se creó en esta migración
            // Generalmente no deberías eliminar esta columna si ya existía antes
            if (Schema::hasColumn('vehiculos', 'precio_dia') && !Schema::hasColumn('vehiculos', 'created_at')) {
                $table->dropColumn('precio_dia');
            }
        });
    }
};
