<?php

namespace App\Validators;

class XMR
{
    public function validate($address): bool
    {
        $valid = false;

        if(preg_match('/^[48](?:[0-9AB]|[1-9A-HJ-NP-Za-km-z]{12}(?:[1-9A-HJ-NP-Za-km-z]{30})?)[1-9A-HJ-NP-Za-km-z]{93}$/', $address)) {
            $valid = true;
        }

        return $valid;
    }
}
