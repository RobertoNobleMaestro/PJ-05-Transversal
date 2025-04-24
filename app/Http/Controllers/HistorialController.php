<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class HistorialController extends Controller
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
    
    // Página principal del historial
    public function index(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Obtener datos para los filtros
        $usuarios = User::all();
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        
        return view('admin.historial', compact('usuarios', 'lugares', 'estados'));
    }
    
    // Método para obtener los datos del historial de reservas en formato JSON (para AJAX)
    public function getData(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck && $request->expectsJson()) {
            return $authCheck;
        }
        
        // Iniciar la consulta
        $query = Reserva::select(
                'reservas.*', 
                'users.nombre as nombre_usuario', 
                'lugares.nombre as nombre_lugar'
            )
            ->leftJoin('users', 'reservas.id_usuario', '=', 'users.id_usuario')
            ->leftJoin('lugares', 'reservas.id_lugar', '=', 'lugares.id_lugar')
            ->orderBy('reservas.fecha_reserva', 'desc'); // Ordenar por fecha de reserva descendente
        
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
        
        if ($request->has('fecha_desde') && !empty($request->fecha_desde)) {
            $query->whereDate('reservas.fecha_reserva', '>=', $request->fecha_desde);
        }
        
        if ($request->has('fecha_hasta') && !empty($request->fecha_hasta)) {
            $query->whereDate('reservas.fecha_reserva', '<=', $request->fecha_hasta);
        }
        
        // Aplicar filtro de precio si existe
        if ($request->has('precio_min') && !empty($request->precio_min)) {
            $query->where('reservas.total_precio', '>=', $request->precio_min);
        }
        
        if ($request->has('precio_max') && !empty($request->precio_max)) {
            $query->where('reservas.total_precio', '<=', $request->precio_max);
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
        
        // Calcular estadísticas generales
        $stats = [
            'total_reservas' => $reservas->count(),
            'reservas_completadas' => $reservas->where('estado', 'completada')->count(),
            'reservas_pendientes' => $reservas->where('estado', 'pendiente')->count(),
            'reservas_canceladas' => $reservas->where('estado', 'cancelada')->count(),
            'reservas_confirmadas' => $reservas->where('estado', 'confirmada')->count(),
            'ingreso_total' => $reservas->where('estado', 'completada')->sum('total_precio'),
            'reserva_maxima' => $reservas->max('total_precio'),
            'reserva_minima' => $reservas->min('total_precio'),
            'reserva_promedio' => $reservas->avg('total_precio')
        ];
        
        return response()->json([
            'reservas' => $reservas,
            'stats' => $stats
        ]);
    }
    
    // Método para generar informes o estadísticas
    public function reportes(Request $request)
    {
        $authCheck = $this->checkAdmin($request);
        if ($authCheck) {
            return $authCheck;
        }
        
        // Implementar lógica para generar informes
        // Por ahora retornamos a la vista principal
        return redirect()->route('admin.historial');
    }
}
