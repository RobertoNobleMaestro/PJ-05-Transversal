<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Valoracion;
use App\Models\Tipo;

class HomeController extends Controller
{
    /**
     * Muestra la página principal con estadísticas y tipos.
     */
    public function index()
    {
        return view('PaginaPrincipal.index', $this->getStatsData());
    }

    /**
     * Devuelve las estadísticas como JSON para peticiones AJAX.
     */
    public function stats()
    {
        return response()->json($this->getStatsData());
    }

    /**
     * Reúne los datos de estadísticas y tipos de vehículos.
     */
    private function getStatsData(): array
    {
        return [
            'usuariosClientes' => User::where('id_roles', 2)->count(),
            'vehiculos' => Vehiculo::count(),
            'valoracionMedia' => round(Valoracion::avg('valoracion'), 1),
            'tipos' => Tipo::all(),
        ];
    }
}