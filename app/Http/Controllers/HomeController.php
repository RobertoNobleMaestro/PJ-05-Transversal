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
    public function listado(Request $request)
    {
        $page = (int) $request->input('page', 1);
        $perPage = (int) $request->input('perPage', 16);
        $offset = ($page - 1) * $perPage;

        $marca = $request->input('marca');
        $precioMin = $request->input('precioMin');
        $precioMax = $request->input('precioMax');
        $tipos = $request->input('tipos');
        $lugares = $request->input('lugares');
        $anios = $request->input('anios');
        $valoraciones = $request->input('valoraciones');

        $query = DB::table('vehiculos')
        ->leftJoin('vehiculos_reservas', 'vehiculos.id_vehiculos', '=', 'vehiculos_reservas.id_vehiculos')
        ->leftJoin('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
        ->leftJoin('valoraciones', 'reservas.id_reservas', '=', 'valoraciones.id_reservas')
        ->leftJoin('lugares', 'vehiculos.id_lugar', '=', 'lugares.id_lugar')
        ->leftJoin('tipo', 'vehiculos.id_tipo', '=', 'tipo.id_tipo')
        ->select(
            'vehiculos.id_vehiculos',
            'vehiculos.precio_dia',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos.kilometraje',
            'vehiculos.año',
            'lugares.nombre as ciudad',
            'tipo.nombre as tipo',
            DB::raw('ROUND(AVG(valoraciones.valoracion), 1) as valoracion')
        )
        ->groupBy(
            'vehiculos.id_vehiculos',
            'vehiculos.precio_dia',
            'vehiculos.marca',
            'vehiculos.modelo',
            'vehiculos.kilometraje',
            'vehiculos.año',
            'lugares.nombre',
            'tipo.nombre'
        );

        if ($marca) $query->where('vehiculos.marca', 'like', "%$marca%");
        if (is_numeric($precioMin)) $query->where('vehiculos.precio_dia', '>=', (float) $precioMin);
        if (is_numeric($precioMax)) $query->where('vehiculos.precio_dia', '<=', (float) $precioMax);
        if ($tipos) $query->whereIn('tipo.nombre', $tipos);
        if ($lugares) $query->whereIn('lugares.nombre', $lugares);
        if ($anios) $query->whereIn('vehiculos.año', $anios);
        if ($valoraciones) {
            $query->havingRaw('FLOOR(AVG(valoraciones.valoracion)) IN (' . implode(',', array_map('intval', $valoraciones)) . ')');
        }

        $total = $query->get()->count();
        $vehiculos = $query->offset($offset)->limit($perPage)->get();

        return response()->json([
            'vehiculos' => $vehiculos,
            'totalPages' => ceil($total / $perPage),
        ]);
    }

    public function obtenerCiudades()
    {
        $ciudades = DB::table('lugares')->select('nombre')->distinct()->pluck('nombre');
        return response()->json($ciudades);
    }

    public function obtenerAño()
    {
        $año = Vehiculo::select('año')
            ->distinct()
            ->orderBy('año', 'desc')
            ->pluck('año');

        return response()->json($año);
    }

}