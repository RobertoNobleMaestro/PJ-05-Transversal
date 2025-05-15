<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChoferController extends Controller
{
    /**
     * Extrae la sede de un usuario chofer basado en su email
     * Por ejemplo: de chofer.barcelona1@carflow.com obtiene 'Barcelona'
     */
    private function obtenerSedeDeEmail($email)
    {
        $partes = explode('@', $email);
        $username = $partes[0]; // chofer.barcelona1
        
        $partesSede = explode('.', $username);
        if (count($partesSede) >= 2) {
            $sedeConNumero = $partesSede[1]; // barcelona1
            // Extraer solo el texto de la sede (eliminar números)
            preg_match('/([a-zA-Z]+)/', $sedeConNumero, $matches);
            if (isset($matches[1])) {
                // Capitalizar la primera letra
                return ucfirst($matches[1]); // Barcelona
            }
        }
        
        return 'Central'; // Valor por defecto si no se puede determinar
    }
    
    /**
     * Muestra el dashboard específico para la sede del chofer logueado
     */
    public function dashboard()
    {
        $usuarioActual = Auth::user();
        $sede = $this->obtenerSedeDeEmail($usuarioActual->email);
        
        // Obtener todos los choferes de la misma sede
        $choferesDeLaSede = User::where('id_roles', 6) // ID 6 = rol de chofer
            ->where(function ($query) use ($sede) {
                $query->where('email', 'LIKE', "chofer.{$sede}%@carflow.com")
                      ->orWhere('email', 'LIKE', "chofer.{$sede}%@%");
            })
            ->get();
        
        return view('chofers.dashboard', [
            'sede' => $sede,
            'choferesCompaneros' => $choferesDeLaSede
        ]);
    }

    /**
     * Muestra la vista para que los clientes soliciten un chofer
     */
    public function pideCoche(){
        return view('chofers.cliente-pide');
    }
    
    /**
     * API para obtener los choferes de una sede específica
     */
    public function getChoferesPorSede($sede)
    {
        $choferes = User::where('id_roles', 6) // ID 6 = rol de chofer
            ->where(function ($query) use ($sede) {
                $query->where('email', 'LIKE', "chofer.{$sede}%@carflow.com")
                      ->orWhere('email', 'LIKE', "chofer.{$sede}%@%");
            })
            ->get(['id_usuario', 'nombre', 'email', 'telefono', 'foto_perfil']);
            
        return response()->json($choferes);
    }

    // Método para mostrar la vista del chat
    public function showChatView(){
        return view('chofers.chat');
    }
}
