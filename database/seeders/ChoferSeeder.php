<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChoferSeeder extends Seeder
{
    public function run(): void
    {
        // Coordenadas por sede
        $coordenadasSede = [
            'Barcelona' => ['lat' => 41.3851, 'lng' => 2.1734],
            'Madrid' => ['lat' => 40.4168, 'lng' => -3.7038],
            'Valencia' => ['lat' => 39.4699, 'lng' => -0.3763]
        ];

        // Obtener todos los usuarios con rol de chofer (id_roles = 6)
        $choferes = DB::table('users')
            ->where('id_roles', 6)
            ->get();

        // Para cada chofer, crear un registro en la tabla choferes
        foreach ($choferes as $chofer) {
            // Extraer la sede del email
            $partes = explode('@', $chofer->email);
            $username = $partes[0]; // chofer.barcelona1
            $partesSede = explode('.', $username);
            $sedeConNumero = $partesSede[1]; // barcelona1
            preg_match('/([a-zA-Z]+)/', $sedeConNumero, $matches);
            $sede = isset($matches[1]) ? ucfirst($matches[1]) : 'Central';

            // Obtener coordenadas base de la sede
            $coords = $coordenadasSede[$sede] ?? ['lat' => 40.4168, 'lng' => -3.7038]; // Madrid como fallback

            // Añadir una pequeña variación aleatoria para que no estén todos en el mismo punto
            $latitud = $coords['lat'] + (rand(-100, 100) / 10000);
            $longitud = $coords['lng'] + (rand(-100, 100) / 10000);

            DB::table('choferes')->insert([
                'id_usuario' => $chofer->id_usuario,
                'latitud' => $latitud,
                'longitud' => $longitud,
                'estado' => 'disponible',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
} 