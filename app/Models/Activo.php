<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    use HasFactory;

    protected $table = 'activos';
    protected $primaryKey = 'id_activo';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria', // circulante, fijo, diferido, etc.
        'valor',
        'fecha_registro',
        'fecha_actualizacion',
        'id_lugar' // sede a la que pertenece el activo
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_actualizacion' => 'datetime',
        'valor' => 'decimal:2'
    ];

    /**
     * Obtiene la sede a la que pertenece este activo
     */
    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
}
