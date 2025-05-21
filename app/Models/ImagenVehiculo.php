<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagenVehiculo extends Model
{
    use HasFactory;

    protected $table = 'imagen_vehiculo';
    protected $primaryKey = 'id_imagen_vehiculo';

    protected $fillable = [
        'nombre_archivo',
        'id_vehiculo'
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'id_vehiculo', 'id_vehiculos');
    }
}
