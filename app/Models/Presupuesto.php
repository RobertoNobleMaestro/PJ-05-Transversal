<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presupuesto extends Model
{
    use HasFactory;
    
    protected $table = 'presupuestos';
    
    protected $fillable = [
        'id_lugar',
        'categoria',
        'monto',
        'gasto_real',
        'fecha_inicio',
        'fecha_fin',
        'periodo_tipo',
        'creado_por',
        'notas'
    ];
    
    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'monto' => 'decimal:2',
        'gasto_real' => 'decimal:2'
    ];
    
    /**
     * Obtiene el lugar asociado al presupuesto
     */
    public function lugar(): BelongsTo
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
    
    /**
     * Obtiene el usuario que creó el presupuesto
     */
    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por', 'id_usuario');
    }
    
    /**
     * Calcula el porcentaje de cumplimiento del presupuesto
     */
    public function calcularPorcentajeCumplimiento(): float
    {
        if (!$this->gasto_real || $this->monto == 0) {
            return 0;
        }
        
        return ($this->gasto_real / $this->monto) * 100;
    }
    
    /**
     * Determina el estado del presupuesto
     * @return string 'success', 'warning' o 'danger'
     */
    public function estado(): string
    {
        if (!$this->gasto_real) {
            return 'info'; // Sin datos aún
        }
        
        $diferencia = $this->monto - $this->gasto_real;
        
        if ($diferencia >= 0) {
            return 'success'; // Cumplido (gastos menores o iguales al presupuesto)
        } elseif ($diferencia > ($this->monto * -0.1)) {
            return 'warning'; // Alerta (sobrepasado pero menos del 10%)
        } else {
            return 'danger'; // Excedido (sobrepasado más del 10%)
        }
    }
}
