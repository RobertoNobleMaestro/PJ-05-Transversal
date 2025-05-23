<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    use HasFactory;

    protected $table = 'facturas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'mantenimiento_id',
        'taller_id',
        'total',
        'detalle',
    ];

    /**
     * Relación con Mantenimiento
     */
    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id', 'id');
    }

    /**
     * Relación con Taller
     */
    public function taller()
    {
        return $this->belongsTo(Taller::class, 'taller_id', 'id');
    }
}
