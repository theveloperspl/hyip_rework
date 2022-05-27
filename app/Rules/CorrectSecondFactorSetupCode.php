<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;
use PragmaRX\Google2FAQRCode\Google2FA;

class CorrectSecondFactorSetupCode implements Rule
{
    private string $errorMessage = "";
    private Google2FA $google2FA;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->google2FA = new Google2FA();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $this->errorMessage = __('security.invalid_code');
        $setup = session()->get('second-factor-setup');

        if (!$setup) {
            $this->errorMessage = __('common.wrong');
            return false;
        }

        try {
            $valid = $this->google2FA->verifyKey($setup['secret_key'], $value);
            if ($valid) {
                return true;
            }
        } catch (Exception $exception) {
            return false;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return $this->errorMessage;
    }
}
