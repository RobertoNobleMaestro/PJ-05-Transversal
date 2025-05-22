<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chofer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SolicitudController extends Controller
{
    public function getChoferesCercanos(Request $request)
    {
        $latitud = $request->latitud;
        $longitud = $request->longitud;
        $radio = 20; // Radio en km

        // Fórmula para calcular la distancia
        // Multiplicamos por 111 para convertir grados a kilómetros (cifra aproximada)
        $choferes = DB::table('choferes')
            ->join('users', 'choferes.id_usuario', '=', 'users.id_usuario')
            ->where('choferes.estado', 'disponible')
            ->select(
                'choferes.id',
                'users.nombre',
                'choferes.latitud',
                'choferes.longitud',
                /*
                // Cálculo de la distancia aproximada (en km) entre un punto de referencia ($latitud, $longitud)
                // y la ubicación de cada chofer usando la fórmula de distancia ajustada 
                // para coordenadas geográficas.
                */

                DB::raw("
                    SQRT(
                        POW(($latitud - choferes.latitud) * 111, 2) + 
                        POW(($longitud - choferes.longitud) * 111 * COS(RADIANS($latitud)), 2)
                    ) AS distancia
                ")
            )
            ->having('distancia', '<=', $radio)
            ->orderBy('distancia')
            ->get();

        return response()->json($choferes);
    }
}
