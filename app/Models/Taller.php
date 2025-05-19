<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taller extends Model
{
    use HasFactory;

    protected $table = 'talleres';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'capacidad_hora'
    ];

    // Relación con las citas de mantenimiento
    public function mantenimientos()
    {
        return $this->hasMany(Mantenimiento::class, 'taller_id', 'id');
    }
}