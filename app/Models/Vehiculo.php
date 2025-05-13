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
        'año',
        'matricula',
        'precio_dia',
        'disponibilidad',
        'kilometraje',
        'id_lugar',
        'id_tipo',
        'parking_id'
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo', 'id_tipo');
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id', 'id');
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
        return $this->belongsToMany(Reserva::class, 'vehiculos_reservas', 'id_vehiculos', 'id_reservas')
                    ->withPivot('fecha_ini', 'fecha_final')
                    ->withTimestamps();
    }

    public function vehiculosReservas()
    {
        return $this->hasMany(VehiculosReservas::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function valoraciones()
    {
        return $this->hasManyThrough(
            Valoracion::class,
            VehiculosReservas::class,
            'id_vehiculos',
            'id_reservas',
            'id_vehiculos',
            'id_reservas'
        );
    }
}
