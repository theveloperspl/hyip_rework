<?php

namespace App\Validators;

class EOS
{
    public function validate($address): bool
    {
        $valid = false;

        if(preg_match('/^[1-5a-z]{12}$/i', $address)) {
            $valid = true;
        }

        return $valid;
    }
}
