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
        Schema::create('pagos_choferes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chofer_id')->constrained('choferes');
            $table->foreignId('solicitud_id')->constrained('solicitudes');
            $table->decimal('importe_total', 10, 2);
            $table->decimal('importe_empresa', 10, 2);
            $table->decimal('importe_chofer', 10, 2);
            $table->enum('estado_pago', ['pendiente', 'pagado', 'cancelado']);
            $table->date('fecha_pago');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos_choferes');
    }
}; 