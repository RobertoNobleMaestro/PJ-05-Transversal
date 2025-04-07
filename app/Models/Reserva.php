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
        'id_usuario'
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
        return $this->belongsToMany(Vehiculo::class, 'vehiculos_reservas', 'id_reservas', 'id_vehiculos');
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
