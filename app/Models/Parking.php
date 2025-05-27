<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{

    protected $table = 'parking'; // Nombre de la tabla
    protected $primaryKey = 'id';  // PK de la tabla

    // Campos de la tabla 
    protected $fillable = [
        'nombre', 
        'plazas',
        'latitud',
        'longitud',
        'id_usuario',
        'id_lugar'
    ];

    // Relación con Lugar
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    // Un parking tiene muchos vehículos
    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'parking_id', 'id');
    }
    
    /**
     * Calcula el valor total del parking
     * 
     * @param int|null $año_referencia Año para el cálculo (por defecto, el año actual)
     * @return float
     */
    public function calcularValorTotal($año_referencia = null)
    {
        if ($año_referencia === null) {
            $año_referencia = now()->year;
        }
        
        // Valor base por plaza de parking
        $valor_por_plaza = 25000; // 25.000€ por plaza
        
        // Metros cuadrados estimados (25m² por plaza)
        $plazas = $this->plazas > 0 ? $this->plazas : 100; // Si no hay plazas, asumimos 100
        
        // Valor total basado en número de plazas
        return $plazas * $valor_por_plaza;
    }
}


