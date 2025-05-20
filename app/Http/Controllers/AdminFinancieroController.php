<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Parking;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminFinancieroController extends Controller
{
    /**
     * Middleware para asegurar que solo los administradores financieros accedan
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->id_roles !== 5) {
                return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
            }
            return $next($request);
        });
    }
    
    /**
     * Muestra la página principal del panel de administración financiera
     */
    public function index()
    {
        // Obtener la sede (lugar) del administrador financiero actual
        $adminFinanciero = Auth::user();
        $adminAsalariado = $adminFinanciero->asalariado;
        
        if (!$adminAsalariado || !$adminAsalariado->parking) {
            return redirect('/')->with('error', 'No tienes una sede asignada. Contacta con el administrador del sistema.');
        }
        
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings asociados a esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // Obtener todos los asalariados de los parkings de esta sede
        $asalariados = Asalariado::whereIn('parking_id', $parkingsIds)
            ->with(['usuario', 'usuario.role', 'parking', 'parking.lugar'])
            ->get();
        
        return view('admin_financiero.index', [
            'asalariados' => $asalariados,
            'sede' => $sede
        ]);
    }
    
    /**
     * Muestra el formulario para editar un asalariado
     */
    public function edit($id)
    {
        $asalariado = Asalariado::with(['usuario', 'parking.lugar'])->findOrFail($id);
        
        // Verificar que el asalariado pertenece a la sede del admin financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeAdmin = $adminAsalariado->parking->id_lugar;
        $sedeAsalariado = $asalariado->parking->id_lugar;
        
        if ($sedeAdmin != $sedeAsalariado) {
            return redirect()->route('admin.financiero.index')
                ->with('error', 'No puedes editar asalariados de otras sedes');
        }
        
        // Obtener los parkings de la sede para permitir cambios
        $parkings = Parking::where('id_lugar', $sedeAdmin)->get();
        
        return view('admin_financiero.edit', [
            'asalariado' => $asalariado,
            'parkings' => $parkings
        ]);
    }
    
    /**
     * Actualiza la información de un asalariado
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'salario' => 'required|numeric|min:0',
            'dia_cobro' => 'required|integer|min:1|max:31',
            'parking_id' => 'required|exists:parking,id'
        ]);
        
        $asalariado = Asalariado::findOrFail($id);
        
        // Verificar que el asalariado pertenece a la sede del admin financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeAdmin = $adminAsalariado->parking->id_lugar;
        
        // Verificar que el nuevo parking pertenece a la misma sede
        $nuevoParking = Parking::findOrFail($request->parking_id);
        if ($nuevoParking->id_lugar != $sedeAdmin) {
            return redirect()->back()
                ->with('error', 'No puedes asignar parkings de otras sedes')
                ->withInput();
        }
        
        $asalariado->update([
            'salario' => $request->salario,
            'dia_cobro' => $request->dia_cobro,
            'parking_id' => $request->parking_id
        ]);
        
        return redirect()->route('admin.financiero.index')
            ->with('success', 'Información del asalariado actualizada correctamente');
    }
    
    /**
     * Muestra el detalle de un asalariado
     */
    public function show($id)
    {
        $asalariado = Asalariado::with(['usuario', 'usuario.role', 'parking', 'parking.lugar'])->findOrFail($id);
        
        // Verificar que el asalariado pertenece a la sede del admin financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeAdmin = $adminAsalariado->parking->id_lugar;
        $sedeAsalariado = $asalariado->parking->id_lugar;
        
        if ($sedeAdmin != $sedeAsalariado) {
            return redirect()->route('admin.financiero.index')
                ->with('error', 'No puedes ver asalariados de otras sedes');
        }
        
        return view('admin_financiero.show', [
            'asalariado' => $asalariado
        ]);
    }
    
    /**
     * Muestra el resumen de costes de personal por sede
     */
    public function resumen()
    {
        // Obtener la sede del administrador financiero
        $adminAsalariado = Auth::user()->asalariado;
        $sedeId = $adminAsalariado->parking->id_lugar;
        $sede = Lugar::find($sedeId);
        
        // Obtener los parkings de esta sede
        $parkingsIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // Estadísticas de asalariados por rol
        $estadisticas = Asalariado::whereIn('parking_id', $parkingsIds)
            ->join('users', 'asalariados.id_usuario', '=', 'users.id_usuario')
            ->join('roles', 'users.id_roles', '=', 'roles.id_roles')
            ->select('roles.nombre_rol', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(asalariados.salario) as total_salarios'))
            ->groupBy('roles.nombre_rol')
            ->get();
        
        // Total mensual de salarios
        $totalMensual = Asalariado::whereIn('parking_id', $parkingsIds)->sum('salario');
        
        return view('admin_financiero.resumen', [
            'sede' => $sede,
            'estadisticas' => $estadisticas,
            'totalMensual' => $totalMensual
        ]);
    }
}
