<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Valoracion;

class HomeController extends Controller
{
    public function index()
    {
        $usuariosClientes = User::where('id_roles', 2)->count();
        $vehiculos = Vehiculo::count();
        $valoracionMedia = Valoracion::avg('valoracion');

        return view('PaginaPrincipal.index', [
            'usuariosClientes' => $usuariosClientes,
            'vehiculos' => $vehiculos,
            'valoracionMedia' => round($valoracionMedia, 1),
        ]);
    }
}