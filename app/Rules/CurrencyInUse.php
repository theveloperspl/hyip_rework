<?php

namespace App\Rules;

use App\Models\Currency;
use Illuminate\Contracts\Validation\Rule;

class CurrencyInUse implements Rule
{
    private string $errorMessage = "";

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $currency = Currency::find($value);

        if (!$currency) {
            $this->errorMessage = __('currencies.not_found');
            return false;
        }

        if ($currency->enabled === Currency::SUSPENDED) {
            $this->errorMessage = __('currencies.suspended', ['currency' => $currency->name]);
            return false;
        }

        if ($currency->enabled === Currency::DISABLED) {
            $this->errorMessage = __('currencies.disabled', ['currency' => $currency->name]);
            return false;
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
        return $this->errorMessage;
    }
}
