<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Valoracion;
use App\Models\Tipo;

class HomeController extends Controller
{
    // Pagina principal con las estadisticas y el tipo
    public function index()
    {
        return view('PaginaPrincipal.index', $this->getStatsData());
    }

    // Devolver estadísticas via JSON
    public function stats()
    {
        return response()->json($this->getStatsData());
    }

    // Estadísticas y tipo vehiculo via fetch
    private function getStatsData(): array
    {
        return [
            'usuariosClientes' => User::where('id_roles', 2)->count(),
            'vehiculos' => Vehiculo::count(),
            'valoracionMedia' => round(Valoracion::avg('valoracion'), 1),
            'tipos' => Tipo::all(),
        ];
    }

    // Vehiculos via fetch
    public function listado() {
        $vehiculos = Vehiculo::select('id_vehiculos', 'precio_dia', 'marca', 'año', 'modelo', 'kilometraje')->get();
        // $valoraciones = Valoracion::select('puntuacion');
        return response()->json($vehiculos);
    }
    
}