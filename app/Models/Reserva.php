<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';
    protected $primaryKey = 'id_reservas';

    protected $fillable = [
        'fecha_reserva',
        'total_precio',
        'estado',
        'id_lugar',
        'id_usuario',
        'referencia_pago'
    ];

    protected $casts = [
        'fecha_reserva' => 'date',
        'total_precio' => 'decimal:2'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    public function vehiculos()
    {
        return $this->belongsToMany(Vehiculo::class, 'vehiculos_reservas', 'id_reservas', 'id_vehiculos')
                    ->withPivot('fecha_ini', 'fecha_final', 'precio_unitario');
    }
    public function vehiculo()
    {
        return $this->belongsToMany(Vehiculo::class, 'vehiculos_reservas', 'id_reservas', 'id_vehiculos')
                    ->withPivot('fecha_ini', 'fecha_final', 'precio_unitario');
    }

    public function vehiculosReservas()
    {
        return $this->hasMany(VehiculosReservas::class, 'id_reservas', 'id_reservas');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_reservas', 'id_reservas');
    }

    public function valoracion()
    {
        return $this->hasOne(Valoracion::class, 'id_reservas', 'id_reservas');
    }
}