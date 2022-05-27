<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/** @see \App\Classes\CurrenciesConverter */
class CurrenciesConverter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'CurrenciesConverter';
    }
}
