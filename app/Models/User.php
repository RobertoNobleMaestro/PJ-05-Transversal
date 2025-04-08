<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'DNI',
        'telefono',
        'fecha_nacimiento',
        'foto_perfil',
        'direccion',
        'licencia_conducir',
        'id_roles',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    /**
     * Get the rol associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_roles', 'id_roles');
    }

    /**
     * Get the reservas associated with the user.
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the valoraciones associated with the user.
     */
    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Get the pagos associated with the user.
     */
    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_usuario', 'id_usuario');
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->nombre_rol === $roleName;
    }
}
