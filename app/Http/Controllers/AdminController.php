<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No tienes permiso para acceder a esta sección'], 403);
            }
            return redirect('/')->with('error', 'No tienes permiso para acceder a esta sección');
        }
        
        return view('admin.index');
    }
}
