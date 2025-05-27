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
    
    /**
     * Calcula el costo de reparación para un vehículo amortizado
     * (Recupera el valor original del vehículo)
     *
     * @return float
     */
    public function calcularCostoReparacion()
    {
        // Si el vehículo no está amortizado, no hay costo de reparación
        if (!$this->estaAmortizado()) {
            return 0;
        }
        
        // El costo de reparación es un porcentaje del valor original del vehículo
        $valor_original = $this->precio_dia * 200; // Estimación: 200 días de alquiler como valor base
        
        // La reparación cuesta entre un 20% y un 40% del valor original, dependiendo de la antigüedad
        $antigüedad = now()->year - $this->año;
        $porcentaje_costo = min(0.4, 0.2 + ($antigüedad * 0.05)); // Aumenta 5% por cada año, máximo 40%
        
        return round($valor_original * $porcentaje_costo, 2);
    }
    
    /**
     * Registra la reparación de un vehículo amortizado
     *
     * @param string $descripcion Descripción de la reparación
     * @return bool
     */
    public function repararVehiculoAmortizado($descripcion = 'Reparación de vehículo amortizado')
    {
        // Solo se puede reparar si está amortizado
        if (!$this->estaAmortizado()) {
            return false;
        }
        
        // Actualizar fechas de mantenimiento
        $this->estado = 'activo'; // Cambiar estado a activo
        $this->ultima_fecha_mantenimiento = now();
        $this->proxima_fecha_mantenimiento = now()->addMonths(6); // Próximo mantenimiento en 6 meses
        
        // Guardar cambios
        return $this->save();
    }
    

}
