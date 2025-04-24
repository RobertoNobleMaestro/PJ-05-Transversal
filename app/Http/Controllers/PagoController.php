<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Pago;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PagoController extends Controller
{
    public function checkout()
    {
        // Obtener la reserva pendiente del usuario con sus vehículos
        $userId = Auth::id() ?? 1;
        Log::info('Usuario ID: ' . $userId);
        
        $reserva = Reserva::where('id_usuario', $userId)
                         ->where('estado', 'pendiente')
                         ->with(['vehiculos', 'vehiculosReservas', 'vehiculosReservas.vehiculo'])
                         ->first();

        if (!$reserva) {
            return redirect()->route('carrito')->with('error', 'No tienes ninguna reserva pendiente');
        }

        // Calcular el precio total de la reserva
        $total = 0;
        foreach ($reserva->vehiculosReservas as $vr) {
            $dias = \Carbon\Carbon::parse($vr->fecha_ini)->diffInDays($vr->fecha_final);
            $dias = max(1, $dias); // Mínimo 1 día
            $total += $vr->vehiculo->precio_dia * $dias;
        }

        // Actualizar la reserva con el precio total
        $reserva->total_precio = $total;
        $reserva->save();

        // Generar un identificador único para la transacción
        $transactionId = 'TRANS-' . strtoupper(substr(md5(uniqid($reserva->id_reservas, true)), 0, 10));
        $reserva->referencia_pago = $transactionId;
        $reserva->save();

        return view('pago.checkout', [
            'reserva' => $reserva,
            'transaction_id' => $transactionId
        ]);
    }
    
    public function procesar(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required',
            'nombre_tarjeta' => 'required',
            'numero_tarjeta' => 'required|digits:16',
            'fecha_expiracion' => 'required',
            'cvv' => 'required|digits:3',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        // Validación manual del formato de fecha (MM/YY)
        $fechaExp = $request->fecha_expiracion;
        if (!preg_match('/^\d{2}\/\d{2}$/', $fechaExp)) {
            return redirect()->back()->withErrors(['fecha_expiracion' => 'El formato de fecha debe ser MM/YY'])->withInput();
        }
        
        // Buscar la reserva por el ID de transacción
        $reserva = Reserva::where('referencia_pago', $request->transaction_id)->first();
        
        if (!$reserva) {
            return redirect()->route('carrito')->with('error', 'No se encontró la reserva asociada a esta transacción');
        }
        
        // Simular proceso de pago (siempre exitoso)
        $reserva->estado = 'confirmada';
        $reserva->save();
        
        // Registrar el pago
        $pago = new Pago();
        $pago->id_reservas = $reserva->id_reservas;
        $pago->estado_pago = 'completado';
        $pago->fecha_pago = now();
        $pago->referencia_externa = $request->transaction_id;
        $pago->total_precio = $reserva->total_precio;
        $pago->moneda = 'EUR';
        $pago->id_usuario = $reserva->id_usuario;
        $pago->save();
        
        return redirect()->route('pago.exito', ['id_reserva' => $reserva->id_reservas]);
    }
    
    public function exito(Request $request, $id_reserva)
    {
        $reserva = Reserva::with(['vehiculos', 'vehiculosReservas', 'vehiculosReservas.vehiculo'])
                         ->findOrFail($id_reserva);
        
        return view('pago.exito', ['reserva' => $reserva]);
    }
    
    public function cancelado()
    {
        return view('pago.cancelado');
    }
}