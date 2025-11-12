<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use App\Listeners\SendVerificationEmailOnEmployerLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register a lightweight listener for login events so we can send
        // verification emails to employer accounts and grant a short grace
        // period for immediate actions (e.g., uploading a business permit).
        Event::listen(Login::class, [SendVerificationEmailOnEmployerLogin::class, 'handle']);
    }
}
