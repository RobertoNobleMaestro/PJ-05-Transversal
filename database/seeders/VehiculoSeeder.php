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
            ['id_vehiculos' => 1, 'marca' => 'Toyota', 'modelo' => 'Corolla', 'año' => 2024, 'kilometraje' => 41733, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 2, 'marca' => 'Volkswagen', 'modelo' => 'Golf', 'año' => 2020, 'kilometraje' => 62124, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 3, 'marca' => 'Ford', 'modelo' => 'Focus', 'año' => 2020, 'kilometraje' => 74116, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 4, 'marca' => 'Renault', 'modelo' => 'Clio', 'año' => 2019, 'kilometraje' => 35532, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 5, 'marca' => 'Peugeot', 'modelo' => '308', 'año' => 2023, 'kilometraje' => 23938, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 6, 'marca' => 'Hyundai', 'modelo' => 'i30', 'año' => 2021, 'kilometraje' => 52000, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 7, 'marca' => 'Kia', 'modelo' => 'Ceed', 'año' => 2022, 'kilometraje' => 33500, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 8, 'marca' => 'Seat', 'modelo' => 'Leon', 'año' => 2020, 'kilometraje' => 61200, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 9, 'marca' => 'Mazda', 'modelo' => '3', 'año' => 2019, 'kilometraje' => 68500, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 10, 'marca' => 'Skoda', 'modelo' => 'Octavia', 'año' => 2024, 'kilometraje' => 12500, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 11, 'marca' => 'Opel', 'modelo' => 'Astra', 'año' => 2023, 'kilometraje' => 20100, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 12, 'marca' => 'Citroen', 'modelo' => 'C4', 'año' => 2021, 'kilometraje' => 48900, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 13, 'marca' => 'Honda', 'modelo' => 'Civic', 'año' => 2020, 'kilometraje' => 75200, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 14, 'marca' => 'Fiat', 'modelo' => 'Tipo', 'año' => 2018, 'kilometraje' => 83200, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 15, 'marca' => 'Nissan', 'modelo' => 'Pulsar', 'año' => 2022, 'kilometraje' => 29100, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 16, 'marca' => 'Chevrolet', 'modelo' => 'Cruze', 'año' => 2019, 'kilometraje' => 67900, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 17, 'marca' => 'Suzuki', 'modelo' => 'Swift', 'año' => 2020, 'kilometraje' => 41000, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 18, 'marca' => 'Mini', 'modelo' => 'Cooper', 'año' => 2023, 'kilometraje' => 13500, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 19, 'marca' => 'Subaru', 'modelo' => 'Impreza', 'año' => 2021, 'kilometraje' => 48900, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 20, 'marca' => 'Lada', 'modelo' => 'Vesta', 'año' => 2022, 'kilometraje' => 39200, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 21, 'marca' => 'Chery', 'modelo' => 'Arrizo 5', 'año' => 2023, 'kilometraje' => 11000, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 22, 'marca' => 'Geely', 'modelo' => 'Emgrand', 'año' => 2020, 'kilometraje' => 55900, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 23, 'marca' => 'Dacia', 'modelo' => 'Sandero', 'año' => 2019, 'kilometraje' => 64800, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 24, 'marca' => 'BYD', 'modelo' => 'Dolphin', 'año' => 2024, 'kilometraje' => 8000, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 25, 'marca' => 'MG', 'modelo' => 'MG5', 'año' => 2023, 'kilometraje' => 15400, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 26, 'marca' => 'Tata', 'modelo' => 'Tiago', 'año' => 2021, 'kilometraje' => 43500, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 27, 'marca' => 'Proton', 'modelo' => 'Saga', 'año' => 2022, 'kilometraje' => 37600, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 28, 'marca' => 'Perodua', 'modelo' => 'Myvi', 'año' => 2020, 'kilometraje' => 49200, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 29, 'marca' => 'Great Wall', 'modelo' => 'Voleex C30', 'año' => 2018, 'kilometraje' => 58900, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 30, 'marca' => 'Zotye', 'modelo' => 'Z300', 'año' => 2021, 'kilometraje' => 32800, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 1, 'created_at' => now(), 'updated_at' => now()],
            // 20 Motos
            ['id_vehiculos' => 31, 'marca' => 'Yamaha', 'modelo' => 'MT-07', 'año' => 2022, 'kilometraje' => 8500, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 32, 'marca' => 'Honda', 'modelo' => 'CBR500R', 'año' => 2021, 'kilometraje' => 9700, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 33, 'marca' => 'Kawasaki', 'modelo' => 'Z650', 'año' => 2020, 'kilometraje' => 12300, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 34, 'marca' => 'BMW', 'modelo' => 'G310R', 'año' => 2023, 'kilometraje' => 5000, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 35, 'marca' => 'Suzuki', 'modelo' => 'GSX-S750', 'año' => 2019, 'kilometraje' => 18400, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 36, 'marca' => 'Ducati', 'modelo' => 'Monster', 'año' => 2021, 'kilometraje' => 7100, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 37, 'marca' => 'Triumph', 'modelo' => 'Street Triple', 'año' => 2022, 'kilometraje' => 6300, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 38, 'marca' => 'KTM', 'modelo' => 'Duke 390', 'año' => 2020, 'kilometraje' => 7900, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 39, 'marca' => 'Harley-Davidson', 'modelo' => 'Iron 883', 'año' => 2018, 'kilometraje' => 11500, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 40, 'marca' => 'Aprilia', 'modelo' => 'RS 660', 'año' => 2023, 'kilometraje' => 4200, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 41, 'marca' => 'Benelli', 'modelo' => 'Leoncino', 'año' => 2022, 'kilometraje' => 6100, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 42, 'marca' => 'Husqvarna', 'modelo' => 'Svartpilen 401', 'año' => 2021, 'kilometraje' => 7000, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 43, 'marca' => 'Royal Enfield', 'modelo' => 'Meteor 350', 'año' => 2020, 'kilometraje' => 8300, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 44, 'marca' => 'CFMOTO', 'modelo' => '300NK', 'año' => 2023, 'kilometraje' => 3700, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 45, 'marca' => 'Zontes', 'modelo' => 'T310', 'año' => 2021, 'kilometraje' => 9200, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 46, 'marca' => 'Moto Guzzi', 'modelo' => 'V7 Stone', 'año' => 2020, 'kilometraje' => 7800, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 47, 'marca' => 'Voge', 'modelo' => '500R', 'año' => 2019, 'kilometraje' => 9700, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 48, 'marca' => 'SYM', 'modelo' => 'Wolf 250', 'año' => 2022, 'kilometraje' => 4500, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 49, 'marca' => 'Mash', 'modelo' => 'Two Fifty', 'año' => 2021, 'kilometraje' => 5300, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 50, 'marca' => 'Lifan', 'modelo' => 'KPR 200', 'año' => 2020, 'kilometraje' => 10200, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 2, 'created_at' => now(), 'updated_at' => now()],
            // 15 Furgonetas
            ['id_vehiculos' => 51, 'marca' => 'Mercedes-Benz', 'modelo' => 'Sprinter', 'año' => 2022, 'kilometraje' => 42300, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 52, 'marca' => 'Ford', 'modelo' => 'Transit', 'año' => 2021, 'kilometraje' => 50200, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 53, 'marca' => 'Renault', 'modelo' => 'Master', 'año' => 2020, 'kilometraje' => 58800, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 54, 'marca' => 'Volkswagen', 'modelo' => 'Crafter', 'año' => 2023, 'kilometraje' => 27900, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 55, 'marca' => 'Citroën', 'modelo' => 'Jumper', 'año' => 2022, 'kilometraje' => 39000, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 56, 'marca' => 'Peugeot', 'modelo' => 'Boxer', 'año' => 2020, 'kilometraje' => 61200, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 57, 'marca' => 'Fiat', 'modelo' => 'Ducato', 'año' => 2021, 'kilometraje' => 48700, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 58, 'marca' => 'Iveco', 'modelo' => 'Daily', 'año' => 2019, 'kilometraje' => 70400, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 59, 'marca' => 'Opel', 'modelo' => 'Movano', 'año' => 2023, 'kilometraje' => 21000, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 60, 'marca' => 'Nissan', 'modelo' => 'NV400', 'año' => 2020, 'kilometraje' => 64300, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 61, 'marca' => 'Hyundai', 'modelo' => 'H350', 'año' => 2021, 'kilometraje' => 35600, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 62, 'marca' => 'Toyota', 'modelo' => 'Proace', 'año' => 2022, 'kilometraje' => 27400, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 63, 'marca' => 'MAN', 'modelo' => 'TGE', 'año' => 2023, 'kilometraje' => 18200, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 64, 'marca' => 'Isuzu', 'modelo' => 'N-Series', 'año' => 2020, 'kilometraje' => 52000, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 65, 'marca' => 'Maxus', 'modelo' => 'Deliver 9', 'año' => 2021, 'kilometraje' => 46800, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 3, 'created_at' => now(), 'updated_at' => now()],
            // 10 Camiones
            ['id_vehiculos' => 66, 'marca' => 'Volvo', 'modelo' => 'FL', 'año' => 2022, 'kilometraje' => 31200, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 67, 'marca' => 'Mercedes-Benz', 'modelo' => 'Atego', 'año' => 2021, 'kilometraje' => 46800, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 68, 'marca' => 'DAF', 'modelo' => 'LF', 'año' => 2020, 'kilometraje' => 52900, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 69, 'marca' => 'Scania', 'modelo' => 'P-Series', 'año' => 2023, 'kilometraje' => 24800, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 70, 'marca' => 'MAN', 'modelo' => 'TGL', 'año' => 2020, 'kilometraje' => 57700, 'seguro_incluido' => false, 'id_lugar' => 2, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 71, 'marca' => 'Iveco', 'modelo' => 'Eurocargo', 'año' => 2019, 'kilometraje' => 69200, 'seguro_incluido' => true, 'id_lugar' => 3, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 72, 'marca' => 'Renault', 'modelo' => 'D-Series', 'año' => 2021, 'kilometraje' => 38400, 'seguro_incluido' => false, 'id_lugar' => 1, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 73, 'marca' => 'Isuzu', 'modelo' => 'F-Series', 'año' => 2022, 'kilometraje' => 32600, 'seguro_incluido' => true, 'id_lugar' => 2, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 74, 'marca' => 'Hino', 'modelo' => '500 Series', 'año' => 2023, 'kilometraje' => 21800, 'seguro_incluido' => true, 'id_lugar' => 1, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['id_vehiculos' => 75, 'marca' => 'Tata', 'modelo' => 'LPT 1618', 'año' => 2020, 'kilometraje' => 61500, 'seguro_incluido' => false, 'id_lugar' => 3, 'id_tipo' => 4, 'created_at' => now(), 'updated_at' => now()]
        ]);
    }
}