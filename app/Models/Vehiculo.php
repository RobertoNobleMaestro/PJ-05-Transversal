<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
{
    // ...
    public function getUltimaMantFormateadaAttribute()
    {
        return $this->ultima_fecha_mantenimiento
            ? \Carbon\Carbon::parse($this->ultima_fecha_mantenimiento)->format('d/m/Y')
            : null;
    }

    public function getProximaMantFormateadaAttribute()
    {
        return $this->proxima_fecha_mantenimiento
            ? \Carbon\Carbon::parse($this->proxima_fecha_mantenimiento)->format('d/m/Y')
            : null;
    }

    use HasFactory;

    protected $table = 'vehiculos';
    protected $primaryKey = 'id_vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'año',
        'matricula',
        'precio_dia',
        'disponibilidad',
        'kilometraje',
        'id_lugar',
        'id_tipo',
        'parking_id',
        'ultima_fecha_mantenimiento',    
        'proxima_fecha_mantenimiento',   
    ];

    protected $casts = [
        'ultima_fecha_mantenimiento' => 'date',   
        'proxima_fecha_mantenimiento' => 'date',  
    ];

    public function lugar()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }

    public function tipo()
    {
        return $this->belongsTo(Tipo::class, 'id_tipo', 'id_tipo');
    }

    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(ImagenVehiculo::class, 'id_vehiculo', 'id_vehiculos');
    }

    public function caracteristicas()
    {
        return $this->hasOne(Caracteristica::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function reservas()
    {
        return $this->belongsToMany(Reserva::class, 'vehiculos_reservas', 'id_vehiculos', 'id_reservas')
                    ->withPivot('fecha_ini', 'fecha_final')
                    ->withTimestamps();
    }

    public function vehiculosReservas()
    {
        return $this->hasMany(VehiculosReservas::class, 'id_vehiculos', 'id_vehiculos');
    }

    public function valoraciones()
    {
        return $this->hasManyThrough(
            Valoracion::class,
            VehiculosReservas::class,
            'id_vehiculos',
            'id_reservas',
            'id_vehiculos',
            'id_reservas'
        );
    }

    // formato de ultima_fecha_mantenimiento
    public function getUltimaFechaMantenimientoFormattedAttribute()
    {
        return $this->ultima_fecha_mantenimiento
            ? $this->ultima_fecha_mantenimiento->format('d/m/Y')
            : 'No disponible';
    }

    //formato de proxima_fecha_mantenimiento
    public function getProximaFechaMantenimientoFormattedAttribute()
    {
        return $this->proxima_fecha_mantenimiento
            ? $this->proxima_fecha_mantenimiento->format('d/m/Y')
            : 'No disponible';
    }
    
    /**
     * Calcula el valor actual del vehículo teniendo en cuenta la depreciación
     * (20% por año, 0 después de 5 años)
     *
     * @param int|null $año_referencia Año para el cálculo (por defecto, el año actual)
     * @return float
     */
    public function calcularValorActual($año_referencia = null)
    {
        if ($año_referencia === null) {
            $año_referencia = now()->year;
        }
        
        // Años transcurridos desde la fabricación
        $años_transcurridos = max(0, $año_referencia - $this->año);
        
        // Si han pasado 5 años o más, el valor es 0
        if ($años_transcurridos >= 5) {
            return 0;
        }
        
        // Usar valor por defecto si el precio es nulo o cero
        $precio_base = $this->precio_dia * 200; // Estimación: 200 días de alquiler como valor base
        
        // Calcular depreciación (20% por año)
        $porcentaje_valor_restante = 1 - (0.2 * $años_transcurridos);
        return round($precio_base * $porcentaje_valor_restante, 2);
    }
    
    /**
     * Determina si el vehículo está amortizado (valor 0)
     *
     * @param int|null $año_referencia Año para el cálculo
     * @return bool
     */
    public function estaAmortizado($año_referencia = null)
    {
        return $this->calcularValorActual($año_referencia) <= 0;
    }
}
