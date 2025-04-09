<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Reserva;
use App\Models\VehiculosReservas;
use Illuminate\Http\Request;

class VehiculoController extends Controller
{
    public function detalle($id)
    {
        // Cargar detalles del vehículo, sus características, valoraciones, etc.
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
                return response()->json(['alert' => [
                    'icon' => 'error',
                    'title' => 'Usuario no autenticado',
                    'text' => 'Debes iniciar sesión para añadir vehículos al carrito.'
                ]]);
            }

            // 1. Buscar o crear una reserva activa ("carrito") para este usuario
            $reserva = Reserva::firstOrCreate(
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

            VehiculosReservas::create([
                'fecha_ini' => now()->toDateString(),
                'fecha_final' => now()->addDays(3)->toDateString(),
                'precio_unitario' => 100,  // Este precio puede venir del vehículo
                'id_reservas' => $reserva->id_reservas,
                'id_vehiculos' => $vehiculo->id_vehiculos,
            ]);

            return response()->json(['alert' => [
                'icon' => 'success',
                'title' => '¡Vehículo añadido al carrito!',
                'text' => 'El vehículo ha sido añadido a tu carrito con éxito.'
            ]]);

        } catch (\Exception $e) {
            return response()->json(['alert' => [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Ocurrió un problema al intentar añadir el vehículo al carrito.'
            ]]);
        }
    }
}
