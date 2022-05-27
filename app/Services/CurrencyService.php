<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\Currency;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CurrencyService
{
    /**
     * Display a list of currencies that are activated in user account
     *
     * @param int $userId
     * @return Collection
     */
    public function userActiveCurrencies(int $userId): Collection
    {
        return Balance::whereUserId($userId)->orderBy('currency_id')->pluck('currency_id');
    }

    /**
     * Display a list of currencies that can be added by user
     *
     * @param int $userId
     * @return Collection
     */
    public function userAddableCurrencies(int $userId): Collection
    {
        $platformActiveCurrencies = Cache::rememberForever(Currency::NOT_DISABLED_CACHE_KEY, function () {
            return Currency::notDisabled()->get();
        });
        $userActiveCurrencies = $this->userActiveCurrencies($userId);

        return $platformActiveCurrencies->filter(function ($currency) use ($userActiveCurrencies) {
            return !$userActiveCurrencies->contains($currency->id);
        });
    }

    /**
     * Activate selected currency balance for user
     *
     * @param int $userId
     * @param int $currency
     * @return array
     */
    public function addCurrency(int $userId, int $currency): array
    {
        $addableCurrencies = $this->userAddableCurrencies($userId)->pluck('id')->toArray();
        $message = [];
        $message['type'] = 'error';
        $message['message'] = __('currencies.already');

        if (in_array($currency, $addableCurrencies)) {
            //add currency balance
            Balance::create([
                'user_id' => $userId,
                'currency_id' => $currency,
                'amount' => 0
            ]);
            //add currency wallet
            Wallet::create([
                'user_id' => $userId,
                'currency_id' => $currency
            ]);
            //set message
            $message['type'] = 'success';
            unset($message['message']);
            //modify addable currencies and check if array is not empty
            $addableCurrencies = array_diff($addableCurrencies, [$currency]);
            if (empty($addableCurrencies)) {
                $message['last'] = true;
            }
        }

        return $message;
    }

    /**
     * Activate selected currency balance for user
     *
     * @param int $currency
     * @param User $user
     * @return void
     */
    public function updateMainWallet(User $user, int $currency): void
    {
        $user->main_wallet = $currency;
        $user->save();
    }

}
