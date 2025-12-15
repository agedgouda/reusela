<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Http\Middleware\RedirectAuthenticatedUsers;

class AppServiceProvider extends ServiceProvider
{
    // ...

    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router): void // <-- Inject the Router here
    {
        $router->aliasMiddleware('guest', RedirectAuthenticatedUsers::class);
    }
}
