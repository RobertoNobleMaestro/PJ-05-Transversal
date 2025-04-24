<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Reserva;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function usuario($id)
    {
        $licencias = ['AM', 'A1', 'A2', 'A', 'B', 'B+E', 'C1', 'C1+E', 'C', 'C+E', 'D1', 'D1+E', 'D', 'D+E'];
        $user = User::findOrFail($id);

        // Obtener las reservas confirmadas y completadas (incluye tanto pagado como confirmada)
        $reservas = $user->reservas()
            ->whereIn('estado', ['confirmada', 'completada'])
            ->with(['vehiculosReservas.vehiculo', 'vehiculosReservas']) // Cargar las relaciones correctas
            ->get();

        return view('Perfil.index', compact('user', 'licencias', 'reservas'));
    }

    public function obtenerDatos($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function actualizar(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id_usuario . ',id_usuario',
            'DNI' => 'required|string|max:9|unique:users,DNI,' . $user->id_usuario . ',id_usuario',
            'fecha_nacimiento' => 'required|date',
            'direccion' => 'required|string|max:255',
            'licencia_conducir' => 'required|string|max:9',
        ]);

        if ($request->hasFile('foto_perfil')) {
            if ($user->foto_perfil) {
                $fotoPath = public_path('img/' . $user->foto_perfil);
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);
                }
            }

            $foto = $request->file('foto_perfil');
            $nombreFoto = uniqid('foto_', true) . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('img'), $nombreFoto);
            $user->foto_perfil = $nombreFoto;
        }

        $user->nombre = $request->nombre;
        $user->email = $request->email;
        $user->DNI = $request->DNI;
        $user->fecha_nacimiento = $request->fecha_nacimiento;
        $user->direccion = $request->direccion;
        $user->licencia_conducir = $request->licencia_conducir;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:8|confirmed'
            ]);
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['message' => 'Perfil actualizado correctamente', 'user' => $user]);
    }

    public function uploadFoto(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($request->hasFile('foto_perfil')) {
            $foto = $request->file('foto_perfil');
            $nombreFoto = uniqid('foto_', true) . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('img'), $nombreFoto);

            $user->foto_perfil = $nombreFoto;
            $user->save();

            return response()->json([
                'message' => 'Foto actualizada correctamente',
                'nombre' => $user->nombre,
                'foto' => $nombreFoto
            ]);
        }

        return response()->json(['error' => 'No se recibió una imagen.'], 422);
    }
}
