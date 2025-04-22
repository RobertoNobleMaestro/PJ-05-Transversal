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
}

