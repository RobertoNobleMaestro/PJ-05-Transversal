<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    // Método privado para verificar si el usuario es administrador
    private function checkAdmin($request)
    {
        // Verificar si el usuario está autenticado
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        // Verificar si el usuario tiene rol de administrador (id_roles = 1)
        if (auth()->user()->id_roles !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar
    }
    
    // Método para obtener reservas en formato JSON (para AJAX)
    public function getReservas(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
        
        // Iniciar la consulta
        $query = Reserva::select('reservas.*', 'users.nombre as nombre_usuario', 'lugares.nombre as nombre_lugar')
                    ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
                    ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar');
        
        // Aplicar filtros si existen
        if ($request->has('usuario') && !empty($request->usuario)) {
            $query->where('users.nombre', 'like', '%' . $request->usuario . '%');
        }
        
        if ($request->has('lugar') && !empty($request->lugar)) {
            $query->where('reservas.id_lugar', $request->lugar);
        }
        
        if ($request->has('estado') && !empty($request->estado)) {
            $query->where('reservas.estado', $request->estado);
        }
        
        if ($request->has('fecha') && !empty($request->fecha)) {
            $query->whereDate('reservas.fecha_reserva', $request->fecha);
        }
        
        // Ejecutar la consulta
        $reservas = $query->get();
        
        // Obtener los vehículos asociados a cada reserva
        $reservas->each(function ($reserva) {
            $reserva->vehiculos_info = $reserva->vehiculos()->select(
                'vehiculos.id_vehiculos', 
                'vehiculos.marca', 
                'vehiculos.modelo', 
                'vehiculos_reservas.fecha_ini',
                'vehiculos_reservas.fecha_final',
                'vehiculos_reservas.precio_unitario'
            )->get();
        });
        
        return response()->json([
            'reservas' => $reservas
        ]);
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        
        return view('admin.reservas', compact('lugares', 'estados'));
    }

    public function create(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener usuarios, lugares y vehículos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::where('disponibilidad', true)->get();
        
        return view('admin.add_reserva', compact('usuarios', 'lugares', 'vehiculos'));
    }

    // Método para obtener reservas de un vehículo específico (para el calendario)
    public function reservasPorVehiculo($id_vehiculo)
    {
        // Buscar el vehículo
        $vehiculo = Vehiculo::findOrFail($id_vehiculo);
        
        // Obtener todas las reservas pendientes, confirmadas o completadas para este vehículo
        $reservasVehiculo = $vehiculo->vehiculosReservas()
            ->join('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
            ->whereIn('reservas.estado', ['pendiente', 'confirmada', 'completada'])
            ->get(['vehiculos_reservas.fecha_ini', 'vehiculos_reservas.fecha_final', 'reservas.estado']);
        
        // Formatear los datos para el calendario
        $eventos = [];
        
        foreach ($reservasVehiculo as $reserva) {
            // Definir color según el estado de la reserva
            $color = '#6f42c1'; // Color lila por defecto
            $titulo = 'Reservado';
            
            if ($reserva->estado === 'pendiente') {
                $color = '#ffc107'; // Amarillo para pendientes
                $titulo = 'Pendiente';
            } elseif ($reserva->estado === 'completada') {
                $color = '#28a745'; // Verde para completadas
                $titulo = 'Completada';
            }
            
            $eventos[] = [
                'start' => $reserva->fecha_ini,
                'end' => date('Y-m-d', strtotime($reserva->fecha_final . ' +1 day')), // Sumar un día para que incluya el día final
                'title' => $titulo,
                'color' => $color,
                'display' => 'background'
            ];
        }
        
        return response()->json($eventos);
    }
    
    // Método para crear una nueva reserva desde la página de detalle de vehículo
    public function crearReserva(Request $request)
    {
        try {
            // Verificar si el usuario está autenticado
            if (!auth()->check()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Debes iniciar sesión para realizar una reserva'
                ], 401);
            }
            
            // Validar datos
            $request->validate([
                'id_vehiculos' => 'required|exists:vehiculos,id_vehiculos',
                'fecha_ini' => 'required|date|after_or_equal:today',
                'fecha_final' => 'required|date|after_or_equal:fecha_ini',
            ]);
            
            // Buscar el vehículo
            $vehiculo = Vehiculo::findOrFail($request->id_vehiculos);
            
            // Verificar disponibilidad en esas fechas
            $fechaOcupada = $vehiculo->vehiculosReservas()
                ->join('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
                ->whereIn('reservas.estado', ['confirmada', 'completada'])
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '<=', $request->fecha_ini)
                          ->where('vehiculos_reservas.fecha_final', '>=', $request->fecha_ini);
                    })->orWhere(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '<=', $request->fecha_final)
                          ->where('vehiculos_reservas.fecha_final', '>=', $request->fecha_final);
                    })->orWhere(function($q) use ($request) {
                        $q->where('vehiculos_reservas.fecha_ini', '>=', $request->fecha_ini)
                          ->where('vehiculos_reservas.fecha_final', '<=', $request->fecha_final);
                    });
                })
                ->exists();
                
            if ($fechaOcupada) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El vehículo no está disponible en las fechas seleccionadas'
                ], 400);
            }
            
            // Calcular el número de días
            $fechaInicio = new \DateTime($request->fecha_ini);
            $fechaFin = new \DateTime($request->fecha_final);
            $diff = $fechaInicio->diff($fechaFin);
            $dias = $diff->days + 1; // Incluir el día de fin
            
            // Calcular precio total
            $precioUnitario = $vehiculo->precio_dia * $dias;
            
            // Iniciar transacción
            DB::beginTransaction();
            
            // Crear reserva (estado pendiente inicialmente)
            $reserva = Reserva::create([
                'fecha_reserva' => now(),
                'total_precio' => $precioUnitario,
                'estado' => 'pendiente',
                'id_lugar' => $vehiculo->id_lugar,
                'id_usuario' => auth()->id(),
            ]);
            
            // Asociar vehículo a la reserva
            $reserva->vehiculos()->attach($vehiculo->id_vehiculos, [
                'fecha_ini' => $request->fecha_ini,
                'fecha_final' => $request->fecha_final,
                'precio_unitario' => $precioUnitario,
            ]);
            
            // Confirmar transacción
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Reserva creada correctamente',
                'id_reserva' => $reserva->id_reservas
            ]);
            
        } catch (\Exception $e) {
            // Revertir transacción en caso de error
            DB::rollBack();
            
            return response()->json([
                'status' => 'error',
                'message' => 'Error al crear la reserva: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function store(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Crear la reserva
            $reserva = Reserva::create([
                'fecha_reserva' => $validatedData['fecha_reserva'],
                'total_precio' => 0, // Se actualizará después
                'estado' => $validatedData['estado'],
                'id_lugar' => $validatedData['id_lugar'],
                'id_usuario' => $validatedData['id_usuario'],
            ]);
            
            // Asociar vehículos a la reserva
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->update(['total_precio' => $total_precio]);
            
            // Confirmar la transacción
            DB::commit();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva añadida correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.reservas')->with('success', 'Reserva añadida correctamente');
        } catch (ValidationException $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error de validación',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al crear la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al crear la reserva: ' . $e->getMessage())->withInput();
        }
    }
    
    public function edit(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Buscar la reserva por su ID
        $reserva = Reserva::findOrFail($id_reservas);
        
        // Obtener los vehículos asociados a esta reserva con sus datos de relación
        $reserva_vehiculos = $reserva->vehiculos()->select(
            'vehiculos.id_vehiculos',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos_reservas.fecha_ini',
            'vehiculos_reservas.fecha_final',
            'vehiculos_reservas.precio_unitario'
        )->get();
        
        // Obtener datos para el formulario
        $usuarios = User::all();
        $lugares = Lugar::all();
        $vehiculos = Vehiculo::all(); // Mostrar todos los vehículos para poder editarlos
        
        return view('admin.edit_reserva', compact('reserva', 'reserva_vehiculos', 'usuarios', 'lugares', 'vehiculos'));
    }
    
    public function update(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        try {
            $validatedData = $request->validate([
                'fecha_reserva' => 'required|date',
                'estado' => 'required|string|in:pendiente,confirmada,cancelada,completada',
                'id_lugar' => 'required|exists:lugares,id_lugar',
                'id_usuario' => 'required|exists:users,id_usuario',
                'vehiculos' => 'required|array|min:1',
                'vehiculos.*' => 'exists:vehiculos,id_vehiculos',
                'fecha_inicio' => 'required|array|min:1',
                'fecha_inicio.*' => 'date',
                'fecha_fin' => 'required|array|min:1',
                'fecha_fin.*' => 'date|after_or_equal:fecha_inicio.*',
            ]);

            // Calcular el precio total de la reserva
            $total_precio = 0;
            
            // Encontrar la reserva a actualizar
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Actualizar los datos básicos de la reserva
            $reserva->fecha_reserva = $validatedData['fecha_reserva'];
            $reserva->estado = $validatedData['estado'];
            $reserva->id_lugar = $validatedData['id_lugar'];
            $reserva->id_usuario = $validatedData['id_usuario'];
            
            // Eliminar todas las relaciones existentes con vehículos
            $reserva->vehiculos()->detach();
            
            // Asociar vehículos a la reserva
            for ($i = 0; $i < count($validatedData['vehiculos']); $i++) {
                $vehiculo_id = $validatedData['vehiculos'][$i];
                $fecha_inicio = $validatedData['fecha_inicio'][$i];
                $fecha_fin = $validatedData['fecha_fin'][$i];
                
                // Obtener el precio diario del vehículo
                $vehiculo = Vehiculo::find($vehiculo_id);
                $precio_diario = $vehiculo->precio_dia;
                
                // Calcular el número de días
                $fecha_inicio_obj = new \DateTime($fecha_inicio);
                $fecha_fin_obj = new \DateTime($fecha_fin);
                $diff = $fecha_inicio_obj->diff($fecha_fin_obj);
                $dias = $diff->days + 1; // Incluir el día de fin
                
                // Calcular precio unitario (precio por todos los días de este vehículo)
                $precio_unitario = $precio_diario * $dias;
                $total_precio += $precio_unitario;
                
                // Asociar el vehículo a la reserva
                $reserva->vehiculos()->attach($vehiculo_id, [
                    'fecha_ini' => $fecha_inicio,
                    'fecha_final' => $fecha_fin,
                    'precio_unitario' => $precio_unitario,
                ]);
            }
            
            // Actualizar el precio total de la reserva
            $reserva->total_precio = $total_precio;
            $reserva->save();
            
            // Confirmar la transacción
            DB::commit();
            
            // Si la petición espera JSON (AJAX), devolver respuesta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva actualizada correctamente'
                ], 200);
            }
            
            // Si es una petición tradicional, redireccionar
            return redirect()->route('admin.reservas')->with('success', 'Reserva actualizada correctamente');
        } catch (ValidationException $e) {
            // Si hay errores de validación
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $e->errors()
                ], 422);
            }
            
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            // Si ocurre cualquier otro error
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al actualizar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Error al actualizar la reserva: ' . $e->getMessage())->withInput();
        }
    }
    
    public function destroy(Request $request, $id_reservas)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }

        try {
            $reserva = Reserva::findOrFail($id_reservas);
            
            // Comenzar una transacción para asegurar la integridad de los datos
            DB::beginTransaction();
            
            // Eliminar la reserva (y sus relaciones con vehículos por cascada)
            $reserva->delete();
            
            // Confirmar la transacción
            DB::commit();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Reserva eliminada correctamente'
                ], 200);
            }
            
            return redirect()->route('admin.reservas')->with('success', 'Reserva eliminada correctamente');
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error al eliminar la reserva: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('admin.reservas')->with('error', 'Error al eliminar la reserva: ' . $e->getMessage());
        }
    }
}
