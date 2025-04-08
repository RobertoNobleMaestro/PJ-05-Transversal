<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tipo extends Model
{
    use HasFactory;

    protected $table = 'tipo';

    protected $primaryKey = 'id_tipo';

    protected $fillable = [
        'nombre_tipo',
        'descripcion'
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_tipo', 'id_tipo');
    }
}
