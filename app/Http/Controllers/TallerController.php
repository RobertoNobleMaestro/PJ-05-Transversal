<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Taller;
use App\Models\Mantenimiento;
use Carbon\Carbon;
use DB;

class TallerController extends Controller
{
    // Método para mostrar la vista de los vehículos
    public function index()
    {
        // Recupera todos los vehículos con sus relaciones (lugar, tipo, parking)
        $vehiculos = Vehiculo::with(['lugar', 'tipo', 'parking'])->get();
        
        // Obtener la lista de talleres disponibles
        $talleres = Taller::all();

        // Retorna la vista 'Taller.index' pasando los vehículos recuperados
        return view('Taller.index', compact('vehiculos', 'talleres'));
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
            // Validar la fecha, hora y taller
            $request->validate([
                'id_vehiculo' => 'required|exists:vehiculos,id_vehiculos',
                'fecha_mantenimiento' => 'required|date|after_or_equal:today',
                'hora_mantenimiento' => 'required',
                'taller_id' => 'required|exists:talleres,id'
            ]);
            
            $vehiculo = Vehiculo::find($request->id_vehiculo);
            
            if (!$vehiculo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vehículo no encontrado'
                ], 404);
            }
            
            // Verificar la cantidad de vehículos ya programados para esa hora y taller
            $citas_existentes = Mantenimiento::where('taller_id', $request->taller_id)
                ->where('fecha_programada', $request->fecha_mantenimiento)
                ->where('hora_programada', $request->hora_mantenimiento)
                ->count();
                
            $taller = Taller::find($request->taller_id);
            
            if ($citas_existentes >= $taller->capacidad_hora) {
                return response()->json([
                    'success' => false,
                    'message' => 'El taller ya tiene ' . $taller->capacidad_hora . ' vehículos programados para esta hora. Por favor seleccione otra hora o taller.'
                ], 400);
            }
            
            // Crear el registro de mantenimiento
            $mantenimiento = new Mantenimiento();
            $mantenimiento->vehiculo_id = $request->id_vehiculo;
            $mantenimiento->taller_id = $request->taller_id;
            $mantenimiento->fecha_programada = $request->fecha_mantenimiento;
            $mantenimiento->hora_programada = $request->hora_mantenimiento;
            $mantenimiento->estado = 'pendiente';
            $mantenimiento->save();
            
            // Guardar fecha actual como última fecha de mantenimiento
            $vehiculo->ultima_fecha_mantenimiento = now();
            
            // Guardar la próxima fecha de mantenimiento (fecha y hora)
            $fechaHora = $request->fecha_mantenimiento . ' ' . $request->hora_mantenimiento;
            $vehiculo->proxima_fecha_mantenimiento = $fechaHora;
            
            $vehiculo->save();
            
            // Obtener el nombre del taller para la respuesta
            $nombre_taller = $taller->nombre;
            
            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento agendado exitosamente en ' . $nombre_taller,
                'fecha' => $vehiculo->proxima_fecha_mantenimiento,
                'taller' => $nombre_taller
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agendar mantenimiento: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Método para obtener los horarios disponibles de un taller específico
    public function getHorariosDisponibles(Request $request)
    {
        try {
            $request->validate([
                'taller_id' => 'required|exists:talleres,id',
                'fecha' => 'required|date|after_or_equal:today',
            ]);
            
            $taller = Taller::find($request->taller_id);
            $fecha = $request->fecha;
            
            // Obtener todas las citas programadas para ese día y taller con la agrupación correcta
            $citas = Mantenimiento::where('taller_id', $taller->id)
                ->where('fecha_programada', $fecha)
                ->select('hora_programada', DB::raw('count(*) as total'))
                ->groupBy('hora_programada')
                ->get()
                ->keyBy('hora_programada');
            
            // Crear un array de horarios disponibles (8am a 6pm)
            $horarios = [];
            $inicio = 8; // 8am
            $fin = 18; // 6pm
            
            for ($hora = $inicio; $hora < $fin; $hora++) {
                $tiempo = sprintf('%02d:00', $hora);
                $total_citas = isset($citas[$tiempo]) ? $citas[$tiempo]->total : 0;
                
                $horarios[] = [
                    'hora' => $tiempo,
                    'disponible' => ($total_citas < $taller->capacidad_hora),
                    'ocupacion' => $total_citas . '/' . $taller->capacidad_hora
                ];
            }
            
            return response()->json([
                'success' => true,
                'horarios' => $horarios
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener horarios: ' . $e->getMessage()
            ], 500);
        }
    }
}