<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asalariado;
use App\Models\User;
use App\Models\Parking;
use App\Models\Lugar;
use Illuminate\Support\Facades\Auth;

class AsalariadoController extends Controller
{
    /**
     * Constructor
     */
    public function __construct()
    {
        // El control de acceso ahora se gestiona mediante middleware en las rutas (routes/web.php)
    }

    /**
     * Muestra la lista de asalariados de la misma sede que el admin financiero
     */
    public function index()
    {
        // Obtener el admin financiero actual
        $adminFinanciero = Auth::user();
        
        // Verificar si el admin financiero tiene información de asalariado
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        
        if (!$asalariadoAdmin || !$asalariadoAdmin->parking_id) {
            return redirect()->route('home')->with('error', 'Necesitas tener un parking asignado para gestionar asalariados');
        }
        
        // Obtener el parking asociado al admin financiero
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener la sede (lugar)
        $sede = Lugar::find($sedeId);
        
        // Obtener todos los parkings de la misma sede
        $parkingsDeSedeIds = Parking::where('id_lugar', $sedeId)->pluck('id')->toArray();
        
        // Obtener todos los asalariados que pertenecen a esos parkings
        $asalariados = Asalariado::whereIn('parking_id', $parkingsDeSedeIds)->get();
        
        // Preparar datos para la vista
        $asalariadosConDetalles = [];
        
        foreach ($asalariados as $asalariado) {
            $usuario = User::find($asalariado->id_usuario);
            $parking = Parking::find($asalariado->parking_id);
            
            $asalariadosConDetalles[] = [
                'id' => $asalariado->id,
                'id_usuario' => $asalariado->id_usuario,
                'nombre' => $usuario->nombre,
                'rol' => $usuario->role->nombre,
                'salario' => $asalariado->salario,
                'dia_cobro' => $asalariado->dia_cobro,
                'parking' => $parking->nombre,
                'sede' => $sede->nombre
            ];
        }
        
        return view('admin_financiero.asalariados.index', [
            'asalariados' => $asalariadosConDetalles,
            'sede' => $sede
        ]);
    }

    /**
     * Muestra el formulario para editar un asalariado
     */
    public function edit($id)
    {
        // Verificar que el asalariado pertenezca a la misma sede que el admin financiero
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener el asalariado
        $asalariado = Asalariado::findOrFail($id);
        $parkingAsalariado = Parking::find($asalariado->parking_id);
        
        // Verificar que pertenezca a la misma sede
        if ($parkingAsalariado->id_lugar != $sedeId) {
            return redirect()->route('asalariados.index')
                ->with('error', 'No puedes editar asalariados de otras sedes');
        }
        
        // Obtener usuario y datos relacionados
        $usuario = User::find($asalariado->id_usuario);
        $sede = Lugar::find($sedeId);
        
        // Obtener parkings de la sede para el selector
        $parkingsDisponibles = Parking::where('id_lugar', $sedeId)->get();
        
        return view('admin_financiero.asalariados.edit', [
            'asalariado' => $asalariado,
            'usuario' => $usuario,
            'sede' => $sede,
            'parkings' => $parkingsDisponibles
        ]);
    }

    /**
     * Actualiza los datos del asalariado
     */
    public function update(Request $request, $id)
    {
        // Validación de datos
        $request->validate([
            'salario' => 'required|numeric|min:0',
            'dia_cobro' => 'required|integer|min:1|max:31',
            'parking_id' => 'required|exists:parking,id'
        ], [
            'salario.required' => 'El salario es obligatorio',
            'salario.numeric' => 'El salario debe ser un número',
            'salario.min' => 'El salario no puede ser negativo',
            'dia_cobro.required' => 'El día de cobro es obligatorio',
            'dia_cobro.integer' => 'El día de cobro debe ser un número entero',
            'dia_cobro.min' => 'El día de cobro debe ser al menos 1',
            'dia_cobro.max' => 'El día de cobro no puede ser mayor a 31',
            'parking_id.required' => 'El parking es obligatorio',
            'parking_id.exists' => 'El parking seleccionado no existe'
        ]);
        
        // Verificar que el admin financiero tenga permisos sobre este asalariado
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Verificar que el nuevo parking pertenezca a la misma sede
        $nuevoParking = Parking::find($request->parking_id);
        if ($nuevoParking->id_lugar != $sedeId) {
            return redirect()->route('asalariados.edit', $id)
                ->with('error', 'El parking seleccionado no pertenece a tu sede');
        }
        
        // Actualizar asalariado
        $asalariado = Asalariado::findOrFail($id);
        $asalariado->salario = $request->salario;
        $asalariado->dia_cobro = $request->dia_cobro;
        $asalariado->parking_id = $request->parking_id;
        $asalariado->save();
        
        return redirect()->route('asalariados.index')
            ->with('success', 'Información de asalariado actualizada correctamente');
    }

    /**
     * Muestra detalles específicos del asalariado
     */
    public function show($id)
    {
        // Verificar que el asalariado pertenezca a la misma sede que el admin financiero
        $adminFinanciero = Auth::user();
        $asalariadoAdmin = Asalariado::where('id_usuario', $adminFinanciero->id_usuario)->first();
        $parkingAdmin = Parking::find($asalariadoAdmin->parking_id);
        $sedeId = $parkingAdmin->id_lugar;
        
        // Obtener el asalariado
        $asalariado = Asalariado::findOrFail($id);
        $parkingAsalariado = Parking::find($asalariado->parking_id);
        
        // Verificar que pertenezca a la misma sede
        if ($parkingAsalariado->id_lugar != $sedeId) {
            return redirect()->route('asalariados.index')
                ->with('error', 'No puedes ver detalles de asalariados de otras sedes');
        }
        
        // Obtener usuario y datos relacionados
        $usuario = User::find($asalariado->id_usuario);
        $parking = Parking::find($asalariado->parking_id);
        $sede = Lugar::find($sedeId);
        
        return view('admin_financiero.asalariados.show', [
            'asalariado' => $asalariado,
            'usuario' => $usuario,
            'parking' => $parking,
            'sede' => $sede
        ]);
    }
}
