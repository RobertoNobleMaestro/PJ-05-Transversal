<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificacionController extends Controller
{
    public function getCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        try {
            $count = DB::table('solicitudes')
                ->where('id_cliente', Auth::id())
                ->where('estado', 'aceptada')
                ->where('notificacion_leida', false)
                ->count();

            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            \Log::error('Error al obtener contador de notificaciones: ' . $e->getMessage());
            return response()->json(['count' => 0]);
        }
    }

    public function marcarComoLeida($id)
    {
        DB::table('solicitudes')
            ->where('id', $id)
            ->where('id_cliente', Auth::id())
            ->update(['notificacion_leida' => true]);

        return response()->json(['success' => true]);
    }
} 