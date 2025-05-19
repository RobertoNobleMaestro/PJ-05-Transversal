<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehiculo;
use App\Models\Taller;
use App\Models\Mantenimiento;
use Carbon\Carbon;
use DB;
use Barryvdh\DomPDF\Facade\Pdf;

class TallerController extends Controller
{
    // Método para mostrar la vista de los vehículos
    public function index()
    {
        // Recupera todos los vehículos con sus relaciones (lugar, tipo, parking)
        $vehiculos = Vehiculo::with(['lugar', 'tipo', 'parking'])->paginate(4);
        
        // Obtener la lista de talleres disponibles
        $talleres = Taller::all();
        
        // Catálogos para los filtros
        $lugares = \App\Models\Lugar::all();
        $tipos = \App\Models\Tipo::all();
        $anios = Vehiculo::select('año')->distinct()->orderBy('año', 'desc')->pluck('año');
        $parkings = \App\Models\Parking::all();

        // Retorna la vista 'Taller.index' pasando los vehículos recuperados y catálogos
        return view('Taller.index', compact('vehiculos', 'talleres', 'lugares', 'tipos', 'anios', 'parkings'));
    }

    // AJAX: Filtrado sumativo
    public function filtrarVehiculos(Request $request)
    {
        $query = Vehiculo::with(['lugar', 'tipo', 'parking']);
        if ($request->filled('sede')) {
            $query->where('id_lugar', $request->sede);
        }
        if ($request->filled('año')) {
            $query->where('año', $request->año);
        }
        if ($request->filled('tipo')) {
            $query->where('id_tipo', $request->tipo);
        }
        if ($request->filled('parking')) {
            $query->where('parking_id', $request->parking);
        }
        $vehiculos = $query->paginate(4)->appends($request->except('page'));
        $html = view('Taller.partials.tabla_vehiculos', compact('vehiculos'))->render();
        return response()->json(['html' => $html]);
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
    
    // Método para obtener historial de mantenimientos con filtro por estado
    public function getMantenimientos(Request $request)
    {
        try {
            $query = Mantenimiento::with(['vehiculo', 'taller'])
                ->orderBy('created_at', 'desc');
            
            // Filtrar por estado si se especifica
            if ($request->has('estado') && $request->estado !== 'todos') {
                $query->where('estado', $request->estado);
            }
            
            $mantenimientos = $query->get();
            
            // Formatear datos para la respuesta
            $resultado = $mantenimientos->map(function($mantenimiento) {
                $fechaHora = Carbon::parse($mantenimiento->fecha_programada->format('Y-m-d') . ' ' . $mantenimiento->hora_programada);
                
                // Determinar el color del badge según el estado
                $colorEstado = [
                    'pendiente' => 'warning',
                    'completado' => 'success',
                    'cancelado' => 'danger'
                ][$mantenimiento->estado] ?? 'secondary';
                
                return [
                    'id' => $mantenimiento->id,
                    'vehiculo' => $mantenimiento->vehiculo->marca . ' ' . $mantenimiento->vehiculo->modelo,
                    'matricula' => $mantenimiento->vehiculo->matricula ?? 'N/A',
                    'taller' => $mantenimiento->taller->nombre,
                    'fecha' => $mantenimiento->fecha_programada->format('d/m/Y'),
                    'hora' => $mantenimiento->hora_programada,
                    'estado' => $mantenimiento->estado,
                    'colorEstado' => $colorEstado,
                    'fechaCompleta' => $fechaHora->format('d/m/Y H:i:s'),
                    'esPasado' => $fechaHora->isPast(),
                    'id_vehiculo' => $mantenimiento->vehiculo_id
                ];
            });
            
            return response()->json([
                'success' => true, 
                'mantenimientos' => $resultado
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener mantenimientos: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Método para obtener detalle de un mantenimiento específico
    public function getDetalleMantenimiento($id)
    {
        try {
            $mantenimiento = Mantenimiento::with(['vehiculo', 'taller'])
                ->findOrFail($id);
            
            $fechaHora = Carbon::parse($mantenimiento->fecha_programada->format('Y-m-d') . ' ' . $mantenimiento->hora_programada);
            
            $detalle = [
                'id' => $mantenimiento->id,
                'vehiculo' => [
                    'id' => $mantenimiento->vehiculo->id_vehiculos,
                    'marca' => $mantenimiento->vehiculo->marca,
                    'modelo' => $mantenimiento->vehiculo->modelo,
                    'matricula' => $mantenimiento->vehiculo->matricula ?? 'N/A',
                    'año' => $mantenimiento->vehiculo->año,
                    'kilometraje' => $mantenimiento->vehiculo->kilometraje
                ],
                'taller' => [
                    'id' => $mantenimiento->taller->id,
                    'nombre' => $mantenimiento->taller->nombre,
                    'direccion' => $mantenimiento->taller->direccion,
                    'telefono' => $mantenimiento->taller->telefono
                ],
                'fecha_programada' => $mantenimiento->fecha_programada->format('Y-m-d'),
                'hora_programada' => $mantenimiento->hora_programada,
                'fechaCompleta' => $fechaHora->format('d/m/Y H:i'),
                'estado' => $mantenimiento->estado,
                'fechaCreacion' => $mantenimiento->created_at->format('d/m/Y H:i'),
                'fechaActualizacion' => $mantenimiento->updated_at->format('d/m/Y H:i'),
                'esPasado' => $fechaHora->isPast()
            ];
            
            return response()->json([
                'success' => true,
                'detalle' => $detalle
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener detalle: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Método para actualizar el estado de un mantenimiento
    public function actualizarEstadoMantenimiento(Request $request, $id)
    {
        try {
            $request->validate([
                'estado' => 'required|in:pendiente,completado,cancelado'
            ]);
            
            $mantenimiento = Mantenimiento::findOrFail($id);
            $mantenimiento->estado = $request->estado;
            $mantenimiento->save();
            
            // Si el mantenimiento se cancela, hay que actualizar la fecha de próximo mantenimiento del vehículo
            if ($request->estado === 'cancelado') {
                $vehiculo = Vehiculo::find($mantenimiento->vehiculo_id);
                if ($vehiculo) {
                    $vehiculo->proxima_fecha_mantenimiento = null;
                    $vehiculo->save();
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Estado de mantenimiento actualizado a: ' . $request->estado
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado: ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        $mantenimiento = Mantenimiento::find($id);
        if (!$mantenimiento) {
            return response()->json(['success' => false, 'message' => 'Mantenimiento no encontrado.']);
        }

        $mantenimiento->delete();
        return response()->json(['success' => true]);
    }

    public function edit($id)
    {
        $mantenimiento = Mantenimiento::findOrFail($id);
        $vehiculos = Vehiculo::all();
        $talleres = Taller::all();

        return view('taller.edit', compact('mantenimiento', 'vehiculos', 'talleres'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'vehiculo_id' => 'required|exists:vehiculos,id_vehiculos',
        'taller_id' => 'required|exists:talleres,id',
        'fecha_programada' => 'required|date',
        'hora_programada' => 'required',
        'estado' => 'required|in:pendiente,completado,cancelado',
    ]);

    $mantenimiento = Mantenimiento::findOrFail($id);
    $mantenimiento->update($request->only([
        'vehiculo_id',
        'taller_id',
        'fecha_programada',
        'hora_programada',
        'estado'
    ]));

    if ($request->estado === 'completado') {
        $vehiculo = $mantenimiento->vehiculo;
        if ($vehiculo) {
            $vehiculo->ultima_fecha_mantenimiento = $request->fecha_programada;

            // Sumar 6 meses a ultima_fecha_mantenimiento para proxima_fecha_mantenimiento
            $vehiculo->proxima_fecha_mantenimiento = Carbon::parse($request->fecha_programada)->addMonths(6);

            $vehiculo->save();
        }
    }

    return redirect()->route('taller.historial')->with('success', 'Mantenimiento actualizado correctamente.');
}




    // Método para mostrar la página de historial de mantenimientos
    public function historial()
    {
        return view('Taller.historial');
    }

    public function descargarFactura($id)
    {
        $mantenimiento = \App\Models\Mantenimiento::with(['vehiculo.tipo'])->findOrFail($id);
        $vehiculo = $mantenimiento->vehiculo;
    
        // Ejemplo de precios por tipo
        $precios = [
            'Coche' => 120,
            'Moto' => 60,
            'Furgoneta' => 150,
        ];
        $precio = $precios[$vehiculo->tipo->nombre ?? 'Coche'] ?? 100;
    
        $data = [
            'mantenimiento' => $mantenimiento,
            'vehiculo' => $vehiculo,
            'precio' => $precio,
            'fecha_hora' => $mantenimiento->fecha_programada->format('d/m/Y') . ' ' . $mantenimiento->hora_programada,
            'imagen' => $vehiculo->imagen // Ajusta según tu sistema
        ];
    
        $pdf = Pdf::loadView('Taller.factura', $data);
        return $pdf->download('factura-mantenimiento-'.$vehiculo->matricula.'.pdf');
    }
}