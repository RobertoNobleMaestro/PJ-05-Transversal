<?php

/**
 * Configuración recomendada para el correo electrónico en tu aplicación Laravel.
 * 
 * Para probar el sistema de restablecimiento de contraseña, sigue estos pasos:
 * 
 * 1. Crea una cuenta en Mailtrap.io (es gratis)
 * 2. Obtén tus credenciales de SMTP (host, puerto, usuario y contraseña)
 * 3. Edita tu archivo .env con estos datos
 * 
 * IMPORTANTE: Por razones de seguridad, no puedo editar directamente tu archivo .env
 */

return [
    'recommended_env_settings' => [
        'MAIL_MAILER' => 'smtp',
        'MAIL_HOST' => 'sandbox.smtp.mailtrap.io',
        'MAIL_PORT' => '2525',
        'MAIL_USERNAME' => 'tu_username_de_mailtrap',
        'MAIL_PASSWORD' => 'tu_password_de_mailtrap',
        'MAIL_ENCRYPTION' => 'tls',
        'MAIL_FROM_ADDRESS' => 'noreply@carflow.com',
        'MAIL_FROM_NAME' => 'Carflow',
    ],
    
    'gmail_settings' => [
        'MAIL_MAILER' => 'smtp',
        'MAIL_HOST' => 'smtp.gmail.com',
        'MAIL_PORT' => '587',
        'MAIL_USERNAME' => 'tu_email@gmail.com',
        'MAIL_PASSWORD' => 'tu_contraseña_de_aplicación', // No tu contraseña normal, sino una "Contraseña de aplicación"
        'MAIL_ENCRYPTION' => 'tls',
        'MAIL_FROM_ADDRESS' => 'tu_email@gmail.com',
        'MAIL_FROM_NAME' => 'Carflow',
    ],
    
    'instructions' => [
        'mailtrap' => 'Mailtrap es ideal para pruebas. Los correos no se envían a destinatarios reales sino que son capturados en tu bandeja de Mailtrap.',
        'gmail' => 'Para usar Gmail, debes habilitar la verificación en dos pasos y generar una "Contraseña de aplicación" específica.',
        'testing' => 'Alternativamente, puedes cambiar MAIL_MAILER a "log" para que los correos se guarden en storage/logs/laravel.log en lugar de enviarse.'
    ]
];
