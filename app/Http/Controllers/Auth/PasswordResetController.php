<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;

class PasswordResetController extends Controller
{
    /**
     * Muestra el formulario para solicitar el link de recuperación de contraseña
     * Esta es la primera pantalla del proceso donde el usuario ingresa su email
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesa la solicitud de recuperación de contraseña
     * 1. Valida que el email exista y tenga formato correcto
     * 2. Genera un token único para el usuario
     * 3. Envía el email con el enlace de recuperación que incluye el token
     * 4. Muestra un mensaje al usuario indicando el resultado
     */
    public function sendResetLink(Request $request)
    {
        // Validación del email
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo electrónico válido',
            'email.exists' => 'No existe ninguna cuenta con este correo electrónico'
        ]);

        // Envío del enlace de restablecimiento
        // Password::sendResetLink hace lo siguiente:
        // 1. Busca al usuario con el email proporcionado
        // 2. Crea un token único y lo guarda en la tabla password_reset_tokens
        // 3. Envía una notificación al usuario (ResetPasswordNotification) mediante el canal de correo
        // 4. La notificación contiene un enlace con el token para verificar la solicitud
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Respuesta al usuario según el resultado
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Muestra el formulario para restablecer la contraseña
     * El usuario llega aquí desde el enlace enviado a su correo
     * El token es usado para validar la solicitud y debe coincidir con el almacenado en la BD
     */
    public function showResetForm(string $token, Request $request)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email
        ]);
    }

    /**
     * Procesa el restablecimiento de contraseña
     * 1. Valida los datos enviados (token, email, nueva contraseña)
     * 2. Verifica que el token sea válido y no haya expirado
     * 3. Actualiza la contraseña del usuario
     * 4. Elimina el token usado
     * 5. Redirige al usuario al login con mensaje de éxito
     */
    public function resetPassword(Request $request)
    {
        // Validación de los datos con reglas estrictas de seguridad
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/' // Al menos una minúscula, una mayúscula y un número
            ],
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'Ingresa un correo electrónico válido',
            'email.exists' => 'No existe ninguna cuenta con este correo electrónico',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'password.regex' => 'La contraseña debe contener al menos una letra minúscula, una mayúscula y un número'
        ]);

        // Proceso de restablecimiento de contraseña
        // Password::reset hace lo siguiente:
        // 1. Verifica que el token sea válido y coincida con el email
        // 2. Comprueba que el token no haya expirado (60 minutos por defecto)
        // 3. Ejecuta la función callback para actualizar la contraseña
        // 4. Elimina el token usado de la tabla password_reset_tokens
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();

                // Dispara el evento para posibles acciones adicionales (logs, notificaciones, etc.)
                event(new PasswordReset($user));
            }
        );

        // Redirección según el resultado
        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
