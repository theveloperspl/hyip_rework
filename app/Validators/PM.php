<?php

namespace App\Validators;

class PM
{
    public function validate($address): bool
    {
        $valid = false;

        if(preg_match('/^[Uu][0-9]{7,15}|.+@.+\..+$/', $address)) {
            $valid = true;
        }

        return $valid;
    }
}
