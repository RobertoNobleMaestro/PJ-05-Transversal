<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pieza extends Model
{
    use HasFactory;
    protected $table = 'piezas';
    protected $fillable = [
        'nombre',
        'tipo_vehiculo',
        'precio',
    ];

    public function averias()
    {
        return $this->belongsToMany(Averia::class, 'averia_pieza', 'pieza_id', 'averia_id')->withPivot('cantidad')->withTimestamps();
    }
}
