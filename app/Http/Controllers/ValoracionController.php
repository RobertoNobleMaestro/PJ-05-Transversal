<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Valoracion;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ValoracionController extends Controller
{
    // Crear una nueva valoración
    public function store(Request $request)
    {
        $request->validate([
            'id_vehiculos' => 'required|exists:vehiculos,id_vehiculos',
            'valoracion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:500',
        ]);

        // Verificar si el usuario ha reservado el vehículo
        $reserva = DB::table('vehiculos_reservas')
            ->join('reservas', 'vehiculos_reservas.id_reservas', '=', 'reservas.id_reservas')
            ->where('reservas.id_usuario', Auth::id())
            ->where('vehiculos_reservas.id_vehiculos', $request->id_vehiculos)
            ->where('vehiculos_reservas.fecha_final', '<', Carbon::now())
            ->first();

        if (!$reserva) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes valorar este vehículo porque no has completado una reserva o la reserva aún no ha finalizado.'
            ], 403);
        }

        $valoracion = Valoracion::create([
            'id_usuario' => Auth::id(),
            'id_reservas' => $reserva->id_reservas, 
            'valoracion' => $request->valoracion,
            'comentario' => $request->comentario,
        ]);
        

        return response()->json([
            'success' => true,
            'message' => 'Valoración creada con éxito.',
            'valoracion' => $valoracion
        ]);
    }

    // Editar una valoración existente
    public function update(Request $request, $id)
    {
        $request->validate([
            'valoracion' => 'required|integer|min:1|max:5',
            'comentario' => 'required|string|max:500',
        ]);

        $valoracion = Valoracion::findOrFail($id);

        // Verificar si el usuario es el propietario de la valoración
        if ($valoracion->id_usuario !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para editar esta valoración.'
            ], 403);
        }

        // Actualizar la valoración
        $valoracion->update([
            'valoracion' => $request->valoracion,
            'comentario' => $request->comentario,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Valoración actualizada con éxito.',
            'valoracion' => $valoracion
        ]);
    }

    // Eliminar una valoración
    public function destroy($id)
    {
        $valoracion = Valoracion::findOrFail($id);

        // Verificar si el usuario es el propietario de la valoración
        if ($valoracion->id_usuario !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar esta valoración.'
            ], 403);
        }

        // Eliminar la valoración
        $valoracion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Valoración eliminada con éxito.'
        ]);
    }
}
