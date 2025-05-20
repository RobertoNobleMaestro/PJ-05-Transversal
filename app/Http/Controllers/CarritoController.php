<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;
use App\Models\VehiculosReservas;
use App\Models\Valoracion;
use Carbon\Carbon;

class CarritoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener vehículos con al menos una reserva pendiente del usuario
        $vehiculos = Vehiculo::with([
            'imagenes', // Aseguramos que se carguen las imágenes
            'tipo',
            'caracteristicas',
            'lugar',
            'vehiculosReservas.reserva.pago',
            'vehiculosReservas.reserva.lugar',
        ])
        ->whereHas('vehiculosReservas.reserva', function ($query) use ($user) {
            $query->where('estado', 'pendiente')
                  ->where('id_usuario', $user->id_usuario);
        })
        ->get();

        $vehiculosConInfo = [];

        foreach ($vehiculos as $vehiculo) {
            foreach ($vehiculo->vehiculosReservas as $vr) {
                $reserva = $vr->reserva;

                if ($reserva && $reserva->estado === 'pendiente' && $reserva->id_usuario == $user->id_usuario) {
                    $vehiculoData = $vehiculo->toArray();

                    // Eliminar la relación innecesaria
                    unset($vehiculoData['vehiculos_reservas']);

                    // Añadir esta línea:
                    $vehiculoData['id_vehiculos_reservas'] = $vr->id_vehiculos_reservas;

                    // Siempre incluir info de reserva + total_precio
                    $vehiculoData['reserva'] = [
                        'id_reserva' => $reserva->id_reservas,
                        'fecha_reserva' => $reserva->fecha_reserva,
                        'estado' => $reserva->estado,
                        'lugar' => $reserva->lugar->nombre ?? null,
                        'total_precio' => (float) $reserva->total_precio, // ✅ Esto asegura que esté presente
                    ];

                    // Si hay pago, incluir info del pago también
                    if ($reserva->pago) {
                        $vehiculoData['pago'] = [
                            'estado_pago' => $reserva->pago->estado_pago,
                            'monto_pagado' => (float) $reserva->pago->monto_pagado,
                            'total_precio' => (float) $reserva->pago->total_precio,
                            'moneda' => $reserva->pago->moneda,
                        ];
                    }

                    $vehiculosConInfo[] = $vehiculoData;
                    break; // para evitar múltiples reservas por vehículo
                }
            }
        }

        return response()->json($vehiculosConInfo);
    }

    public function eliminarReserva($idVehiculoReserva)
    {
        $user = Auth::user();

        DB::beginTransaction();

        try {
            // Buscar la relación vehiculos_reservas y verificar que pertenece a una reserva del usuario
            $vehiculoReserva = VehiculosReservas::with(['reserva', 'vehiculo'])
                ->where('id_vehiculos_reservas', $idVehiculoReserva)
                ->first();

            if (!$vehiculoReserva || !$vehiculoReserva->reserva || $vehiculoReserva->reserva->id_usuario != $user->id_usuario) {
                return response()->json(['error' => 'Reserva no encontrada o no autorizada'], 404);
            }

            $reserva = $vehiculoReserva->reserva;
            $fechaIni = Carbon::parse($vehiculoReserva->fecha_ini);
            $fechaFin = Carbon::parse($vehiculoReserva->fecha_final);
            $dias = $fechaIni->diffInDays($fechaFin) + 1;
            $precioPorDia = $vehiculoReserva->vehiculo->precio_dia;
            $precioVehiculo = $precioPorDia * $dias;
            $reserva->total_precio -= $precioVehiculo;
            if ($reserva->total_precio < 0) $reserva->total_precio = 0;
            $reserva->save();

            // Eliminar la relación vehiculos_reservas
            $vehiculoReserva->delete();

            // Si la reserva ya no tiene vehículos asociados, eliminar la reserva y valoraciones
            $vehiculosRestantes = VehiculosReservas::where('id_reservas', $reserva->id_reservas)->count();
            if ($vehiculosRestantes == 0) {
                Valoracion::where('id_reservas', $reserva->id_reservas)->delete();
                $reserva->delete();
            }

            DB::commit();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al eliminar el vehículo de la reserva: ' . $e->getMessage()], 500);
        }
    }

    public function getCartCount()
    {
        $user = Auth::user();
        $count = Vehiculo::whereHas('vehiculosReservas.reserva', function ($query) use ($user) {
            $query->where('estado', 'pendiente')
                  ->where('id_usuario', $user->id_usuario);
        })->count();

        return response()->json(['count' => $count]);
    }
}
