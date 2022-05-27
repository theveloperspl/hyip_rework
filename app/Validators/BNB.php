<?php

namespace App\Validators;

use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\Bech32Decoder;
use Merkeleon\PhpCryptocurrencyAddressValidation\Utils\Bech32Exception;

class BNB extends Validation
{
    public function validate($address): bool
    {
        $valid = false;
        try {
            $valid = is_array($decoded = Bech32Decoder::decodeRaw($address)) && 'bnb' === $decoded[0];
        } catch (Bech32Exception $exception) {}

        return $valid;
    }
}
