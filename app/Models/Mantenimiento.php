<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mantenimiento extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos';
    protected $primaryKey = 'id';

    protected $fillable = [
        'vehiculo_id',
        'taller_id',
        'fecha_programada',
        'hora_programada',
        'estado'
    ];

    protected $casts = [
        'fecha_programada' => 'date',
    ];

    // Relación con el vehículo
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id_vehiculos');
    }

    // Relación con el taller
    public function taller()
    {
        return $this->belongsTo(Taller::class, 'taller_id', 'id');
    }

    // Atributo para mostrar fecha y hora completas en un solo campo
    public function getFechaCompletaAttribute()
    {
        return $this->fecha_programada->format('Y-m-d') . ' ' . $this->hora_programada;
    }

    // Atributo para mostrar el nombre del vehículo (si existe)
    public function getVehiculoNombreAttribute()
    {
        return $this->vehiculo ? $this->vehiculo->modelo . ' - ' . $this->vehiculo->placa : 'N/A';
    }

    // Atributo para mostrar el nombre del taller (si existe)
    public function getTallerNombreAttribute()
    {
        return $this->taller ? $this->taller->nombre : 'N/A';
    }

    // Atributo para el color del estado (para la tabla)
    public function getColorEstadoAttribute()
    {
        return match ($this->estado) {
            'pendiente' => 'warning',
            'completado' => 'success',
            'cancelado' => 'danger',
            default => 'secondary',
        };
    }
}
