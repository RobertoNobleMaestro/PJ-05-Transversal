<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatIAController extends Controller
{
    public function responder(Request $request)
    {
        $mensaje = $request->input('mensaje');
    
        try {
            $response = Http::withToken(env('OPENAI_API_KEY'))
                ->withOptions([
                    'verify' => false // SOLO PARA DESARROLLO, NO USAR EN PRODUCCIÓN
                ])
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'Eres un asistente para ayudar con preguntas sobre alquiler de coches.'],
                        ['role' => 'user', 'content' => $mensaje],
                    ],
                ]);
    
            $json = $response->json();
    
            return response()->json([
                'respuesta' => $json['choices'][0]['message']['content'] ?? 'Error: sin contenido'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'respuesta' => 'Error al contactar con OpenAI: ' . $e->getMessage()
            ], 500);
        }
    }
}