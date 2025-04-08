<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;

class CarritoController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reservas = Reserva::with('vehiculos.imagenes')
        ->where('id_usuario', $user->id_usuario)
        ->where('estado', 'pendiente')
        ->get();
        $vehiculos = $reservas->flatMap->vehiculos;

        return response()->json($vehiculos);
    }
}
