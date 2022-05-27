<?php

namespace App\Rules;

use App\Models\Currency;
use App\Validators\Validation;
use Illuminate\Contracts\Validation\Rule;

class ValidCurrencyAddress implements Rule
{
    private Currency $currency;
    private string $errorMessage = "";

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $currency)
    {
        $this->currency = Currency::find($currency);
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
        if (!$this->currency) {
            $this->errorMessage = __('withdraw.unsupported');
            return false;
        }

        try {
            $validator = Validation::make($this->currency->validator);
            $validated = $validator->validate($value);

            if (!$validated) {
                $this->errorMessage = __('withdraw.wrong_address', ['currency' => $this->currency->name]);
                return false;
            }
        } catch (\Exception $exception) {
            $this->errorMessage = __('withdraw.unsupported');
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
