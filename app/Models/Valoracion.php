<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Valoracion extends Model
{
    use HasFactory;

    protected $table = 'valoraciones';
    protected $primaryKey = 'id_valoraciones';

    protected $fillable = [
        'valoracion',
        'comentario',
        'fecha_valoracion',
        'id_reservas',
        'id_usuario'
    ];

    protected $casts = [
        'fecha_valoracion' => 'datetime',
        'valoracion' => 'integer'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function reserva()
    {
        return $this->belongsTo(Reserva::class, 'id_reservas', 'id_reservas');
    }
}
