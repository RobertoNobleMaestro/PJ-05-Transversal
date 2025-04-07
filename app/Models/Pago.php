<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    use HasFactory;

    protected $table = 'pago';
    protected $primaryKey = 'id_pago';

    protected $fillable = [
        'fecha_pago',
        'monto',
        'estado',
        'id_usuario',
        'id_reservas'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reservas', 'id_reservas');
    }

    public function metodosPago()
    {
        return $this->hasMany(MetodoPago::class, 'id_pago', 'id_pago');
    }
}
