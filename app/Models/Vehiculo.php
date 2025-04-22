<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'año',
        'precio_dia',
        'disponibilidad',
        'kilometraje',
        'seguro_incluido',
        'id_lugar',
        'id_tipo'
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo', 'id_tipo');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenVehiculo::class, 'id_vehiculo', 'id_vehiculos');
    }

    public function caracteristicas()
    {
        return $this->hasMany(Caracteristica::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'vehiculos_reservas', 'id_vehiculos', 'id_reservas');
    }

    /**
     * Obtiene la valoración media del vehículo
     * 
     * @return float|null
     */
    public function getValoracionMedia()
    {
        try {
            // Verificar primero si existen las tablas necesarias
            if (!Schema::hasTable('valoraciones') || 
                !Schema::hasTable('reservas') || 
                !Schema::hasTable('vehiculos_reservas')) {
                return null;
            }
            
            // Obtener las valoraciones relacionadas con las reservas de este vehículo
            $valoraciones = DB::table('valoraciones')
                ->join('reservas', 'valoraciones.id_reservas', '=', 'reservas.id_reservas')
                ->join('vehiculos_reservas', 'reservas.id_reservas', '=', 'vehiculos_reservas.id_reservas')
                ->where('vehiculos_reservas.id_vehiculos', $this->id_vehiculos)
                ->pluck('valoraciones.puntuacion');
            
            // Si no hay valoraciones, devuelve null
            if (!$valoraciones || $valoraciones->isEmpty()) {
                return null;
            }
            
            // Calcular la media
            return round($valoraciones->avg(), 1);
        } catch (\Exception $e) {
            // En caso de cualquier error, devolver null
            return null;
        }
    }
}
