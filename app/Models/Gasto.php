<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Gasto extends Model
{
    use HasFactory;
    
    protected $table = 'gastos';
    
    protected $fillable = [
        'concepto',
        'descripcion',
        'tipo',
        'importe',
        'fecha',
        'id_vehiculo',
        'id_parking',
        'id_asalariado',
        'id_mantenimiento'
    ];
    
    protected $casts = [
        'fecha' => 'date',
        'importe' => 'decimal:2'
    ];
    
    /**
     * Relación con el vehículo asociado al gasto
     */
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculos');
    }
    
    /**
     * Relación con el parking asociado al gasto
     */
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'id_parking', 'id');
    }
    
    /**
     * Relación con el asalariado asociado al gasto
     */
    public function asalariado()
    {
        return $this->belongsTo(Asalariado::class, 'id_asalariado');
    }
    
    /**
     * Relación con el mantenimiento asociado al gasto
     */
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'id_mantenimiento');
    }
    
    /**
     * Formatea la fecha para mostrar
     */
    public function getFechaFormateadaAttribute()
    {
        return $this->fecha ? $this->fecha->format('d/m/Y') : null;
    }
    
    /**
     * Calcula el total de gastos por tipo en un periodo
     */
    public static function totalPorTipo($tipo, $fechaDesde = null, $fechaHasta = null)
    {
        $query = self::where('tipo', $tipo);
        
        if ($fechaDesde) {
            $query->where('fecha', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha', '<=', $fechaHasta);
        }
        
        return $query->sum('importe');
    }
    
    /**
     * Obtener los gastos agrupados por tipo
     */
    public static function gastosPorTipo($fechaDesde = null, $fechaHasta = null)
    {
        $query = self::query();
        
        if ($fechaDesde) {
            $query->where('fecha', '>=', $fechaDesde);
        }
        
        if ($fechaHasta) {
            $query->where('fecha', '<=', $fechaHasta);
        }
        
        // Obtener los gastos agrupados por tipo
        $gastosPorTipo = [];
        
        foreach ($query->get() as $gasto) {
            if (!isset($gastosPorTipo[$gasto->tipo])) {
                $gastosPorTipo[$gasto->tipo] = 0;
            }
            
            $gastosPorTipo[$gasto->tipo] += $gasto->importe;
        }
        
        return $gastosPorTipo;
    }
}
