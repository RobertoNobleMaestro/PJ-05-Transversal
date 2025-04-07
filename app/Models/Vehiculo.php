<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'matricula',
        'precio_dia',
        'disponibilidad',
        'id_lugar',
        'id_tipo'
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo', 'id_tipo');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenVehiculo::class, 'id_vehiculo', 'id_vehiculos');
    }

    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'vehiculos_reservas', 'id_vehiculos', 'id_reservas');
    }
}
