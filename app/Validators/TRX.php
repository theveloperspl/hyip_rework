<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;

class TRX extends Base58Validation
{
    protected $base58PrefixToHexVersion = [
        'T' => '41',
    ];
}
