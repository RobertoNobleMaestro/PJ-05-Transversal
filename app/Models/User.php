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

    public function grupo()
{
    return $this->belongsTo(Grupo::class, 'grupo_id', 'id');
}
    
    /**
     * Envía la notificación de restablecimiento de contraseña al usuario.
     * 
     * Este método es llamado automáticamente por Laravel cuando un usuario
     * solicita restablecer su contraseña desde la página de recuperación.
     * 
     * El flujo completo es el siguiente:
     * 1. El usuario solicita recuperar su contraseña en la página forgot-password
     * 2. Laravel verifica que el email existe en la base de datos
     * 3. Si existe, genera un token único y lo guarda en la tabla password_reset_tokens
     * 4. Llama a este método pasando el token generado
     * 5. Este método instancia la clase ResetPasswordNotification con el token
     * 6. La notificación personalizada configura el correo en español
     * 7. El correo se envía al usuario mediante SMTP (configurado en .env)
     * 
     * La personalización del correo (asunto, contenido, botones) se realiza
     * en la clase ResetPasswordNotification.
     *
     * @param  string  $token  Token único generado por Laravel para la recuperación
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // Notifica al usuario usando nuestra clase personalizada que configura
        // el correo en español con el estilo de Carflow
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
