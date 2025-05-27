<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $table = 'solicitudes';

    protected $fillable = [
        'id_chofer',
        'id_cliente',
        'latitud_origen',
        'longitud_origen',
        'latitud_destino',
        'longitud_destino',
        'precio',
        'estado'
    ];

    public function cliente()
    {
        return $this->belongsTo(User::class, 'id_cliente');
    }

    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'id_chofer');
    }

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_solicitud');
    }

    public function pagoChofer()
    {
        return $this->hasOne(PagoChofer::class, 'solicitud_id');
    }
}
