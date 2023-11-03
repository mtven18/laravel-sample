<?php

namespace App\Providers;

use App\Services\Auth\AuthService;
use App\Services\Auth\SanctumTokenAuth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthService::class, SanctumTokenAuth::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // configure default password rule
        Password::defaults(function () {
            $rule = Password::min(8);

            return $this->app->environment(['prod', 'production'])
                ? $rule->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                : $rule;
        });
    }
}
