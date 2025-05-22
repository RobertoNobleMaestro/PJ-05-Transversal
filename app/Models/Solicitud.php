<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_usuario_solicitado',
        'latitud_solicitante',
        'longitud_solicitante',
        'chofer_id',
        'estado_solicitud',
        'latitud_destino',
        'longitud_destino',
        'id_lugar',
        'precio'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario_solicitado');
    }

    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'chofer_id');
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
