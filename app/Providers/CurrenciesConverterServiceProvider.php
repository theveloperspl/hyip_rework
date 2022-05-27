<?php

namespace App\Providers;

use App\Classes\CurrenciesConverter;
use Illuminate\Support\ServiceProvider;

class CurrenciesConverterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('CurrenciesConverter', function () {
            return new CurrenciesConverter();
        });
    }
}
