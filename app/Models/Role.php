<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_roles';
    
    protected $fillable = [
        'nombre_rol',
    ];

    public function usuarios()
    {
        return $this->hasMany(User::class, 'id_roles', 'id_roles');
    }
    
    /**
     * Get the formatted name of the role
     *
     * @return string
     */
    public function getFormattedNameAttribute()
    {
        if ($this->nombre_rol === 'admin_financiero') {
            return 'Admin Financiero';
        }
        
        // First letter capitalized for other roles
        return ucfirst($this->nombre_rol);
    }
}

