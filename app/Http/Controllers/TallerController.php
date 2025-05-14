<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;

class TallerController extends Controller
{
    // Método para mostrar la vista de los vehículos
    public function index()
    {
        // Recupera todos los vehículos con sus relaciones (lugar, tipo, parking)
        $vehiculos = Vehiculo::with(['lugar', 'tipo', 'parking'])->get();

        // Retorna la vista 'Taller.index' pasando los vehículos recuperados
        return view('Taller.index', compact('vehiculos'));
    }

    // Método para actualizar la fecha de mantenimiento de un vehículo
    public function actualizarMantenimiento(Request $request, $id)
    {
        // Busca el vehículo por su ID
        $vehiculo = Vehiculo::find($id);

        // Si no se encuentra el vehículo, redirige con un mensaje de error
        if (!$vehiculo) {
            return redirect()->route('taller.index')->with('error', 'Vehículo no encontrado');
        }

        // Valida y guarda la nueva fecha de mantenimiento
        $vehiculo->proxima_fecha_mantenimiento = $request->proxima_fecha_mantenimiento;
        $vehiculo->save();

        // Redirige de nuevo a la lista de vehículos con un mensaje de éxito
        return redirect()->route('taller.index')->with('success', 'Mantenimiento actualizado exitosamente');
    }
    
    // Método para manejar peticiones AJAX de programación de mantenimiento
    public function agendarMantenimiento(Request $request)
    {
        try {
            $request->validate([
                'id_vehiculo' => 'required|exists:vehiculos,id_vehiculos',
                'fecha_mantenimiento' => 'required|date|after:today',
                'hora_mantenimiento' => 'required'
            ]);
            
            $vehiculo = Vehiculo::find($request->id_vehiculo);
            
            if (!$vehiculo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado'
                ], 404);
            }
            
            // Guardar fecha actual como última fecha de mantenimiento
            $vehiculo->ultima_fecha_mantenimiento = now();
            
            // Combinar fecha y hora para el próximo mantenimiento
            $fechaHora = $request->fecha_mantenimiento . ' ' . $request->hora_mantenimiento;
            $vehiculo->proxima_fecha_mantenimiento = $fechaHora;
            
            $vehiculo->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento agendado exitosamente',
                'fecha' => $vehiculo->proxima_fecha_mantenimiento
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agendar mantenimiento: ' . $e->getMessage()
            ], 500);
        }
    }
}