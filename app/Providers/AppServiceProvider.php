<?php

namespace App\Providers;

use App\Http\Middleware\RedirectAuthenticatedUsers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    // ...

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void // <-- Inject the Router here
    {
        $router->aliasMiddleware('guest', RedirectAuthenticatedUsers::class);

        RateLimiter::for('address-suggestions', fn (Request $request): Limit => Limit::perMinute(60)->by($request->ip()));
    }
}
