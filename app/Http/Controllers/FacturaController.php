<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Reserva;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class FacturaController extends Controller
{
    public function descargarFactura($id_reserva)
    {
        // Verificar que la reserva pertenece al usuario
        $reserva = Reserva::with(['vehiculosReservas.vehiculo', 'pago', 'usuario', 'lugar'])
                          ->where('id_reservas', $id_reserva)
                          ->whereIn('estado', ['confirmada', 'completada'])
                          ->first();
        
        if (!$reserva) {
            abort(404, 'No se encontró la factura solicitada');
        }
        
        if ($reserva->id_usuario != Auth::id() && !(Auth::user() && Auth::user()->id_roles == 1)) {
            abort(403, 'No tienes permiso para acceder a esta factura');
        }
        
        $data = [
            'reserva' => $reserva,
            'fecha_emision' => now()->format('d/m/Y'),
            'numero_factura' => 'F-' . str_pad($reserva->id_reservas, 6, '0', STR_PAD_LEFT)
        ];
        
        // Para la vista normal sin PDF
        return view('facturas.factura', $data);
    }
}