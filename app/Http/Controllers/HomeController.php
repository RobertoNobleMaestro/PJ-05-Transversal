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
        $perPage = (int) $request->input('perPage', 8);
        $offset = ($page - 1) * $perPage;

        $marca = $request->input('marca');
        $anio = $request->input('anio');
        $precioMin = $request->input('precioMin');
        $precioMax = $request->input('precioMax');
        $valoracionMin = $request->input('valoracionMin');
        
        // Base query con joins y agrupaciones
        $baseQuery = DB::table('vehiculos')
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
            );

        // Aplicar filtros (usando where si es posible para evitar conflicto con groupBy)
        if (!empty($marca)) {
            $baseQuery->where('vehiculos.marca', 'like', "%$marca%");
        }

        if (!empty($valoracionMin)) {
            $baseQuery->havingRaw('ROUND(AVG(valoraciones.valoracion), 1) >= ?', [$valoracionMin]);
        }

        if (!empty($anio)) {
            $baseQuery->where('vehiculos.año', '=', $anio);
        }

        if (is_numeric($precioMin)) {
            $baseQuery->where('vehiculos.precio_dia', '>=', (float) $precioMin);
        }
        
        if (is_numeric($precioMax)) {
            $baseQuery->where('vehiculos.precio_dia', '<=', (float) $precioMax);
        }            

        // Clonar la query para contar total antes de aplicar limit y offset
        $countQuery = clone $baseQuery;
        $total = $countQuery->get()->count(); // contar después de agrupar

        // Paginación SQL
        $vehiculosPaginados = $baseQuery
            ->offset($offset)
            ->limit($perPage)
            ->get();

        $totalPages = ceil($total / $perPage);

        return response()->json([
            'vehiculos' => $vehiculosPaginados,
            'totalPages' => $totalPages,
        ]);
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