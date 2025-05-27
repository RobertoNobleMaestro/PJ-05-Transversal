<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GastoTaller extends Model
{
    use HasFactory;

    protected $table = 'gastos_taller';
    protected $primaryKey = 'id';

    protected $fillable = [
        'pieza_id',
        'cantidad',
        'precio_pieza',
        'mantenimiento_id',
        'averia_id',
        'factura_id',
    ];

    public function pieza()
    {
        return $this->belongsTo(Pieza::class, 'pieza_id');
    }

    public function mantenimiento()
    {
        return $this->belongsTo(Mantenimiento::class, 'mantenimiento_id');
    }

    public function averia()
    {
        return $this->belongsTo(Averia::class, 'averia_id');
    }

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'factura_id');
    }
} 