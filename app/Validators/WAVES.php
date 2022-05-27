<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Base58Validation;

class WAVES extends Base58Validation
{
    //length of bytes format
    protected $length = 26;

    private string $prefix = '0157';

    public function validate($address): bool
    {
        $valid = false;

        //perform address checks
        $addressMatchesRegexPattern = preg_match('/^[a-zA-Z0-9]{35}$/', $address);

        $addressToHex = parent::base58ToHex($address);
        //check for hex prefix in decoded address
        $hexPrefixIsPresent = substr($addressToHex, 0, 4) === $this->prefix;
        //convert hex to binary data and check its length
        $hexAddressBytesLength = strlen(hex2bin($addressToHex));

        if ($addressMatchesRegexPattern && $hexPrefixIsPresent && $hexAddressBytesLength === $this->length) {
            $valid = true;
        }

        return $valid;
    }
}
