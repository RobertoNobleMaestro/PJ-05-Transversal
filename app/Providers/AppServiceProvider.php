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
    }
}