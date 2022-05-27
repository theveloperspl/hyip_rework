<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;

class BTG extends Base58Validation
{
    // more info at http://lenschulwitz.com/base58 (decode address with selected prefix and select first hex value)
    protected $base58PrefixToHexVersion = [
        'G' => '26',
        'd' => '17'
    ];

    public function validate($address): bool
    {
        $address = (string)$address;
        return parent::validate($address);
    }
}
