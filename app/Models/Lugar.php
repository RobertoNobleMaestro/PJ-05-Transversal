<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory;

    protected $table = 'lugares';
    protected $primaryKey = 'id_lugar';

    protected $fillable = [
        'nombre_lugar',
        'direccion',
        'ciudad',
        'codigo_postal'
    ];

    public function vehiculos()
    {
        return $this->hasMany(Vehiculo::class, 'id_lugar', 'id_lugar');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_lugar', 'id_lugar');
    }
}
