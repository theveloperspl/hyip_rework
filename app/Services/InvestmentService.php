<?php

namespace App\Services;

use App\Models\Investment;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\View\View;

class InvestmentService
{
    /**
     * Check if investment UUID exists
     *
     * @param string $uuid
     * @return bool
     */
    public function investmentExists(string $uuid): bool
    {
        $investment = Investment::withTrashed()->find($uuid);
        if ($investment) {
            return true;
        }

        return false;
    }

    /**
     * Check if investment hash exists
     *
     * @param string $hash
     * @return bool
     */
    public function transactionHashExists(string $hash): bool
    {
        $investment = Investment::whereHash($hash)->first();
        if ($investment) {
            return true;
        }

        return false;
    }

    /**
     * Load investment details view
     *
     * @param Investment $investment
     * @return Application|Factory|View
     * @throws BindingResolutionException
     */
    public function loadInvestmentDetails(Investment $investment)
    {
        $planClass = loadPlan($investment->plan);

        //TODO calculate cycles and decide if display working days or days left to finish
        $cycles = 0;
        $profitType = '';
        $cyclesType = '';
        switch ($planClass->getType()) {
            case 'daily':
                $profitType = __('deposits.daily');
                $cyclesType = __('deposits.days_left');
                $cycles = isPlanInfinite($planClass) ? config('constants.infinity_symbol') : $investment->cycles;
                break;
            case 'weekly':
                $profitType = __('deposits.weekly');
                $cyclesType = __('deposits.weeks_left');
                $cycles = isPlanInfinite($planClass) ? config('constants.infinity_symbol') : $investment->cycles;
                break;
            case 'monthly':
                $profitType = __('deposits.monthly');
                $cyclesType = __('deposits.months_left');
                $cycles = isPlanInfinite($planClass) ? config('constants.infinity_symbol') : $investment->cycles;
                break;
            case 'yearly':
                $profitType = __('deposits.yearly');
                $cyclesType = __('deposits.years_left');
                $cycles = isPlanInfinite($planClass) ? config('constants.infinity_symbol') : $investment->cycles;
                break;
            case 'after':
                $profitType = __('deposits.return');
                $cyclesType = __('deposits.working');
                $cycles = $planClass->getCycles() - $investment->cycles;
                break;
            case 'split':
                $profitType = __('deposits.return');
                $cyclesType = __('deposits.return_day');
                $cycles = $planClass->calculateSteps()->formatSteps('hyphens'); // TODO remove already passed steps
                break;
        }
        $isCancelable = isPlanCancelable($planClass);

        return view('invest.details', compact('investment', 'profitType', 'cyclesType', 'cycles', 'isCancelable', 'planClass'));
    }
}
