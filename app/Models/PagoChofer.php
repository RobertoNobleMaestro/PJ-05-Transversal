<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PagoChofer extends Model
{
    use HasFactory;

    protected $table = 'pagos_choferes';

    protected $fillable = [
        'chofer_id',
        'solicitud_id',
        'importe_total',
        'importe_empresa',
        'importe_chofer',
        'estado_pago',
        'fecha_pago'
    ];

    public function chofer()
    {
        return $this->belongsTo(Chofer::class, 'chofer_id');
    }

    public function solicitud()
    {
        return $this->belongsTo(Solicitud::class, 'solicitud_id');
    }
} 