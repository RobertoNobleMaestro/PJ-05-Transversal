<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    // Método para ver la vista del login
    public function login()
    {
        return view('auth.login');
    }

    public function loginProcess(Request $request)
    {
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
        } catch (\Exception $e) {
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

    public function register()
    {

        $licencias = ['AM', 'A1', 'A2', 'A', 'B', 'B+E', 'C1', 'C1+E', 'C', 'C+E', 'D1', 'D1+E', 'D', 'D+E'];

        return view('auth.register', compact('licencias'));
    }

    // Función para el proceso de registro
    public function registerProcess(Request $request)
    {
        try {
            // Normalizar email y dni
            $request->merge([
                'email' => strtolower(trim($request->email)),
                'dni' => strtoupper(trim($request->dni))
            ]);

            // Validar campos
            $request->validate([
                'nombre' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    Rule::unique('users', 'email'),
                ],
                'dni' => [
                    'required',
                    'string',
                    Rule::unique('users', 'DNI'),  // <-- cambiado a minúsculas
                ],
                'telf' => 'required|string|max:20',
                'fecha_nacimiento' => 'required|date',
                'direccion' => 'required|string|max:255',
                'licencia_conducir' => 'required|string',
                'password' => 'required|min:6|confirmed',
                'imagen' => 'nullable|image|max:2048',
            ], [
                'email.unique' => 'El correo electrónico ya está registrado',
                'dni.unique' => 'Este DNI ya está registrado',
                'password.confirmed' => 'Las contraseñas no coinciden',
            ]);

            // Crear usuario
            $user = new User();
            $user->nombre = $request->nombre;
            $user->email = $request->email;
            $user->DNI = $request->dni; // Corregido: usar dni (minúsculas) que es como viene del formulario
            $user->telefono = $request->telf;
            $user->fecha_nacimiento = $request->fecha_nacimiento;
            $user->direccion = $request->direccion;
            $user->licencia_conducir = $request->licencia_conducir;
            $user->id_roles = 2;
            $user->password = Hash::make($request->password);

            if ($request->hasFile('imagen')) {
                $ruta = $request->file('imagen')->store('perfiles', 'public');
                $user->foto_perfil = $ruta;
            }

            $user->save();

           

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado correctamente.'
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors' => $ve->errors()
            ], 422);
        } catch (\Throwable $e) {  // <-- Usar Throwable para capturar todo, no solo Exception
            return response()->json([
                'success' => false,
                'message' => 'Ha ocurrido un error, por favor intente más tarde.',
                'error' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }
}
