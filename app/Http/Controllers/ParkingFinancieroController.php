<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parking;
use App\Models\Lugar;
use Illuminate\Support\Facades\Auth;

class ParkingFinancieroController extends Controller
{
    /**
     * Verificar que el usuario es administrador financiero
     */
    private function verificarAdminFinanciero()
    {
        if (!Auth::check() || !Auth::user()->hasRole('admin_financiero')) {
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        return null;
    }
    
    /**
     * Muestra la lista de parkings con sus detalles de valoración
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $check = $this->verificarAdminFinanciero();
        if ($check) return $check;
        
        // Obtener todos los parkings con sus lugares relacionados
        $parkings = Parking::with('lugar')->get();
        
        // Utilizar los valores almacenados en la base de datos o calcularlos si no existen
        foreach ($parkings as $parking) {
            // Si no tiene valores almacenados, calcularlos
            if ($parking->metros_cuadrados <= 0) {
                $parking->metros_cuadrados = $parking->plazas * 25; // 25m² por plaza
            }
            
            // Usar el método del modelo que ya comprueba si usar valores de DB
            $parking->valor_total = $parking->calcularValorTotal();
            
            // Asignar el precio por metro desde la DB al atributo que usa la vista
            $parking->precio_por_metro = $parking->precio_metro_cuadrado > 0 ? 
                                       $parking->precio_metro_cuadrado : 
                                       ($parking->metros_cuadrados > 0 ? 
                                        round($parking->valor_total / $parking->metros_cuadrados, 2) : 0);
                                        
            // Asegurar que la vista recibe el precio correcto usando el atributo que espera
        }
        
        return view('admin_financiero.parkings', compact('parkings'));
    }
    
    /**
     * Muestra el formulario para editar un parking específico
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $check = $this->verificarAdminFinanciero();
        if ($check) return $check;
        
        $parking = Parking::with('lugar')->findOrFail($id);
        
        // Utilizar valores almacenados en la base de datos o calcularlos si no existen
        if ($parking->metros_cuadrados <= 0) {
            $parking->metros_cuadrados = $parking->plazas * 25; // 25m² por plaza
        }
        
        // Usar el método del modelo que ya comprueba si usar valores de DB
        $parking->valor_total = $parking->calcularValorTotal();
        
        // Asignar el precio por metro desde la DB al atributo que usa la vista
        $parking->precio_por_metro = $parking->precio_metro_cuadrado > 0 ? 
                                   $parking->precio_metro_cuadrado : 
                                   ($parking->metros_cuadrados > 0 ? 
                                    round($parking->valor_total / $parking->metros_cuadrados, 2) : 0);
                                    
        // Asegurar que la vista recibe el precio correcto usando el atributo que espera
        
        // Obtener la lista de lugares para el select
        $lugares = Lugar::all();
        
        return view('admin_financiero.edit_parking', compact('parking', 'lugares'));
    }
    
    /**
     * Actualiza la información de un parking
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $check = $this->verificarAdminFinanciero();
        if ($check) return $check;
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'plazas' => 'required|integer|min:1',
            'id_lugar' => 'required|exists:lugares,id_lugar',
        ]);
        
        $parking = Parking::findOrFail($id);
        
        $parking->nombre = $request->nombre;
        $parking->plazas = $request->plazas;
        $parking->id_lugar = $request->id_lugar;
        
        $parking->save();
        
        return redirect()->route('admin.financiero.parkings.index')
                        ->with('success', 'Parking actualizado correctamente');
    }
}
