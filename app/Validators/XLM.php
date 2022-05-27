<?php

namespace App\Validators;

class XLM
{
    protected int $length = 56;

    private string $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function validate($address): bool
    {
        $valid = false;

        //perform address checks
        $addressMatchesRegexPattern = preg_match('/^[' . $this->alphabet . ']{56}$/', $address);
        $addressStartstWithCorrectPrefix = $address[0] === "G";

        if($addressMatchesRegexPattern && $addressStartstWithCorrectPrefix && strlen($address) == $this->length) {
            $valid = true;
        }

        return $valid;
    }
}
