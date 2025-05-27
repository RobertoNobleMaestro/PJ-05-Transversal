<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Types\Type;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero renombramos la columna dia_cobro a hiredate
        Schema::table('asalariados', function (Blueprint $table) {
            // Verificamos si existe la columna dia_cobro antes de intentar renombrarla
            if (Schema::hasColumn('asalariados', 'dia_cobro')) {
                $table->renameColumn('dia_cobro', 'hiredate');
                // Comentamos el cambio de tipo porque puede causar problemas
                // $table->date('hiredate')->nullable()->change();
            } else {
                // Si no existe, simplemente la creamos
                $table->date('hiredate')->nullable();
            }
        });
        
        // Luego agregamos los nuevos campos
        Schema::table('asalariados', function (Blueprint $table) {
            // Agregar campo para estado (alta o baja)
            if (!Schema::hasColumn('asalariados', 'estado')) {
                $table->enum('estado', ['alta', 'baja'])->default('alta');
            }
            
            // Agregar campo para contar días trabajados
            if (!Schema::hasColumn('asalariados', 'dias_trabajados')) {
                $table->integer('dias_trabajados')->default(0);
            }
            
            // Agregar referencia directa al lugar (antes se refería indirectamente a través del parking)
            if (!Schema::hasColumn('asalariados', 'id_lugar')) {
                $table->unsignedBigInteger('id_lugar')->nullable();
                $table->foreign('id_lugar')->references('id_lugar')->on('lugares')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asalariados', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['id_lugar']);
            
            // Eliminar las columnas añadidas
            $table->dropColumn(['estado', 'dias_trabajados', 'id_lugar']);
            
            // Renombrar hiredate a dia_cobro
            if (Schema::hasColumn('asalariados', 'hiredate')) {
                $table->renameColumn('hiredate', 'dia_cobro');
                // Cambiar el tipo a integer si era necesario
                $table->integer('dia_cobro')->nullable()->change();
            }
        });
    }
};
