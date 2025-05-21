<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'gestor_id',
        'message',
        'sender_type',
        'receiver_id',
        'grupo_id',   // Para mensajes de grupo
        'read_at',    // Para marcar como leído
    ];
    
    protected $casts = [
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Obtiene el remitente del mensaje
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        // Este método siempre devuelve una relación con User independientemente del tipo
        // Utiliza el campo user_id por defecto, compatible con mensajes de grupo
        return $this->belongsTo(User::class, 'user_id', 'id_usuario');
    }
    
    /**
     * Obtiene el usuario que envió el mensaje
     * 
     * @return \App\Models\User|null
     */
    public function getSenderAttribute()
    {
        // Este método devuelve directamente el objeto User
        return User::find($this->user_id);
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'grupo_id', 'id');
    }
}
