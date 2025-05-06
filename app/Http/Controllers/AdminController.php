<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {   
            return redirect('/login');
        }
        
        // Verificar si el usuario tiene rol de administrador (id_roles = 1)
        if (Auth::user()->id_roles !== 1) {
            // Si es una petición AJAX, devolver respuesta JSON con código 403 (Forbidden)
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            // Si es una carga normal, redirigir con mensaje de error
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        // Obtener lista de usuarios con sus roles para el dashboard
        $users = User::select('users.*', 'roles.nombre as nombre_rol')
                    ->leftJoin('roles', 'users.id_roles', '=', 'roles.id_roles')
                    ->get();

        // Cargar la vista del panel de administración con los datos
        return view('admin.index', compact('users'));
    }
}