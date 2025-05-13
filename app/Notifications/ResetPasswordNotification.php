<?php
/**
 * ResetPasswordNotification.php
 * 
 * Clase encargada de gestionar las notificaciones de restablecimiento de contraseña
 * enviadas por correo electrónico a los usuarios en idioma español.
 * 
 * Esta notificación se activa cuando un usuario solicita restablecer su contraseña
 * desde la página de "¿Has olvidado tu contraseña?". El sistema genera un token
 * único y envía un correo electrónico personalizado en español con un enlace
 * seguro para completar el proceso.
 */

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

/**
 * Clase ResetPasswordNotification
 * 
 * Extiende la clase base Notification de Laravel para crear una notificación
 * personalizada de restablecimiento de contraseña en español para Carflow.
 * 
 * Utiliza el sistema de colas (Queueable) para procesar los envíos de correo
 * de manera eficiente, especialmente útil cuando hay muchas solicitudes.
 */
class ResetPasswordNotification extends Notification
{
    use Queueable; // Permite que la notificación sea puesta en cola para procesamiento asíncrono

    /**
     * El token de restablecimiento de contraseña.
     * 
     * Este token único se genera automáticamente por Laravel cuando un usuario
     * solicita restablecer su contraseña. Se incluye en el enlace enviado por
     * correo electrónico y sirve como mecanismo de seguridad para verificar
     * que la solicitud es legítima.
     *
     * @var string
     */
    public $token;

    /**
     * Callback para personalizar la creación de la URL de restablecimiento.
     * 
     * Permite personalizar la generación de la URL de restablecimiento de contraseña
     * en caso de que se necesite un comportamiento específico (por ejemplo, para
     * aplicaciones SPA o aplicaciones móviles que no utilizan las rutas web estándar).
     *
     * @var (\Closure(mixed, string): string)|null
     */
    public static $createUrlCallback;

    /**
     * Constructor de la notificación.
     * 
     * Recibe y almacena el token único generado por Laravel para el restablecimiento
     * de contraseña. Este token se utilizará para crear el enlace seguro que se
     * enviará al usuario.
     *
     * @param  string  $token  Token único para el restablecimiento de contraseña
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Determina los canales por los que se enviará la notificación.
     * 
     * En este caso, solo utilizamos el canal de correo electrónico ('mail'), pero
     * Laravel permite enviar notificaciones por varios canales simultáneamente,
     * como SMS, Slack, base de datos, etc.
     *
     * @param  mixed  $notifiable  El modelo de usuario que recibirá la notificación
     * @return array  Canales de envío de la notificación
     */
    public function via($notifiable)
    {
        return ['mail']; // Solo enviamos por correo electrónico
    }

    /**
     * Construye el mensaje de correo electrónico para la notificación.
     * 
     * Este método personaliza el contenido del correo de restablecimiento de contraseña
     * en español. Utiliza la clase MailMessage de Laravel que proporciona una interfaz
     * fluida para construir mensajes elegantes y responsivos sin tener que definir
     * plantillas HTML complejas.
     *
     * @param  mixed  $notifiable  El modelo de usuario que recibirá la notificación
     * @return \Illuminate\Notifications\Messages\MailMessage  Objeto que representa el email
     */
    public function toMail($notifiable)
    {
        // Primero, determinamos qué URL de restablecimiento usar
        if (static::$createUrlCallback) {
            // Si se ha definido un callback personalizado, lo usamos
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            // De lo contrario, generamos la URL usando la ruta 'password.reset'
            // incluyendo el token y el email del usuario como parámetros
            $url = url(route('password.reset', [
                'token' => $this->token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        }

        // Construimos el mensaje del correo en español
        return (new MailMessage)
            ->subject('Carflow - Restablecimiento de contraseña') // Asunto del correo
            ->greeting('¡Hola!') // Saludo inicial
            ->line('Has recibido este correo porque hemos recibido una solicitud de restablecimiento de contraseña para tu cuenta.') // Primer párrafo
            ->action('Restablecer contraseña', $url) // Botón de acción con la URL
            ->line('Este enlace de restablecimiento de contraseña caducará en 60 minutos.') // Información de caducidad
            ->line('Si no has solicitado un restablecimiento de contraseña, no se requiere ninguna acción adicional.') // Mensaje de seguridad
            ->salutation('Saludos, el equipo de Carflow'); // Despedida
    }

    /**
     * Establece un callback personalizado para crear la URL del botón de restablecimiento.
     * 
     * Este método estático permite a los desarrolladores personalizar cómo se generan
     * las URLs de restablecimiento, lo que es útil en escenarios como aplicaciones SPA
     * o cuando se necesita una estructura de URL específica.
     *
     * @param  \Closure(mixed, string): string  $callback  Función que generará la URL
     * @return void
     */
    public static function createUrlUsing($callback)
    {
        static::$createUrlCallback = $callback;
    }
}
