<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudChofer extends Model
{
    protected $table = 'solicitudes_chofer';
    protected $primaryKey = 'id_solicitud';
    public $timestamps = true;

    protected $fillable = [
        'id_usuario',
        'estado',
        'leida',
        'fecha_solicitud'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }
} 