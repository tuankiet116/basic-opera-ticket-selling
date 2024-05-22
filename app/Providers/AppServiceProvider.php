<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;

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
        if($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Gate::define('viewPulse', function (User $user) {
            return $user->id;
        });

        Gate::define('viewLogViewer', function (User $user) {
            return $user->id;
        });

        Pulse::user(fn (User $user) => [
            'name' => $user->name,
            'extra' => $user->email,
            'avatar' => Vite::asset("resources/images/image.png"),
        ]);
    }
}
