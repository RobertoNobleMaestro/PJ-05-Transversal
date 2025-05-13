<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;

class VehiculoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('vehiculos')->insert([
            // 30 Coches
            ['id_vehiculos' => 1, 'precio_dia' => rand(20,150), 'marca' => 'Toyota', 'modelo' => 'Corolla', 'año' => 2024, 'kilometraje' => 41733,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 2, 'precio_dia' => rand(20,150), 'marca' => 'Volkswagen', 'modelo' => 'Golf', 'año' => 2020, 'kilometraje' => 62124,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 3, 'precio_dia' => rand(20,150), 'marca' => 'Ford', 'modelo' => 'Focus', 'año' => 2020, 'kilometraje' => 74116,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 4, 'precio_dia' => rand(20,150), 'marca' => 'Renault', 'modelo' => 'Clio', 'año' => 2019, 'kilometraje' => 35532,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 5, 'precio_dia' => rand(20,150), 'marca' => 'Peugeot', 'modelo' => '308', 'año' => 2023, 'kilometraje' => 23938,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 6, 'precio_dia' => rand(20,150), 'marca' => 'Hyundai', 'modelo' => 'i30', 'año' => 2021, 'kilometraje' => 52000,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 7, 'precio_dia' => rand(20,150), 'marca' => 'Kia', 'modelo' => 'Ceed', 'año' => 2022, 'kilometraje' => 33500,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 8, 'precio_dia' => rand(20,150), 'marca' => 'Seat', 'modelo' => 'Leon', 'año' => 2020, 'kilometraje' => 61200,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 9, 'precio_dia' => rand(20,150), 'marca' => 'Mazda', 'modelo' => '3', 'año' => 2019, 'kilometraje' => 68500,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 10, 'precio_dia' => rand(20,150), 'marca' => 'Skoda', 'modelo' => 'Octavia', 'año' => 2024, 'kilometraje' => 12500,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 11, 'precio_dia' => rand(20,150), 'marca' => 'Opel', 'modelo' => 'Astra', 'año' => 2023, 'kilometraje' => 20100,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 12, 'precio_dia' => rand(20,150), 'marca' => 'Citroen', 'modelo' => 'C4', 'año' => 2021, 'kilometraje' => 48900,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 13, 'precio_dia' => rand(20,150), 'marca' => 'Honda', 'modelo' => 'Civic', 'año' => 2020, 'kilometraje' => 75200,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 14, 'precio_dia' => rand(20,150), 'marca' => 'Fiat', 'modelo' => 'Tipo', 'año' => 2018, 'kilometraje' => 83200,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 15, 'precio_dia' => rand(20,150), 'marca' => 'Nissan', 'modelo' => 'Pulsar', 'año' => 2022, 'kilometraje' => 29100,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 16, 'precio_dia' => rand(20,150), 'marca' => 'Chevrolet', 'modelo' => 'Cruze', 'año' => 2019, 'kilometraje' => 67900,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 17, 'precio_dia' => rand(20,150), 'marca' => 'Suzuki', 'modelo' => 'Swift', 'año' => 2020, 'kilometraje' => 41000,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 18, 'precio_dia' => rand(20,150), 'marca' => 'Mini', 'modelo' => 'Cooper', 'año' => 2023, 'kilometraje' => 13500,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 19, 'precio_dia' => rand(20,150), 'marca' => 'Subaru', 'modelo' => 'Impreza', 'año' => 2021, 'kilometraje' => 48900,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 20, 'precio_dia' => rand(20,150), 'marca' => 'Lada', 'modelo' => 'Vesta', 'año' => 2022, 'kilometraje' => 39200,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 21, 'precio_dia' => rand(20,150), 'marca' => 'Chery', 'modelo' => 'Arrizo 5', 'año' => 2023, 'kilometraje' => 11000,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 22, 'precio_dia' => rand(20,150), 'marca' => 'Geely', 'modelo' => 'Emgrand', 'año' => 2020, 'kilometraje' => 55900,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 23, 'precio_dia' => rand(20,150), 'marca' => 'Dacia', 'modelo' => 'Sandero', 'año' => 2019, 'kilometraje' => 64800,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 24, 'precio_dia' => rand(20,150), 'marca' => 'BYD', 'modelo' => 'Dolphin', 'año' => 2024, 'kilometraje' => 8000,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 25, 'precio_dia' => rand(20,150), 'marca' => 'MG', 'modelo' => 'MG5', 'año' => 2023, 'kilometraje' => 15400,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 26, 'precio_dia' => rand(20,150), 'marca' => 'Tata', 'modelo' => 'Tiago', 'año' => 2021, 'kilometraje' => 43500,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 27, 'precio_dia' => rand(20,150), 'marca' => 'Proton', 'modelo' => 'Saga', 'año' => 2022, 'kilometraje' => 37600,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 28, 'precio_dia' => rand(20,150), 'marca' => 'Perodua', 'modelo' => 'Myvi', 'año' => 2020, 'kilometraje' => 49200,  'id_lugar' => 3, 'id_tipo' => 1, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 29, 'precio_dia' => rand(20,150), 'marca' => 'Great Wall', 'modelo' => 'Voleex C30', 'año' => 2018, 'kilometraje' => 58900,  'id_lugar' => 1, 'id_tipo' => 1, 'parking_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 30, 'precio_dia' => rand(20,150), 'marca' => 'Zotye', 'modelo' => 'Z300', 'año' => 2021, 'kilometraje' => 32800,  'id_lugar' => 2, 'id_tipo' => 1, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            // 20 Motos
            ['id_vehiculos' => 31, 'precio_dia' => rand(20,150), 'marca' => 'Yamaha', 'modelo' => 'MT-07', 'año' => 2022, 'kilometraje' => 8500,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 32, 'precio_dia' => rand(20,150), 'marca' => 'Honda', 'modelo' => 'CBR500R', 'año' => 2021, 'kilometraje' => 9700,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 33, 'precio_dia' => rand(20,150), 'marca' => 'Kawasaki', 'modelo' => 'Z650', 'año' => 2020, 'kilometraje' => 12300,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 34, 'precio_dia' => rand(20,150), 'marca' => 'BMW', 'modelo' => 'G310R', 'año' => 2023, 'kilometraje' => 5000,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 35, 'precio_dia' => rand(20,150), 'marca' => 'Suzuki', 'modelo' => 'GSX-S750', 'año' => 2019, 'kilometraje' => 18400,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 36, 'precio_dia' => rand(20,150), 'marca' => 'Ducati', 'modelo' => 'Monster', 'año' => 2021, 'kilometraje' => 7100,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 37, 'precio_dia' => rand(20,150), 'marca' => 'Triumph', 'modelo' => 'Street Triple', 'año' => 2022, 'kilometraje' => 6300,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 38, 'precio_dia' => rand(20,150), 'marca' => 'KTM', 'modelo' => 'Duke 390', 'año' => 2020, 'kilometraje' => 7900,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 39, 'precio_dia' => rand(20,150), 'marca' => 'Harley-Davidson', 'modelo' => 'Iron 883', 'año' => 2018, 'kilometraje' => 11500,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 40, 'precio_dia' => rand(20,150), 'marca' => 'Aprilia', 'modelo' => 'RS 660', 'año' => 2023, 'kilometraje' => 4200,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 41, 'precio_dia' => rand(20,150), 'marca' => 'Benelli', 'modelo' => 'Leoncino', 'año' => 2022, 'kilometraje' => 6100,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 42, 'precio_dia' => rand(20,150), 'marca' => 'Husqvarna', 'modelo' => 'Svartpilen 401', 'año' => 2021, 'kilometraje' => 7000,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 43, 'precio_dia' => rand(20,150), 'marca' => 'Royal Enfield', 'modelo' => 'Meteor 350', 'año' => 2020, 'kilometraje' => 8300,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 44, 'precio_dia' => rand(20,150), 'marca' => 'CFMOTO', 'modelo' => '300NK', 'año' => 2023, 'kilometraje' => 3700,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 45, 'precio_dia' => rand(20,150), 'marca' => 'Zontes', 'modelo' => 'T310', 'año' => 2021, 'kilometraje' => 9200,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 46, 'precio_dia' => rand(20,150), 'marca' => 'Moto Guzzi', 'modelo' => 'V7 Stone', 'año' => 2020, 'kilometraje' => 7800,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 47, 'precio_dia' => rand(20,150), 'marca' => 'Voge', 'modelo' => '500R', 'año' => 2019, 'kilometraje' => 9700,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 48, 'precio_dia' => rand(20,150), 'marca' => 'SYM', 'modelo' => 'Wolf 250', 'año' => 2022, 'kilometraje' => 4500,  'id_lugar' => 2, 'id_tipo' => 2, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 49, 'precio_dia' => rand(20,150), 'marca' => 'Mash', 'modelo' => 'Two Fifty', 'año' => 2021, 'kilometraje' => 5300,  'id_lugar' => 1, 'id_tipo' => 2, 'parking_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 50, 'precio_dia' => rand(20,150), 'marca' => 'Lifan', 'modelo' => 'KPR 200', 'año' => 2020, 'kilometraje' => 10200,  'id_lugar' => 3, 'id_tipo' => 2, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            // 15 Furgonetas
            ['id_vehiculos' => 51, 'precio_dia' => rand(20,150), 'marca' => 'Mercedes-Benz', 'modelo' => 'Sprinter', 'año' => 2022, 'kilometraje' => 42300,  'id_lugar' => 1, 'id_tipo' => 3, 'parking_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 52, 'precio_dia' => rand(20,150), 'marca' => 'Ford', 'modelo' => 'Transit', 'año' => 2021, 'kilometraje' => 50200,  'id_lugar' => 2, 'id_tipo' => 3, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 53, 'precio_dia' => rand(20,150), 'marca' => 'Renault', 'modelo' => 'Master', 'año' => 2020, 'kilometraje' => 58800,  'id_lugar' => 3, 'id_tipo' => 3, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 54, 'precio_dia' => rand(20,150), 'marca' => 'Volkswagen', 'modelo' => 'Crafter', 'año' => 2023, 'kilometraje' => 27900,  'id_lugar' => 1, 'id_tipo' => 3, 'parking_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 55, 'precio_dia' => rand(20,150), 'marca' => 'Citroën', 'modelo' => 'Jumper', 'año' => 2022, 'kilometraje' => 39000,  'id_lugar' => 2, 'id_tipo' => 3, 'parking_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 56, 'precio_dia' => rand(20,150), 'marca' => 'Peugeot', 'modelo' => 'Boxer', 'año' => 2020, 'kilometraje' => 61200,  'id_lugar' => 3, 'id_tipo' => 3, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 57, 'precio_dia' => rand(20,150), 'marca' => 'Fiat', 'modelo' => 'Ducato', 'año' => 2021, 'kilometraje' => 48700,  'id_lugar' => 1, 'id_tipo' => 3, 'parking_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 58, 'precio_dia' => rand(20,150), 'marca' => 'Iveco', 'modelo' => 'Daily', 'año' => 2019, 'kilometraje' => 70400,  'id_lugar' => 2, 'id_tipo' => 3, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 59, 'precio_dia' => rand(20,150), 'marca' => 'Opel', 'modelo' => 'Movano', 'año' => 2023, 'kilometraje' => 21000,  'id_lugar' => 3, 'id_tipo' => 3, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 60, 'precio_dia' => rand(20,150), 'marca' => 'Nissan', 'modelo' => 'NV400', 'año' => 2020, 'kilometraje' => 64300,  'id_lugar' => 2, 'id_tipo' => 3, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 61, 'precio_dia' => rand(20,150), 'marca' => 'Hyundai', 'modelo' => 'H350', 'año' => 2021, 'kilometraje' => 35600,  'id_lugar' => 1, 'id_tipo' => 3, 'parking_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 62, 'precio_dia' => rand(20,150), 'marca' => 'Toyota', 'modelo' => 'Proace', 'año' => 2022, 'kilometraje' => 27400,  'id_lugar' => 3, 'id_tipo' => 3, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 63, 'precio_dia' => rand(20,150), 'marca' => 'MAN', 'modelo' => 'TGE', 'año' => 2023, 'kilometraje' => 18200,  'id_lugar' => 1, 'id_tipo' => 3, 'parking_id' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 64, 'precio_dia' => rand(20,150), 'marca' => 'Isuzu', 'modelo' => 'N-Series', 'año' => 2020, 'kilometraje' => 52000,  'id_lugar' => 2, 'id_tipo' => 3, 'parking_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 65, 'precio_dia' => rand(20,150), 'marca' => 'Maxus', 'modelo' => 'Deliver 9', 'año' => 2021, 'kilometraje' => 46800,  'id_lugar' => 3, 'id_tipo' => 3, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()],
            // 10 Camiones
            ['id_vehiculos' => 66, 'precio_dia' => rand(20,150), 'marca' => 'Volvo', 'modelo' => 'FL', 'año' => 2022, 'kilometraje' => 31200,  'id_lugar' => 1, 'id_tipo' => 4, 'parking_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 67, 'precio_dia' => rand(20,150), 'marca' => 'Mercedes-Benz', 'modelo' => 'Atego', 'año' => 2021, 'kilometraje' => 46800,  'id_lugar' => 2, 'id_tipo' => 4, 'parking_id' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 68, 'precio_dia' => rand(20,150), 'marca' => 'DAF', 'modelo' => 'LF', 'año' => 2020, 'kilometraje' => 52900,  'id_lugar' => 3, 'id_tipo' => 4, 'parking_id' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 69, 'precio_dia' => rand(20,150), 'marca' => 'Scania', 'modelo' => 'P-Series', 'año' => 2023, 'kilometraje' => 24800,  'id_lugar' => 1, 'id_tipo' => 4, 'parking_id' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 70, 'precio_dia' => rand(20,150), 'marca' => 'MAN', 'modelo' => 'TGL', 'año' => 2020, 'kilometraje' => 57700,  'id_lugar' => 2, 'id_tipo' => 4, 'parking_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 71, 'precio_dia' => rand(20,150), 'marca' => 'Iveco', 'modelo' => 'Eurocargo', 'año' => 2019, 'kilometraje' => 69200,  'id_lugar' => 3, 'id_tipo' => 4, 'parking_id' => 13, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 72, 'precio_dia' => rand(20,150), 'marca' => 'Renault', 'modelo' => 'D-Series', 'año' => 2021, 'kilometraje' => 38400,  'id_lugar' => 1, 'id_tipo' => 4, 'parking_id' => 8, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 73, 'precio_dia' => rand(20,150), 'marca' => 'Isuzu', 'modelo' => 'F-Series', 'año' => 2022, 'kilometraje' => 32600,  'id_lugar' => 2, 'id_tipo' => 4, 'parking_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 74, 'precio_dia' => rand(20,150), 'marca' => 'Hino', 'modelo' => '500 Series', 'año' => 2023, 'kilometraje' => 21800,  'id_lugar' => 1, 'id_tipo' => 4, 'parking_id' => 9, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 75, 'precio_dia' => rand(20,150), 'marca' => 'Tata', 'modelo' => 'LPT 1618', 'año' => 2020, 'kilometraje' => 61500,  'id_lugar' => 3, 'id_tipo' => 4, 'parking_id' => 11, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}