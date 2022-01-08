<?php

namespace App\Providers;

use App\Utils\Crypt\CryptManager;
use Illuminate\Support\ServiceProvider;

class CryptServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CryptManager::class, function ($app) {
            return new CryptManager();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
