<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;

class DOGE extends Base58Validation
{
    protected $base58PrefixToHexVersion = [
        'D' => '1E',
        '9' => '16',
        'A' => '16',
    ];
}
