<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RutaController extends Controller
{
    public function obtenerRuta(Request $request)
    {
        try {
            $origen_lat = $request->query('origen_lat');
            $origen_lng = $request->query('origen_lng');
            $destino_lat = $request->query('destino_lat');
            $destino_lng = $request->query('destino_lng');

            if (!$origen_lat || !$origen_lng || !$destino_lat || !$destino_lng) {
                return response()->json([
                    'success' => false,
                    'message' => 'Faltan coordenadas necesarias'
                ], 400);
            }

            $apiKey = env('OPENROUTE_API_KEY', '5b3ce3597851110001cf6248e4c8c0c0c0c84c0c0c0c0c0c0c0c0c0c0c0c0c0');
            
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Accept' => 'application/json, application/geo+json, application/gpx+xml, img/png; charset=utf-8',
            ])->get('https://api.openrouteservice.org/v2/directions/driving-car', [
                'start' => "$origen_lng,$origen_lat",
                'end' => "$destino_lng,$destino_lat"
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return response()->json([
                    'success' => true,
                    'route' => [
                        'coordinates' => $data['features'][0]['geometry']['coordinates']
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la ruta'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ], 500);
        }
    }
} 