<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Valoracion;
use App\Models\VehiculosReservas;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'año',
        'matricula',
        'precio_dia',
        'disponibilidad',
        'kilometraje',
        'seguro_incluido',
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
        return $this->hasOne(Caracteristica::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'vehiculos_reservas', 'id_vehiculos', 'id_reservas');
    }
    public function vehiculosReservas()
    {
        return $this->hasMany(VehiculosReservas::class, 'id_vehiculos', 'id_vehiculos');
    }
    
    public function valoraciones()
    {
        return $this->hasManyThrough(
            Valoracion::class,              // Modelo final
            VehiculosReservas::class,       // Modelo intermedio
            'id_vehiculos',                 // FK en VehiculosReservas que apunta a este modelo (Vehiculo)
            'id_reservas',                  // FK en Valoracion que apunta a Reservas
            'id_vehiculos',                 // Local Key en este modelo (Vehiculo)
            'id_reservas'                   // Local Key en VehiculosReservas que apunta a Valoraciones
        );
    }

}