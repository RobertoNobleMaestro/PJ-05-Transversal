<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Models\Vehiculo;

class ReservaController extends Controller
{
    public function reservasPorVehiculo($id)
    {
        $reservas = DB::table('vehiculos_reservas')
            ->where('id_vehiculos', $id)
            ->get();
            // $reservas = DB::table('vehiculos_reservas as vr')
            // ->join('reservas as r', 'vr.id_reservas', '=', 'r.id_reservas')
            // ->where('vr.id_vehiculos', $id)
            // ->where('r.estado', 'confirmada')
            // ->get();
    
            $eventos = $reservas->map(function ($reserva) {
                return [
                    'title' => 'Reservado',
                    'start' => $reserva->fecha_ini,
                    'end' => date('Y-m-d', strtotime($reserva->fecha_final . ' +1 day')), // ← aquí el ajuste
                    'color' => '#dc3545'
                ];
            });
            
    
        return response()->json($eventos);
    }
    public function crearReserva(Request $request)
    {
        $request->validate([
            'id_vehiculos' => 'required|exists:vehiculos,id_vehiculos',
            'fecha_ini' => 'required|date',
            'fecha_final' => 'required|date|after_or_equal:fecha_ini'
        ]);
    
        $vehiculo = Vehiculo::findOrFail($request->id_vehiculos);
    
        // Calcular precio total (puedes mejorar esto si hay descuentos, etc.)
        $dias = now()->parse($request->fecha_ini)->diffInDays(now()->parse($request->fecha_final)) + 1;
        $total = $vehiculo->precio_dia * $dias;        
    
        // Crear reserva principal
        $reservaId = DB::table('reservas')->insertGetId([
            'fecha_reserva' => now()->toDateString(),
            'total_precio' => $total,
            'estado' => 'pendiente', // o 'confirmada', etc.
            'id_lugar' => $vehiculo->id_lugar,
            'id_usuario' => auth()->id(), // o $request->id_usuario si lo mandas desde JS
            'referencia_pago' => null,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        // Crear entrada en vehiculos_reservas
        DB::table('vehiculos_reservas')->insert([
            'id_reservas' => $reservaId,
            'id_vehiculos' => $request->id_vehiculos,
            'fecha_ini' => $request->fecha_ini,
            'fecha_final' => $request->fecha_final,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    
        return response()->json(['success' => true]);
    }
    
    
}
