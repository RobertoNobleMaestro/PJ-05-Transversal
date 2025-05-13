<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehiculosReservas extends Model
{
    protected $table = 'vehiculos_reservas';
    protected $primaryKey = 'id_vehiculos_reservas';

    public $timestamps = true;

    protected $fillable = [
        'fecha_ini',
        'fecha_final',
        'id_reservas',
        'id_vehiculos'
    ];

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reservas', 'id_reservas');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculos', 'id_vehiculos');
    }
}
