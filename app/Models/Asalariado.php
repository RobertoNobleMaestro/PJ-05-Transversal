<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asalariado extends Model
{
    use HasFactory;

    protected $table = 'asalariados';
    
    protected $fillable = [
        'id_usuario',
        'salario',
        'dia_cobro',
        'parking_id',
        'hiredate',
        'estado',
        'dias_trabajados',
        'id_lugar',
    ];
    
    protected $dates = [
        'hiredate',
        'created_at',
        'updated_at'
    ];

    /**
     * Obtiene el usuario asociado a este asalariado
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Obtiene el parking asignado a este asalariado
     */
    public function parking()
    {
        return $this->belongsTo(Parking::class, 'parking_id', 'id');
    }

    /**
     * Obtiene la sede (lugar) directamente
     */
    public function sede()
    {
        return $this->belongsTo(Lugar::class, 'id_lugar', 'id_lugar');
    }
    
    /**
     * Calcula el salario proporcional basado en la fecha de contratación
     * @param string $fecha Fecha para la que calcular el salario (formato Y-m-d)
     * @return float Salario proporcional
     */
    public function calcularSalarioProporcional($fecha = null)
    {
        if (!$fecha) {
            $fecha = now()->format('Y-m-d');
        }
        
        $fechaActual = \Carbon\Carbon::parse($fecha);
        $primerDiaMes = \Carbon\Carbon::parse($fecha)->startOfMonth();
        $ultimoDiaMes = \Carbon\Carbon::parse($fecha)->endOfMonth();
        $diasEnMes = $ultimoDiaMes->day;
        
        // Asegurar que hiredate es un objeto Carbon
        $fechaContratacion = \Carbon\Carbon::parse($this->hiredate);
        
        // Si fue contratado antes del mes actual, recibe el salario completo
        if ($fechaContratacion->lt($primerDiaMes)) {
            return $this->salario;
        }
        
        // Si fue contratado durante el mes actual, calcular la parte proporcional
        if ($fechaContratacion->between($primerDiaMes, $ultimoDiaMes)) {
            $diasTrabajados = $diasEnMes - $fechaContratacion->day + 1;
            return ($this->salario / $diasEnMes) * $diasTrabajados;
        }
        
        // Si fue contratado después del último día del mes, no recibe salario
        return 0;
    }
    
    /**
     * Calcula el salario proporcional para empleados dados de baja
     * @return float Salario proporcional hasta la fecha de baja
     */
    public function calcularSalarioBaja()
    {
        try {
            // Si aún no está dado de baja o no tiene días trabajados registrados, retornar 0
            if ($this->estado !== 'baja' || !isset($this->dias_trabajados) || $this->dias_trabajados <= 0) {
                return 0;
            }
            
            $fechaActual = now();
            $primerDiaMes = $fechaActual->copy()->startOfMonth();
            $diasEnMes = $fechaActual->copy()->endOfMonth()->day;
            
            // Usar diasTrabajados directamente en lugar de updated_at
            $diasTrabajados = $this->dias_trabajados;
            
            // Asegurar que tenemos un salario válido
            $salario = floatval($this->salario);
            if ($salario <= 0) {
                return 0;
            }
            
            // Calcular el salario proporcional
            return ($salario / $diasEnMes) * $diasTrabajados;
        } catch (\Exception $e) {
            \Log::error('Error al calcular el salario de baja: ' . $e->getMessage());
            return 0; // En caso de error, devolver 0
        }
    }
}
