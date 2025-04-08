<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Método para ver la vista del login
    public function login(){
        return view('auth.login');
    }

    public function loginProcess(Request $request){
        try {
            // Validar los datos del formulario
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico debe ser válido',
                'password.required' => 'La contraseña es obligatoria'
            ]);

            // Intentar autenticar al usuario
            $credentials = $request->only('email', 'password');
            
            if (Auth::attempt($credentials)) {
                // La autenticación fue exitosa
                $request->session()->regenerate();
                
                // Obtener el usuario autenticado
                $user = Auth::user();
                
                // Determinar la ruta de redirección según el rol
                $redirect = $user->id_roles === 1 ? '/admin' : '/home';
                
                // Devolver respuesta exitosa
                return response()->json([
                    'status' => 'success',
                    'message' => '¡Bienvenido/a!',
                    'redirect' => $redirect
                ], 200);
            }

            // Si la autenticación falla
            return response()->json([
                'status' => 'error',
                'message' => 'Las credenciales proporcionadas no coinciden.',
                'errors' => [
                    'email' => ['Credenciales incorrectas']
                ]
            ], 401);

        } catch(\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ha ocurrido un error al intentar iniciar sesión.',
                'errors' => [
                    'error' => [$e->getMessage()]
                ]
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            Auth::logout();

            // Invalidar la sesión
            $request->session()->invalidate();

            // Regenerar el token CSRF por seguridad
            $request->session()->regenerateToken();

            return redirect()->route('home')->with('success', 'Sesión cerrada correctamente.');
        }

        return redirect()->route('login');
    }

    public function register(){
        return view('auth.register');
    }
}