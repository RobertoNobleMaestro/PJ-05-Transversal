<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

Route::get('/test-mail', function () {
    try {
        Mail::raw('Este es un email de prueba desde Carflow usando Mailgun', function (Message $message) {
            $message->to('alegofe04@gmail.com')
                    ->subject('Test de Mailgun con Laravel');
        });

        return 'Email enviado con éxito';
    } catch (\Exception $e) {
        return 'Error al enviar el email: ' . $e->getMessage();
    }
});
