<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateParkingPricesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder updates the parking entries with realistic price variations
     * based on location, prestige, and demand factors.
     */
    public function run(): void
    {
        // Obtener todos los parkings existentes
        $parkings = DB::table('parking')->get();
        
        foreach ($parkings as $parking) {
            // Calcular metros cuadrados basado en el número de plazas
            // Estimamos ~25m² por plaza (incluye espacio de maniobra, accesos, etc.)
            $metrosCuadrados = $parking->plazas * 25;
            
            // Determinar precio base por metro cuadrado según la ciudad
            $precioBase = 0;
            
            // Si el nombre contiene Barcelona
            if (strpos($parking->nombre, 'Barcelona') !== false) {
                // Barcelona tiene los precios más altos
                $precioBase = rand(1100, 1400); // Entre 1.100€ y 1.400€ por m²
                
                // Ajustes específicos por zona
                if (strpos($parking->nombre, 'Centro') !== false) {
                    $precioBase += 200; // Centro de Barcelona premium
                } elseif (strpos($parking->nombre, 'Diagonal') !== false) {
                    $precioBase += 150; // Zona comercial premium
                } elseif (strpos($parking->nombre, 'Sagrada Familia') !== false) {
                    $precioBase += 100; // Zona turística premium
                }
            }
            // Si el nombre contiene Madrid
            elseif (strpos($parking->nombre, 'Madrid') !== false) {
                // Madrid tiene precios medios-altos
                $precioBase = rand(1000, 1250); // Entre 1.000€ y 1.250€ por m²
                
                // Ajustes específicos por zona
                if (strpos($parking->nombre, 'Sol') !== false || 
                    strpos($parking->nombre, 'Gran Vía') !== false) {
                    $precioBase += 200; // Centro de Madrid premium
                } elseif (strpos($parking->nombre, 'Bernabéu') !== false) {
                    $precioBase += 150; // Zona deportiva premium
                } elseif (strpos($parking->nombre, 'Retiro') !== false) {
                    $precioBase += 50; // Zona verde premium
                }
            }
            // Si el nombre contiene Valencia
            elseif (strpos($parking->nombre, 'Valencia') !== false) {
                // Valencia tiene precios más económicos
                $precioBase = rand(800, 1000); // Entre 800€ y 1.000€ por m²
                
                // Ajustes específicos por zona
                if (strpos($parking->nombre, 'Centro') !== false) {
                    $precioBase += 100; // Centro de Valencia premium
                } elseif (strpos($parking->nombre, 'Ciudad de las Artes') !== false) {
                    $precioBase += 75; // Zona turística premium
                }
            }
            // Para cualquier otro parking sin ciudad identificada
            else {
                $precioBase = rand(900, 1100); // Precio base genérico
            }
            
            // Pequeña variación aleatoria adicional para más realismo (-5% a +5%)
            $variacion = $precioBase * (rand(-50, 50) / 1000);
            $precioFinal = $precioBase + $variacion;
            
            // Actualizar el parking con metros cuadrados y precio por metro cuadrado
            DB::table('parking')
                ->where('id', $parking->id)
                ->update([
                    'metros_cuadrados' => $metrosCuadrados,
                    'precio_metro_cuadrado' => $precioFinal
                ]);
                
            // Mostrar información del parking actualizado
            $this->command->info(
                "Actualizado: {$parking->nombre} - " .
                "{$metrosCuadrados}m² a {$precioFinal}€/m² = " .
                number_format($metrosCuadrados * $precioFinal, 2, ',', '.') . "€"
            );
        }
        
        $this->command->info('Precios de parkings actualizados correctamente con variaciones realistas.');
    }
}
