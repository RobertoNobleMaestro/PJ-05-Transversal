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
        
        // Obtener asalariados dados de baja
        $asalariados = Asalariado::where('estado', 'baja')
            ->with('usuario', 'usuario.role', 'parking', 'sede')
            ->get();
            
        return view('admin_financiero.asalariados_inactivos', [
            'asalariados' => $asalariados
        ]);
    }
    
    /**
     * Desactiva un asalariado (cambia su estado a 'baja')
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
            
            // Guardar los días trabajados hasta hoy en el mes actual
            $fechaActual = Carbon::now();
            $primerDiaMes = Carbon::now()->startOfMonth();
            $diasTrabajados = 0;
            
            // Verificar que la fecha de contratación es válida
            if ($asalariado->hiredate instanceof \Carbon\Carbon) {
                if ($asalariado->hiredate->lt($primerDiaMes)) {
                    // Si fue contratado antes del inicio del mes actual
                    $diasTrabajados = $fechaActual->day;
                } else {
                    // Si fue contratado durante el mes actual
                    $diasTrabajados = $fechaActual->diffInDays($asalariado->hiredate) + 1;
                }
            } else {
                // Si no hay fecha de contratación válida, asumimos el mes completo
                $diasTrabajados = $fechaActual->day;
            }
            
            // Actualizar el asalariado
            $asalariado->dias_trabajados = $diasTrabajados;
            $asalariado->estado = 'baja';
            $asalariado->save();
            
            // Intentar registrar un gasto por el salario proporcional
            try {
                $salarioProporcional = $asalariado->calcularSalarioBaja();
                if ($salarioProporcional > 0) {
                    \App\Models\Gasto::create([
                        'concepto' => 'Liquidación por baja',
                        'descripcion' => 'Pago proporcional por ' . $diasTrabajados . ' días trabajados',
                        'tipo' => 'salario',
                        'importe' => $salarioProporcional,
                        'fecha' => Carbon::now(),
                        'id_asalariado' => $asalariado->id
                    ]);
                }
            } catch (\Exception $e) {
                // Registramos el error pero no interrumpimos el proceso
                \Log::warning('Error al crear gasto de liquidación: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => 'Asalariado desactivado correctamente', 
                'asalariado' => $asalariado,
                'dias_trabajados' => $diasTrabajados
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error al desactivar asalariado: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al desactivar el asalariado'], 500);
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
            $asalariado->estado = 'alta';
            $asalariado->dias_trabajados = 0; // Reiniciar contador de días trabajados
            $asalariado->hiredate = Carbon::now(); // Establecer la fecha de contratación a la fecha actual
            $asalariado->save();
            
            return response()->json(['success' => 'Asalariado reactivado correctamente', 'asalariado' => $asalariado]);
        } catch (\Exception $e) {
            \Log::error('Error al reactivar asalariado: ' . $e->getMessage());
            return response()->json(['error' => 'Ocurrió un error al reactivar el asalariado: ' . $e->getMessage()], 500);
        }
    }
}
