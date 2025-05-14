<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parking extends Model
{

    // protected $table = 'parking'; // Nombre de la tabla
    // protected $primaryKey = 'id';  // PK de la tabla

    // // Campos de la tabla 
    // protected $fillable = [
    //     'nombre', 
    //     'plazas',
    //     'latitud',
    //     'longitud',
    //     'id_usuario',
    //     'id_lugar'
    // ];

    // // Relación con Lugar
    // public function lugar()
    // {
    //     return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    // }

    // // Un parking tiene muchos vehículos
    // public function vehiculos()
    // {
    //     return $this->hasMany(Vehiculo::class, 'parking_id', 'id');
    // }
}


