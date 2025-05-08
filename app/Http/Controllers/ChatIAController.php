<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatIAController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string'
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'user', 'content' => $request->input('message')],
                ],
                'temperature' => 0.7
            ]);

            $responseData = $response->json();

            return response()->json([
                'reply' => $responseData['choices'][0]['message']['content'] ?? 'No pude generar una respuesta'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error al conectar con el servicio de IA: ' . $e->getMessage()
            ], 500);
        }
    }
}