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
    

}
