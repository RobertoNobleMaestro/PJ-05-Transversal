<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function usuario($id)
    {
        $licencias = ['AM', 'A1', 'A2', 'A', 'B', 'B+E', 'C1', 'C1+E', 'C', 'C+E', 'D1', 'D1+E', 'D', 'D+E'];
        $user = User::findOrFail($id);
        return view('Perfil.index', compact('user', 'licencias'));
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
            'email' => 'required|email|unique:users,email,'.$user->id_usuario.',id_usuario',
            'DNI' => 'required|string|max:9|unique:users,DNI,'.$user->id_usuario.',id_usuario',
            'fecha_nacimiento' => 'required|date',
            'direccion' => 'required|string|max:255',
            'licencia_conducir' => 'required|string|max:9',
        ]);

        if ($request->hasFile('foto_perfil')) {
            // Elimina la foto anterior si existe
            if ($user->foto_perfil) {
                $fotoPath = public_path('img/' . $user->foto_perfil);
                if (file_exists($fotoPath)) {
                    unlink($fotoPath);  // Elimina el archivo
                }
            }
        
            // Obtén el archivo
            $foto = $request->file('foto_perfil');
            
            // Genera un nombre aleatorio para la foto
            $nombreFoto = uniqid('foto_', true) . '.' . $foto->getClientOriginalExtension();
            
            // Mueve la foto al directorio 'public/img' con el nombre aleatorio
            $foto->move(public_path('img'), $nombreFoto);
            
            // Guarda el nombre del archivo en el usuario
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

        // Guarda en la BD
        $user->foto_perfil = $nombreFoto;
        $user->save();

        return response()->json([
            'message' => 'Foto actualizada correctamente',
            'nombre' => $user->nombre, // <- Aquí puedes enviar lo que luego accedes en JS
            'foto' => $nombreFoto
        ]);
    }

    return response()->json(['error' => 'No se recibió una imagen.'], 422);
}

}