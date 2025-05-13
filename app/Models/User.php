<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_usuario';

    protected $fillable = [
        'nombre',
        'email',
        'dni',
        'telefono',
        'fecha_nacimiento',
        'foto_perfil',
        'direccion',
        'licencia_conducir',
        'id_roles',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_roles', 'id_roles');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'id_usuario');
    }

    public function valoraciones()
    {
        return $this->hasMany(Valoracion::class, 'id_usuario', 'id_usuario');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'id_usuario', 'id_usuario');
    }

    public function parkings()
    {
        return $this->hasMany(Parking::class, 'id_usuario', 'id_usuario');
    }

    public function hasRole($roleName)
    {
        return $this->role && $this->role->nombre_rol === $roleName;
    }
}
