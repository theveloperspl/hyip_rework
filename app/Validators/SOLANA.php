<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;

class SOLANA extends Base58Validation
{
    //length of bytes format
    protected $length = 32;

    public function validate($address): bool
    {
        $valid = false;

        //perform address checks
        $addressMatchesRegexPattern = preg_match('/[1-9A-HJ-NP-Za-km-z]{32,44}/', $address);

        $addressToHex = parent::base58ToHex($address);
        //convert hex to binary data and check its length
        $hexAddressBytesLength = strlen(hex2bin($addressToHex));

        if ($addressMatchesRegexPattern && $hexAddressBytesLength === $this->length) {
            $valid = true;
        }

        return $valid;
    }
}
