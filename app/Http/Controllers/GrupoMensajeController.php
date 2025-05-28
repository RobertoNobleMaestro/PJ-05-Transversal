<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Grupo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GrupoMensajeController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'message' => 'required|string|max:1000'
        ]);

        $mensaje = Message::create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'sender_type' => 'user',
            'grupo_id' => $request->grupo_id
        ]);

        // Obtener el nombre del remitente
        $senderName = Auth::user()->nombre;

        return response()->json([
            'success' => true,
            'message' => [
                'grupo_id' => $mensaje->grupo_id,
                'sender_name' => $senderName,
                'message' => $mensaje->message,
                'created_at' => $mensaje->created_at->format('Y-m-d H:i:s'),
                'is_own' => false
            ]
        ]);
    }

    public function obtener(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'last_id' => 'nullable|integer'
        ]);

        $query = Message::where('grupo_id', $request->grupo_id)
            ->with('sender:id_usuario,nombre')
            ->orderBy('created_at', 'desc')
            ->limit(50);

        if ($request->has('last_id')) {
            $query->where('id', '<', $request->last_id);
        }

        $mensajes = $query->get()->reverse();

        $mensajesFormateados = $mensajes->map(function ($mensaje) {
            return [
                'grupo_id' => $mensaje->grupo_id,
                'sender_name' => $mensaje->sender->nombre,
                'message' => $mensaje->message,
                'created_at' => $mensaje->created_at->format('Y-m-d H:i:s'),
                'is_own' => $mensaje->user_id === Auth::id()
            ];
        });

        return response()->json([
            'success' => true,
            'messages' => $mensajesFormateados
        ]);
    }
} 