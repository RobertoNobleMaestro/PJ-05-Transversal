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
     * @return array Contiene el salario proporcional, días trabajados, días del mes y porcentaje
     */
    public function calcularSalarioProporcional($fecha = null)
    {
        $hoy = \Carbon\Carbon::today();
        // Use today if no specific date is given, otherwise parse the provided date
        $fechaReferencia = $fecha ? \Carbon\Carbon::parse($fecha)->startOfDay() : $hoy;

        $primerDiaMesReferencia = $fechaReferencia->copy()->startOfMonth();
        $ultimoDiaMesReferencia = $fechaReferencia->copy()->endOfMonth();
        
        $fechaContratacion = \Carbon\Carbon::parse($this->hiredate)->startOfDay();

        // --- Calculate potential total working days in the reference month (6-day week logic) ---
        // Start counting for the month from hire date if hired within this month, else from start of month.
        $inicioCalculoMesCompleto = $fechaContratacion->gt($primerDiaMesReferencia) ? $fechaContratacion->copy() : $primerDiaMesReferencia->copy();
        $diasCalendarioMesCompleto = 0;
        if ($inicioCalculoMesCompleto->lte($ultimoDiaMesReferencia)) {
            $diasCalendarioMesCompleto = $inicioCalculoMesCompleto->diffInDays($ultimoDiaMesReferencia) + 1;
        }
        
        $fullWeeksMesCompleto = floor($diasCalendarioMesCompleto / 7);
        $remainingDaysMesCompleto = $diasCalendarioMesCompleto % 7;
        $diasLaborablesPotencialesMes = ($fullWeeksMesCompleto * 6) + min($remainingDaysMesCompleto, 6);
        if ($diasLaborablesPotencialesMes < 0) $diasLaborablesPotencialesMes = 0;


        // --- Calculate actual worked days up to $fechaReferencia (today if $fecha is null) using 6-day week logic ---
        // Effective start date for this month's calculation
        $fechaInicioCalculoReal = $fechaContratacion->gt($primerDiaMesReferencia) ? $fechaContratacion->copy() : $primerDiaMesReferencia->copy();
        
        // End date for calculation is $fechaReferencia, but not beyond the month's end or before start.
        $fechaFinCalculoReal = $fechaReferencia->copy();
        if ($fechaFinCalculoReal->gt($ultimoDiaMesReferencia)) {
            $fechaFinCalculoReal = $ultimoDiaMesReferencia->copy();
        }
        if ($fechaFinCalculoReal->lt($fechaInicioCalculoReal)) { // e.g. if $fechaReferencia is before hire date in the same month
            $fechaFinCalculoReal = $fechaInicioCalculoReal->copy();
        }

        $diasTrabajadosHastaFechaRefCalculados = 0;
        if ($fechaInicioCalculoReal->lte($fechaFinCalculoReal)) {
            // Ensure we don't count days if hire date is after $fechaFinCalculoReal
            if ($this->hiredate > $fechaFinCalculoReal->toDateString()){
                 $diasCalendarioHastaFechaRef = 0;
            } else {
                 $diasCalendarioHastaFechaRef = $fechaInicioCalculoReal->diffInDays($fechaFinCalculoReal) + 1;
            }
            
            $fullWeeksHastaFechaRef = floor($diasCalendarioHastaFechaRef / 7);
            $remainingDaysHastaFechaRef = $diasCalendarioHastaFechaRef % 7;
            $diasTrabajadosHastaFechaRefCalculados = ($fullWeeksHastaFechaRef * 6) + min($remainingDaysHastaFechaRef, 6);
        }
        if ($diasTrabajadosHastaFechaRefCalculados < 0) $diasTrabajadosHastaFechaRefCalculados = 0;

        // If hire date is after the reference date (e.g. hired later this month than 'today'), worked days are 0.
        if ($fechaContratacion->gt($fechaReferencia)) {
            $diasTrabajadosHastaFechaRefCalculados = 0;
        }

        // --- Calculate proportional salary ---
        $salarioProporcional = 0;
        if ($diasLaborablesPotencialesMes > 0 && $this->salario > 0) {
            $salarioProporcional = ($this->salario / $diasLaborablesPotencialesMes) * $diasTrabajadosHastaFechaRefCalculados;
        }
        // Ensure salary isn't more than base if they worked all potential days or more due to calculation nuances
        $salarioProporcional = min($salarioProporcional, floatval($this->salario)); 
        if ($diasTrabajadosHastaFechaRefCalculados <= 0) $salarioProporcional = 0;

        $porcentaje = $diasLaborablesPotencialesMes > 0 ? round(($diasTrabajadosHastaFechaRefCalculados / $diasLaborablesPotencialesMes) * 100, 2) : 0;

        return [
            'salarioProporcional' => round($salarioProporcional, 2),
            'diasTrabajados' => $diasTrabajadosHastaFechaRefCalculados, 
            'diasLaborablesPotencialesEnMes' => $diasLaborablesPotencialesMes, 
            'porcentaje' => $porcentaje,
            'fechaCalculoHasta' => $fechaReferencia->toDateString()
        ];
    }
    
    /**
     * Calcula el salario proporcional para empleados dados de baja
     * @return array Contiene el salario proporcional, días trabajados, días del mes y porcentaje
     */
    public function calcularSalarioBaja()
    {
        try {
            // Si aún no está dado de baja o no tiene días trabajados registrados, retornar 0
            if ($this->estado !== 'baja' || !isset($this->dias_trabajados) || $this->dias_trabajados <= 0) {
                return [
                    'salario' => 0,
                    'diasTrabajados' => 0,
                    'diasEnMes' => now()->endOfMonth()->day,
                    'porcentaje' => 0
                ];
            }
            
            $fechaActual = now();
            $primerDiaMes = $fechaActual->copy()->startOfMonth();
            $diasEnMes = $fechaActual->copy()->endOfMonth()->day;
            
            // Usar diasTrabajados directamente en lugar de updated_at
            $diasTrabajados = $this->dias_trabajados;
            
            // Asegurar que tenemos un salario válido
            $salario = floatval($this->salario);
            if ($salario <= 0) {
                return [
                    'salario' => 0,
                    'diasTrabajados' => 0,
                    'diasEnMes' => $diasEnMes,
                    'porcentaje' => 0
                ];
            }
            
            // Calcular el salario proporcional
            $salarioProporcional = ($salario / $diasEnMes) * $diasTrabajados;
            $porcentaje = round(($diasTrabajados / $diasEnMes) * 100, 2);
            
            return [
                'salario' => $salarioProporcional,
                'diasTrabajados' => $diasTrabajados,
                'diasEnMes' => $diasEnMes,
                'porcentaje' => $porcentaje
            ];
        } catch (\Exception $e) {
            \Log::error('Error al calcular el salario de baja: ' . $e->getMessage());
            return [
                'salario' => 0,
                'diasTrabajados' => 0,
                'diasEnMes' => now()->endOfMonth()->day,
                'porcentaje' => 0
            ];
        }
    }
}
