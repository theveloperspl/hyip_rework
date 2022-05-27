<?php

namespace App\Validators;

class PAYEER
{
    public function validate($address): bool
    {
        $valid = false;

        if(preg_match('/^[Pp][0-9]{7,15}|.+@.+\..+$/', $address)) {
            $valid = true;
        }

        return $valid;
    }
}
