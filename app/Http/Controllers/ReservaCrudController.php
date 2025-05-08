<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Vehiculo;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

class ReservaCrudController extends Controller
{
    
    public function index(Request $request)
    { 
        // Obtener datos para los filtros
        $lugares = Lugar::all();
        $estados = ['pendiente', 'confirmada', 'cancelada', 'completada'];
        
        return view('admin.reservas', compact('lugares', 'estados'));
    }

    
}
