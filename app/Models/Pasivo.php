<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasivo extends Model
{
    use HasFactory;

    protected $table = 'pasivos';
    protected $primaryKey = 'id_pasivo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria', // circulante, fijo, largo plazo, etc.
        'valor',
        'fecha_registro',
        'fecha_vencimiento',
        'fecha_actualizacion',
        'id_lugar' // sede a la que pertenece el pasivo
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'valor' => 'decimal:2'
    ];

    /**
     * Obtiene la sede a la que pertenece este pasivo
     */
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
}
