<?php

namespace App\Http\Controllers;

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
        try {
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
                $fecha_inicio = now()->parse($request->fecha_ini);
                $fecha_final = now()->parse($request->fecha_final);
    
                $dias = $fecha_inicio->diffInDays($fecha_final) + 1;
                $total = $vehiculo->precio_dia * $dias;
    
                // Crear la reserva
                $reserva = Reserva::create([
                    'fecha_reserva' => now()->toDateString(),
                    'total_precio' => $total,
                    'estado' => 'pendiente',
                    'id_lugar' => $vehiculo->id_lugar,
                    'id_usuario' => auth()->id(),
                    'referencia_pago' => null
                ]);
            } else {
                // Calcular el precio del nuevo vehículo
                $dias = max(1, \Carbon\Carbon::parse($request->fecha_ini)->diffInDays($request->fecha_final) + 1);
                $nuevoPrecio = $vehiculo->precio_dia * $dias;
    
                // Sumar el nuevo precio al total existente
                $total = $reserva->total_precio + $nuevoPrecio;
    
                // Actualizar el total de la reserva
                $reserva->total_precio = $total;
                $reserva->save();
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
                'precio_unitario' => $vehiculo->precio_dia,
                'id_reservas' => $reserva->id_reservas,
                'id_vehiculos' => $vehiculo->id_vehiculos
            ]);
    
            return response()->json(['alert' => [
                'icon' => 'success',
                'title' => '¡Vehículo añadido al carrito!',
                'text' => 'El vehículo ha sido añadido a tu carrito con éxito.'
            ]]);
    
        } catch (\Exception $e) {
            // Capturamos el error y lo mostramos en los logs para depuración
            return response()->json(['alert' => [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un problema al intentar añadir el vehículo al carrito.'
            ]]);
        }
    }
    
}
