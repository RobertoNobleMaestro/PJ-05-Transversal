<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use Illuminate\Support\Facades\DB;

class ReservaController extends Controller
{
    public function reservasPorVehiculo($id)
    {
        $reservas = DB::table('vehiculos_reservas')
            ->where('id_vehiculos', $id)
            ->get();
    
        $eventos = $reservas->map(function ($reserva) {
            return [
                'title' => 'Reservado',
                'start' => $reserva->fecha_ini,
                'end' => $reserva->fecha_final,
                'color' => '#dc3545'
            ];
        });
    
        return response()->json($eventos);
    }
    
}
