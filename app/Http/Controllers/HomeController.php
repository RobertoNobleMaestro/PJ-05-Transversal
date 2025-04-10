<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
    public function listado()
    {
        $vehiculos = DB::table('vehiculos')
            ->leftJoin('vehiculos_reservas', 'vehiculos.id_vehiculos', '=', 'vehiculos_reservas.id_vehiculos')
            ->leftJoin('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
            ->leftJoin('valoraciones', 'reservas.id_reservas', '=', 'valoraciones.id_reservas')
            ->select(
                'vehiculos.id_vehiculos',
                'vehiculos.precio_dia',
                'vehiculos.marca',
                'vehiculos.modelo',
                'vehiculos.kilometraje',
                'vehiculos.año',
                DB::raw('ROUND(AVG(valoraciones.valoracion), 1) as valoracion')
            )
            ->groupBy(
                'vehiculos.id_vehiculos',
                'vehiculos.precio_dia',
                'vehiculos.marca',
                'vehiculos.modelo',
                'vehiculos.kilometraje',
                'vehiculos.año'
            )
            ->get();

        return response()->json($vehiculos);
    }

    // public function listado()
    // {
    //     $vehiculos = DB::table('vehiculos')
    //         ->select(
    //             'id_vehiculos',
    //             'precio_dia',
    //             'marca',
    //             'modelo',
    //             'kilometraje',
    //             'año',
    //             DB::raw('0 as valoracion') // Provisional
    //         )
    //         ->get();

    //     return response()->json($vehiculos);
    // }

}