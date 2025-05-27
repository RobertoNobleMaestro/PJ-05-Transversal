<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateVehiculosPreciosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Actualizar los vehículos existentes y añadir algunos nuevos con años variados
        $vehiculos = [
            // Vehículos de gama alta
            ['marca' => 'Ferrari', 'modelo' => 'F8 Tributo', 'año' => 2023, 'precio' => 320000, 'precio_dia' => 450],
            ['marca' => 'Lamborghini', 'modelo' => 'Aventador', 'año' => 2022, 'precio' => 380000, 'precio_dia' => 500],
            ['marca' => 'Porsche', 'modelo' => '911 Turbo S', 'año' => 2024, 'precio' => 240000, 'precio_dia' => 350],
            ['marca' => 'McLaren', 'modelo' => '720S', 'año' => 2021, 'precio' => 290000, 'precio_dia' => 400],
            ['marca' => 'Aston Martin', 'modelo' => 'DBS Superleggera', 'año' => 2020, 'precio' => 330000, 'precio_dia' => 420],
            
            // Vehículos de gama media-alta
            ['marca' => 'Mercedes-Benz', 'modelo' => 'AMG GT', 'año' => 2022, 'precio' => 140000, 'precio_dia' => 250],
            ['marca' => 'BMW', 'modelo' => 'M8 Competition', 'año' => 2023, 'precio' => 130000, 'precio_dia' => 230],
            ['marca' => 'Audi', 'modelo' => 'R8', 'año' => 2021, 'precio' => 150000, 'precio_dia' => 260],
            ['marca' => 'Jaguar', 'modelo' => 'F-Type R', 'año' => 2019, 'precio' => 110000, 'precio_dia' => 200],
            ['marca' => 'Maserati', 'modelo' => 'GranTurismo', 'año' => 2020, 'precio' => 120000, 'precio_dia' => 220],
            
            // Vehículos de gama media
            ['marca' => 'Tesla', 'modelo' => 'Model S', 'año' => 2022, 'precio' => 90000, 'precio_dia' => 150],
            ['marca' => 'Lexus', 'modelo' => 'LC 500', 'año' => 2021, 'precio' => 95000, 'precio_dia' => 160],
            ['marca' => 'BMW', 'modelo' => 'M5', 'año' => 2020, 'precio' => 85000, 'precio_dia' => 140],
            ['marca' => 'Audi', 'modelo' => 'RS7', 'año' => 2019, 'precio' => 80000, 'precio_dia' => 130],
            ['marca' => 'Mercedes-Benz', 'modelo' => 'CLS 53 AMG', 'año' => 2018, 'precio' => 75000, 'precio_dia' => 120],
            
            // Vehículos más antiguos para demostrar la amortización
            ['marca' => 'Porsche', 'modelo' => 'Cayman S', 'año' => 2017, 'precio' => 60000, 'precio_dia' => 100],
            ['marca' => 'BMW', 'modelo' => 'M3', 'año' => 2016, 'precio' => 55000, 'precio_dia' => 90],
            ['marca' => 'Audi', 'modelo' => 'TT RS', 'año' => 2015, 'precio' => 50000, 'precio_dia' => 80],
            ['marca' => 'Mercedes-Benz', 'modelo' => 'C63 AMG', 'año' => 2014, 'precio' => 45000, 'precio_dia' => 70],
            ['marca' => 'Nissan', 'modelo' => 'GT-R', 'año' => 2013, 'precio' => 40000, 'precio_dia' => 60],
        ];
        
        // Asignar lugares, tipos y parkings aleatoriamente (asumiendo que ya existen)
        $lugares = \App\Models\Lugar::all()->pluck('id_lugar')->toArray();
        $tipos = \App\Models\Tipo::all()->pluck('id_tipo')->toArray();
        $parkings = \App\Models\Parking::all()->pluck('id')->toArray();
        
        // Añadir matrículas aleatorias
        $letras = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'R', 'S', 'T', 'V', 'W', 'X', 'Y', 'Z'];
        
        foreach ($vehiculos as $vehiculo) {
            // Generar matrícula aleatoria si no se proporcionó
            if (!isset($vehiculo['matricula'])) {
                $numeros = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
                $letras_mat = $letras[array_rand($letras)] . $letras[array_rand($letras)] . $letras[array_rand($letras)];
                $matricula = $numeros . $letras_mat;
                $vehiculo['matricula'] = $matricula;
            }
            
            // Asignar valores aleatorios para lugar, tipo y parking
            $vehiculo['id_lugar'] = $lugares[array_rand($lugares)];
            $vehiculo['id_tipo'] = $tipos[array_rand($tipos)];
            $vehiculo['parking_id'] = $parkings[array_rand($parkings)];
            $vehiculo['disponibilidad'] = 1; // Disponible por defecto
            $vehiculo['kilometraje'] = rand(1000, 50000);
            
            // Insertar nuevos vehículos en lugar de intentar actualizar los existentes
            try {
                \App\Models\Vehiculo::create($vehiculo);
            } catch (\Exception $e) {
                // Ignorar errores de inserción (como claves duplicadas)
            }
        }
        
        // Actualizar también los vehículos existentes que no tienen precio
        $vehiculosExistentes = \App\Models\Vehiculo::whereNull('precio')->orWhere('precio', 0)->get();
        
        foreach ($vehiculosExistentes as $vehiculo) {
            // Asignar un precio basado en la marca y el año
            $precioBase = 0;
            
            if (stripos($vehiculo->marca, 'ferrari') !== false || stripos($vehiculo->marca, 'lamborghini') !== false) {
                $precioBase = rand(200000, 400000);
            } elseif (stripos($vehiculo->marca, 'porsche') !== false || stripos($vehiculo->marca, 'aston') !== false) {
                $precioBase = rand(150000, 250000);
            } elseif (stripos($vehiculo->marca, 'bmw') !== false || stripos($vehiculo->marca, 'mercedes') !== false || stripos($vehiculo->marca, 'audi') !== false) {
                $precioBase = rand(80000, 150000);
            } else {
                $precioBase = rand(30000, 80000);
            }
            
            $vehiculo->precio = $precioBase;
            $vehiculo->save();
        }
        
        // Actualizar los parkings con metros cuadrados y precio por metro cuadrado
        $parkings = \App\Models\Parking::all();
        
        foreach ($parkings as $parking) {
            // Metros cuadrados aleatorios entre 500 y 3000
            $metrosCuadrados = rand(500, 3000);
            
            // Precio por metro cuadrado entre 800 y 2000 euros
            $precioMetroCuadrado = rand(800, 2000);
            
            $parking->metros_cuadrados = $metrosCuadrados;
            $parking->precio_metro_cuadrado = $precioMetroCuadrado;
            $parking->save();
        }
    }
}
