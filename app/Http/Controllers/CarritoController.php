<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Vehiculo;

class CarritoController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Obtener vehículos con al menos una reserva pendiente del usuario
        $vehiculos = Vehiculo::with([
            'imagenes',
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
}
