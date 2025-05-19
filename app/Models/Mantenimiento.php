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
}