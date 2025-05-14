<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{   

    public function getMessages($userId)
    {
        $authId = Auth::user()->id_usuario;

        $messages = Message::where(function ($query) use ($authId, $userId) {
            $query->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($authId, $userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $authId);
        })->orderBy('created_at')->get();

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
            'gestor_id'    => 'required_if:sender_type,user|exists:users,id_usuario',
            'user_id'      => 'required_if:sender_type,gestor|exists:users,id_usuario',
            'message'      => 'required|string',
            'sender_type'  => 'required|in:user,gestor'
        ]);
    
        $data = [
            'message'      => $request->message,
            'sender_type'  => $request->sender_type,
            'user_id'      => $request->user_id,
            'gestor_id'    => $request->gestor_id,
        ];
    
        $msg = Message::create($data);
    
        return response()->json(['message' => 'Mensaje enviado', 'data' => $msg]);
    }
    public function stream($id_usuario)
    {
        if (!Auth::check()) {
            abort(403);
        }
    
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
    
        $authUser = Auth::user();
        $isGestor = $authUser->rol === 'gestor';
    
        $userId = $isGestor ? $id_usuario : $authUser->id_usuario;
        $gestorId = $isGestor ? $authUser->id_usuario : $id_usuario;
    
        // Obtener el último mensaje del otro participante
        $mensaje = \App\Models\Message::where('user_id', $userId)
            ->where('gestor_id', $gestorId)
            ->where('sender_type', $isGestor ? 'user' : 'gestor')
            ->orderBy('id', 'desc')
            ->first();
    
        // Solo envía el mensaje si es más reciente que el último recibido por el frontend
        if ($mensaje) {
            echo "data: " . json_encode([
                'message' => $mensaje->message,
                'created_at' => $mensaje->created_at->format('H:i d/m/Y'),
                'id' => $mensaje->id,
            ]) . "\n\n";
        }
    
        flush();
    }
    


}
