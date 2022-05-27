<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Exception\CryptocurrencyValidatorNotFound;

abstract class Validation extends \Merkeleon\PhpCryptocurrencyAddressValidation\Validation
{
    public static function make($iso)
    {
        $class = 'App\Validators\\' . strtoupper($iso);
        \Log::debug($class);
        if (class_exists($class))
        {
            return new $class();
        }
        throw new CryptocurrencyValidatorNotFound($iso);
    }
}
