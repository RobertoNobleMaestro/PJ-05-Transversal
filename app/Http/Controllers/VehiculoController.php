<?php

namespace App\Http\Controllers;

use App\Models\Vehiculo;
use App\Models\Reserva;
use App\Models\VehiculosReservas;
use Illuminate\Http\Request;
use App\Models\ImagenVehiculo;

class VehiculoController extends Controller
{
    public function detalle($id)
    {
        $vehiculo = Vehiculo::with(['tipo', 'lugar', 'caracteristicas', 'valoraciones', 'vehiculosReservas.reserva', 'imagenes'])
        ->findOrFail($id);
    

        $precioUnitario = $vehiculo->vehiculosReservas
            ->where('fecha_final', '>=', now())
            ->first()->precio_unitario ?? $vehiculo->precio_unitario;

        return view('vehiculos.detalle_vehiculo', [
            'vehiculo' => $vehiculo,
            'precio_unitario' => $precioUnitario,
            'imagenes' => $vehiculo->imagenes
        ]);
    }


    public function añadirAlCarrito($id)
    {
        try {
            $vehiculo = Vehiculo::findOrFail($id);
            $usuarioId = auth()->id();

            // Depurador para ver el vehículo y el ID del usuario
            dd($vehiculo, $usuarioId);

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
                    'total_precio' => 0, // Se puede recalcular luego
                    'id_lugar' => $vehiculo->id_lugar,
                ]
            );

            // Depurador para ver la reserva creada o encontrada
            dd($reserva);

            // 2. Obtener el precio unitario del vehículo desde la tabla vehiculos_reservas
            $precioUnitario = VehiculosReservas::where('id_vehiculos', $vehiculo->id_vehiculos)
                ->where('id_reservas', $reserva->id_reservas)
                ->first()->precio_unitario ?? 100; // Usamos 100 como valor por defecto si no se encuentra el precio.

            // Depurador para ver el precio unitario
            dd($precioUnitario);

            // 3. Insertar el vehículo en vehiculos_reservas si aún no está
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

            // Insertar la relación con el precio unitario obtenido
            VehiculosReservas::create([
                'fecha_ini' => now()->toDateString(),
                'fecha_final' => now()->addDays(3)->toDateString(),
                'precio_unitario' => $precioUnitario,  // Usar el precio unitario de la reserva
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
