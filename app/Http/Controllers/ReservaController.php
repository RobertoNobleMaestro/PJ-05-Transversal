<?php

namespace App\Http\Controllers;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  
use App\Models\Vehiculo;
use App\Models\VehiculosReservas;
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
        // Validación de los datos de entrada
        $request->validate([
            'id_vehiculos' => 'required|exists:vehiculos,id_vehiculos',
            'fecha_ini' => 'required|date',
            'fecha_final' => 'required|date|after_or_equal:fecha_ini'
        ]);
    
        // Obtener el vehículo
        $vehiculo = Vehiculo::findOrFail($request->id_vehiculos);
    
        // Buscar una reserva pendiente existente para este usuario
        $reserva = Reserva::where('estado', 'pendiente')
                          ->where('id_usuario', auth()->id())
                          ->first();
    
        // Si no existe una reserva pendiente, crear una nueva
        if (!$reserva) {
            // Calcular precio total
            $dias = now()->parse($request->fecha_ini)->diffInDays(now()->parse($request->fecha_final)); 
            $total = $vehiculo->precio_dia * ($dias + 1);  // Añadir 1 para incluir el último día
            
            // Crear la reserva
            $reserva = Reserva::create([
                'fecha_reserva' => now()->toDateString(),
                'total_precio' => $total,
                'estado' => 'pendiente',  // Estado pendiente
                'id_lugar' => $vehiculo->id_lugar,
                'id_usuario' => auth()->id(),
                'referencia_pago' => null
            ]);
        }

        // Verificar si el vehículo ya está en la reserva
        $existe = VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)
                                   ->where('id_reservas', $reserva->id_reservas)
                                   ->exists();
    
        if ($existe) {
            return response()->json(['alert' => [
                'icon' => 'warning',
                'title' => '¡Vehículo ya en el carrito!',
                'text' => 'Este vehículo ya está en tu carrito de compras.'
            ]]);
        }
    
        // Insertar el vehículo en vehiculos_reservas
        VehiculosReservas::create([
            'fecha_ini' => $request->fecha_ini,
            'fecha_final' => $request->fecha_final,
            'precio_unitario' => $vehiculo->precio_dia,  // Asumimos que el precio unitario es el precio por día
            'id_reservas' => $reserva->id_reservas,
            'id_vehiculos' => $vehiculo->id_vehiculos
        ]);
    
        // Recalcular el precio total de la reserva con todos los vehículos añadidos
        $total = 0;
        foreach ($reserva->vehiculosReservas as $vr) {
            $dias = max(1, \Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final));  // Calcular días de alquiler
            $total += $vr->vehiculo->precio_dia * $dias;  // Sumar el precio total
        }
    
        // Actualizar el total de la reserva
        $reserva->total_precio = $total;
        $reserva->save();  // Guardar los cambios
    
        // Responder con éxito
        return response()->json(['alert' => [
            'icon' => 'success',
            'title' => '¡Vehículo añadido al carrito!',
            'text' => 'El vehículo ha sido añadido a tu carrito con éxito.'
        ]]);
    }
    
    
    
}
