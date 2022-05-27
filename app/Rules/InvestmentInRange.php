<?php

namespace App\Rules;

use App\Classes\Converter;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Validation\Rule;

class InvestmentInRange implements Rule
{
    private string $errorMessage = "";
    private string $plan;

    /**
     * Create a new rule instance.
     *
     * @param $plan
     */
    public function __construct($plan)
    {
        $this->plan = $plan;
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
        try {
            $planClass = loadPlan($this->plan);
        } catch (BindingResolutionException $exception) {
            $this->errorMessage = __('invest.error');
            return false;
        }

        $planMinimum = $planClass->getMinimumDeposit();
        $planMaximum = $planClass->getMaximumDeposit();

        if (Converter::toCents($value) < $planMinimum) {
            $this->errorMessage = __('invest.minimum', ['min' => Converter::toDollars($planMinimum)]);
            return false;
        }

        if (Converter::toCents($value) > $planMaximum) {
            $this->errorMessage = __('invest.maximum', ['max' => Converter::toDollars($planMaximum)]);
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
