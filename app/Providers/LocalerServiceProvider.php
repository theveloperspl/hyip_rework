<?php

namespace App\Providers;

use App\Classes\Localization;
use Illuminate\Support\ServiceProvider;

class LocalerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Localer', function ($app) {
            return new Localization($app);
        });
    }
}
