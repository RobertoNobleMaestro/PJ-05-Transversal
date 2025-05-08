<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ChatViewController extends Controller
{
    public function index()
    {
        $user = Auth::user();
    
        // Buscar último gestor con el que haya hablado
        $ultimoMensaje = Message::where('user_id', $user->id_usuario)
            ->orderBy('created_at', 'desc')
            ->first();
    
        if ($ultimoMensaje) {
            $gestorId = $ultimoMensaje->gestor_id;
        } else {
            // Buscar un gestor al azar
            $gestor = User::whereHas('role', function ($q) {
                $q->where('nombre', 'gestor');
            })->inRandomOrder()->first();
    
            $gestorId = $gestor ? $gestor->id_usuario : null;
        }
    
        // Obtener todos los mensajes con ese gestor
        $messages = Message::where(function ($query) use ($user, $gestorId) {
            $query->where('user_id', $user->id_usuario)->where('gestor_id', $gestorId);
        })->orWhere(function ($query) use ($user, $gestorId) {
            $query->where('user_id', $gestorId)->where('gestor_id', $user->id_usuario);
        })->orderBy('created_at')->get();
    
        return view('chat.index', compact('messages', 'gestorId'));
    }
    public function listarConversaciones()
    {
        
        $gestorId = Auth::user()->id_usuario;
        
        $usuarios = Message::where('gestor_id', $gestorId)
            ->whereNotNull('user_id')
            ->join('users', 'users.id_usuario', '=', 'messages.user_id')
            ->select('users.id_usuario', 'users.nombre', DB::raw('MAX(messages.created_at) as ultima'))
            ->groupBy('users.id_usuario', 'users.nombre')
            ->orderByDesc('ultima')
            ->get();
    
        return view('gestor.chat.listar', compact('usuarios'));
    }
    

    public function verConversacion($id)
    {
        $gestorId = Auth::user()->id_usuario;
    
        $mensajes = Message::where('gestor_id', $gestorId)
            ->where('user_id', $id)
            ->orderBy('created_at')
            ->get();
    
        $usuario = \App\Models\User::findOrFail($id); 
    
        return view('gestor.chat.conversacion', [
            'mensajes' => $mensajes,
            'usuario' => $usuario,
            'id' => $id
        ]);
    }
    

public function eliminarMensaje($id)
{
    $message = Message::findOrFail($id);
    $message->delete();

    return back()->with('success', 'Mensaje eliminado');
}

}
