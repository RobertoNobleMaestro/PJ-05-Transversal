<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    // Nombre de la tabla
    protected $table = "grupos";

    // Campos 
      protected $fillable = [
        'nombre',
        'fecha_creacion',
        'imagen_grupo',
    ];

    // Relación con la tabla usuarios 
       public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'grupo_id', 'id');
    }

    // Relación con la tabla mensajes 
      public function mensajes(): HasMany
    {
        return $this->hasMany(Message::class, 'grupo_id', 'id');
    }
}
