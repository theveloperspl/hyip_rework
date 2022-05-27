<?php

namespace App\Validators;

class ADVCASH
{
    public function validate($address): bool
    {
        $valid = false;

        if(preg_match('/^U [0-9]+ [0-9]+ [0-9]+$/i', $address)) {
            $valid = true;
        }

        return $valid;
    }
}
