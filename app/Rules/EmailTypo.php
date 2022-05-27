<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Session;

class EmailTypo implements Rule
{
    private string $correctDomain = "";

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        // make sure we've got a valid email
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $domain = substr(strrchr($value, "@"), 1);
            //check against database for typo
            $typoCheck = \App\Models\EmailTypo::whereTypo($domain)->first();
            if ($typoCheck) {
                //save session counter
                if (!Session::has('typo_counter')) {
                    Session::put('typo_counter', 1);
                } else {
                    $counter = Session::get('typo_counter');
                    $counter++;
                    if ($counter > 3) {
                        Session::forget('typo_counter');
                        return true;
                    }
                    Session::put('typo_counter', $counter);
                }
                //prepare correct domain data
                $this->correctDomain = $typoCheck->correct;
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('register.typo', ['correct' => $this->correctDomain]);
    }
}
