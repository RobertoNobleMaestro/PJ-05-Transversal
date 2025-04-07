<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('metodos_de_pago')->insert([
            [
                'nombre' => 'Tarjeta de Crédito',
                'id_pago' => null,
            ],
            [
                'nombre' => 'PayPal',
                'id_pago' => null,
            ],
            [
                'nombre' => 'Transferencia Bancaria',
                'id_pago' => null,
            ],
        ]);
    }
}
