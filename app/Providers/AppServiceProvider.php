<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

class AppServiceProvider extends ServiceProvider
{


    public function register(): void
    {
        //
    }



    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Carbon::setLocale('es');
        
        // Deshabilitar verificación SSL para el correo electrónico (entorno de desarrollo)
        if (config('app.env') === 'local') {
            \Config::set('mail.mailers.smtp.verify_peer', false);
            \Config::set('mail.mailers.smtp.verify_peer_name', false);
            \Config::set('mail.mailers.smtp.allow_self_signed', true);
        }
    }
}