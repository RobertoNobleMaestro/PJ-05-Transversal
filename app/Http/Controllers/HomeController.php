<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Valoracion;
use App\Models\Tipo;

class HomeController extends Controller
{
    public function index()
    {
        $usuariosClientes = User::where('id_roles', 2)->count();
        $vehiculos = Vehiculo::count();
        $valoracionMedia = Valoracion::avg('valoracion');
        $tipos = Tipo::all(); // Tipos de vehículos

        return view('PaginaPrincipal.index', [
            'usuariosClientes' => $usuariosClientes,
            'vehiculos' => $vehiculos,
            'valoracionMedia' => round($valoracionMedia, 1),
            'tipos' => $tipos,
        ]);
    }
}