<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caracteristica extends Model
{
    use HasFactory;

    protected $table = 'caracteristicas';
    protected $primaryKey = 'id_caracteristicas';

    protected $fillable = [
        'techo',
        'transmision',
        'num_puertas',
        'etiqueta_medioambiental',
        'aire_acondicionado',
        'capacidad_maletero',
        'id_vehiculos'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculos', 'id_vehiculos');
    }
}
