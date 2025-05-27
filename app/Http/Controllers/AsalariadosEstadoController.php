<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AsalariadosEstadoController extends Controller
{
    /**
     * Muestra la lista de asalariados dados de baja
     */
    public function inactivos(Request $request)
    {   
        // Verificar si el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect()->route('home')->with('error', 'No tienes permisos para acceder a esta sección');
        }
        
        // Obtener asalariados dados de baja realmente
        // No incluir aquellos con baja programada pendiente
        $asalariados = Asalariado::where('estado', 'baja')
            // Excluir asalariados que tienen una baja programada pendiente
            ->where(function($query) {
                $query->whereNull('estado_baja_programada')
                      ->orWhere('estado_baja_programada', '!=', 'pendiente');
            })
            ->with('usuario', 'usuario.role', 'parking', 'sede')
            ->get();
            
        return view('admin_financiero.asalariados_inactivos', [
            'asalariados' => $asalariados
        ]);
    }
    
    /**
     * Programa la baja de un asalariado para el día 1 del mes siguiente
     */
    public function desactivar($id)
    {
        // Verificar si el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return response()->json(['error' => 'No tienes permisos para esta acción'], 403);
        }
        
        try {
            // Verificar que el asalariado existe y no está ya dado de baja
            $asalariado = Asalariado::findOrFail($id);
            
            if ($asalariado->estado === 'baja') {
                return response()->json(['error' => 'Este asalariado ya está dado de baja'], 400);
            }
            
            if ($asalariado->estado_baja_programada === 'pendiente') {
                return response()->json(['error' => 'Este asalariado ya tiene una baja programada'], 400);
            }
            
            // Calcular la fecha para el día 1 del mes siguiente
            $fechaActual = Carbon::now();
            $fechaBajaProgramada = $fechaActual->copy()->addMonth()->startOfMonth();
            
            // Guardar la fecha actual para referencia
            $diasActuales = $asalariado->dias_trabajados ?? 0;
            
            // Programar la baja sin cambiar el estado actual (sigue activo)
            $asalariado->fecha_baja_programada = $fechaBajaProgramada;
            $asalariado->estado_baja_programada = 'pendiente';
            $asalariado->save();
            
            // Intentar registrar un gasto por el salario proporcional al final del mes
            try {
                $salarioProporcional = $asalariado->calcularSalarioBaja();
                if ($salarioProporcional > 0) {
                    \App\Models\Gasto::create([
                        'concepto' => 'Liquidación programada',
                        'descripcion' => 'Pago programado para baja efectiva el ' . $fechaBajaProgramada->format('d/m/Y'),
                        'tipo' => 'salario',
                        'importe' => $salarioProporcional,
                        'fecha' => $fechaBajaProgramada,
                        'id_asalariado' => $asalariado->id
                    ]);
                }
            } catch (\Exception $e) {
                // Registramos el error pero no interrumpimos el proceso
                \Log::warning('Error al crear gasto de liquidación programada: ' . $e->getMessage());
            }
            
            // Formatear fecha para el mensaje
            $fechaFormateada = $fechaBajaProgramada->format('d/m/Y');
            
            return response()->json([
                'success' => 'Baja programada correctamente para el día ' . $fechaFormateada, 
                'asalariado' => $asalariado,
                'fecha_baja_programada' => $fechaFormateada
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al programar baja de asalariado: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al programar la baja del asalariado'], 500);
        }
    }
    
    /**
     * Reactiva un asalariado (cambia su estado a 'alta')
     */
    public function reactivar($id)
    {
        // Verificar si el usuario tiene permisos para administrar asalariados
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return response()->json(['error' => 'No tienes permisos para esta acción'], 403);
        }
        
        try {
            $asalariado = Asalariado::findOrFail($id);
            
            // Obtener los días trabajados acumulados
            $diasTrabajadosAcumulados = $asalariado->dias_trabajados ?? 0;
            
            // Actualizar estado y fecha de contratación, pero mantener los días trabajados
            $asalariado->estado = 'alta';
            $asalariado->hiredate = Carbon::now(); // Actualizar fecha de contratación
            
            // Guardar historial de días acumulados para referencia
            $asalariado->dias_acumulados_historico = $diasTrabajadosAcumulados;
            
            // Establecer la fecha de última reactivación
            $asalariado->fecha_ultima_reactivacion = Carbon::now();
            
            // Mantener los días trabajados acumulados (no se reinician)
            $asalariado->dias_trabajados = $diasTrabajadosAcumulados;
            
            // Si había una baja programada pendiente, cancelarla
            if ($asalariado->estado_baja_programada === 'pendiente') {
                $asalariado->estado_baja_programada = 'cancelada';
            }
            
            $asalariado->save();
            
            return response()->json(['success' => 'Asalariado reactivado correctamente', 'asalariado' => $asalariado]);
        } catch (\Exception $e) {
            \Log::error('Error al reactivar asalariado: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al reactivar el asalariado: ' . $e->getMessage()], 500);
        }
    }
}
