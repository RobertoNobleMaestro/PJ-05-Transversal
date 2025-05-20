<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Averia extends Model
{
    use HasFactory;
    protected $table = 'averias';
    protected $fillable = [
        'vehiculo_id',
        'descripcion',
        'fecha',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class, 'vehiculo_id', 'id_vehiculos');
    }

    public function piezas()
    {
        return $this->belongsToMany(Pieza::class, 'averia_pieza', 'averia_id', 'pieza_id')->withPivot('cantidad')->withTimestamps();
    }
}
