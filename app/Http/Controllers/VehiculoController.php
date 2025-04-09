<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;

class VehiculoController extends Controller
{
    public function detalle($id)
    {
        $vehiculo = Vehiculo::with(['tipo', 'lugar', 'caracteristicas', 'valoraciones'])->findOrFail($id);
    
        return view('vehiculos.detalle_vehiculo', [
            'vehiculo' => $vehiculo
        ]);
    }

    public function añadirAlCarrito($id)
{
    try {
        $vehiculo = Vehiculo::findOrFail($id);
        $usuarioId = auth()->id();

        if (!$usuarioId) {
            return response()->json(['success' => false, 'error' => 'Usuario no autenticado.'], 401);
        }

        // 1. Buscar o crear una reserva activa ("carrito") para este usuario
        $reserva = \App\Models\Reserva::firstOrCreate(
            [
                'estado' => 'pendiente',
                'id_usuario' => $usuarioId
            ],
            [
                'fecha_reserva' => now(),
                'total_precio' => 0, // se puede recalcular luego
                'id_lugar' => $vehiculo->id_lugar,
            ]
        );

        // 2. Insertar el vehículo en vehiculos_reservas si aún no está
        $existe = \App\Models\VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)
            ->where('id_reservas', $reserva->id_reservas)
            ->exists();

        if ($existe) {
            return response()->json(['success' => false, 'error' => 'Este vehículo ya está en tu carrito.']);
        }

        \App\Models\VehiculosReservas::create([
            'fecha_ini' => now()->toDateString(),
            'fecha_final' => now()->addDays(3)->toDateString(),
            'precio_unitario' => 100,
            'id_reservas' => $reserva->id_reservas,
            'id_vehiculos' => $vehiculo->id_vehiculos,
        ]);

        return response()->json(['success' => true]);

    } catch (\Exception $e) {
        return response()->json(['success' => false, 'error' => $e->getMessage()]);
    }
}

    
}
