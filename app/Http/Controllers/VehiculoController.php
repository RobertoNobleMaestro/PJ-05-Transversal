<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function detalle($id)
    {
        $vehiculo = Vehiculo::with(['tipo', 'lugar', 'caracteristicas', 'valoraciones'])->findOrFail($id);
    
        return view('vehiculos.detalle_vehiculo', [
            'vehiculo' => $vehiculo
        ]);
    }
    
}
