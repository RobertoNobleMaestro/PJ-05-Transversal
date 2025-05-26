<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\User;
use App\Models\Lugar;
use App\Models\Parking;
use App\Models\Vehiculo;
use Illuminate\Support\Facades\DB;

class ParkingGestorController extends Controller
{
    private function checkGestor($request)
    {
        if (!auth()->check()) {
            return redirect('/login');
        }
        
        if (auth()->user()->id_roles !== 3 && auth()->user()->id_roles !== 1) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return null; // El usuario es administrador, continuar
    }

    public function index(Request $request)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) {
            return $authCheck;
        }
        $user = auth()->user();
        if ($user->id_roles == 1) {
            $parkings = Parking::all();
            return view('gestor.parking.index', compact('parkings'));
        }
        $gestor = auth()->user();
        $parkings = Parking::where('id_usuario', auth()->user()->id_usuario)->get();
        return view('gestor.parking.index', compact('parkings'));
    }

    public function update(Request $request, $id)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) return $authCheck;

        $request->validate([
            'nombre' => 'required|string|max:255',
            'plazas' => 'required|integer|min:1',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        $parking = Parking::where('id_usuario', auth()->user()->id_usuario)->findOrFail($id);
        $parking->update([
            'nombre' => $request->nombre,
            'plazas' => $request->plazas,
            'latitud' => $request->latitud,
            'longitud' => $request->longitud,
        ]);

        return redirect()->route('gestor.parking.index')->with('success', 'Parking actualizado correctamente.');
    }

    public function destroy(Request $request, $id)
    {
        $authCheck = $this->checkGestor($request);
        if ($authCheck) return $authCheck;

        // Buscar el parking a eliminar
        $parking = Parking::where('id_usuario', auth()->user()->id_usuario)->findOrFail($id);

        // Buscar otro parking del mismo lugar (distinto del actual)
        $parkingAlternativo = Parking::where('id_lugar', $parking->id_lugar)
                                    ->where('id', '!=', $parking->id)
                                    ->first();

        if (!$parkingAlternativo) {
            return redirect()->route('gestor.parking.index')->with('error', 'No se puede eliminar el parking porque no existe otro parking en el mismo lugar para reasignar los vehículos.');
        }

        DB::beginTransaction();

        try {
            // Reasignar vehículos al parking alternativo
            Vehiculo::where('parking_id', $parking->id)
                ->update(['parking_id' => $parkingAlternativo->id]);

            // Eliminar el parking
            $parking->delete();

            DB::commit();

            return redirect()->route('gestor.parking.index')->with('success', 'Parking eliminado correctamente y vehículos reasignados.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('gestor.parking.index')->with('error', 'Error al eliminar el parking: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'plazas' => 'required|integer|min:1',
            'latitud' => 'required|numeric',
            'longitud' => 'required|numeric',
        ]);

        $parking = new \App\Models\Parking();
        $parking->nombre = $request->nombre;
        $parking->plazas = $request->plazas;
        $parking->latitud = $request->latitud;
        $parking->longitud = $request->longitud;
        $parking->id_usuario = auth()->user()->id_usuario; // O el campo que corresponda
        // Si tienes id_lugar, añádelo aquí
        $parking->save();

        return redirect()->route('gestor.parking.index')->with('success', 'Parking creado correctamente.');
    }

    public function create()
    {
        // Redirige a index con un flag en la sesión
        return redirect()->route('gestor.parking.index')->with('openCreatePanel', true);
    }
}
