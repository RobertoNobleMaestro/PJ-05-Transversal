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
        'id_lugar',
        'metros_cuadrados',
        'precio_metro_cuadrado'
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
     * Calcula el valor total del parking usando los valores almacenados en la base de datos
     * 
     * @param int|null $año_referencia Año para el cálculo (por defecto, el año actual)
     * @return float
     */
    public function calcularValorTotal($año_referencia = null)
    {
        if ($año_referencia === null) {
            $año_referencia = now()->year;
        }
        
        // Si tenemos valores de metros cuadrados y precio por metro, los usamos
        if ($this->metros_cuadrados > 0 && $this->precio_metro_cuadrado > 0) {
            return $this->metros_cuadrados * $this->precio_metro_cuadrado;
        }
        
        // Fallback al método antiguo si no hay datos en la base de datos
        $metros_cuadrados = $this->plazas * 25; // 25m² por plaza
        $precio_por_metro = 1000; // 1.000€ por metro cuadrado por defecto
        
        return $metros_cuadrados * $precio_por_metro;
    }
}


