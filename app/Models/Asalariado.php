<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asalariado extends Model
{
    use HasFactory;

    protected $table = 'asalariados';
    
    protected $fillable = [
        'id_usuario',
        'salario',
        'dia_cobro',
        'parking_id',
    ];

    /**
     * Obtiene el usuario asociado a este asalariado
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Obtiene el parking asignado a este asalariado
     */
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id', 'id');
    }

    /**
     * Obtiene la sede (lugar) a través del parking asignado
     */
    public function sede()
    {
        return $this->hasOneThrough(
            Lugar::class, 
            Parking::class,
            'id',  // Clave en parkings
            'id_lugar', // Clave en lugares
            'parking_id', // Clave en asalariados
            'id_lugar' // Clave en parkings
        );
    }
}
