<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'grupo_usuario', 'grupo_id', 'id_usuario')
            ->withTimestamps();
    }

    // Relación con la tabla mensajes 
      public function mensajes(): HasMany
    {
        return $this->hasMany(Message::class, 'grupo_id', 'id');
    }
}
