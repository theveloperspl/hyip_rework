<?php

namespace App\Services;

use App\Models\Balance;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    /**
     * Increase user balance
     *
     * @param int $user_id
     * @param int $currency_id
     * @param int $amount
     * @return void
     */
    public function increaseBalance(int $user_id, int $currency_id, int $amount): void
    {
        Balance::whereUserId($user_id)->whereCurrencyId($currency_id)->update([
            'amount' => DB::raw("amount + {$amount}")
        ]);
    }

    /**
     * Decrease user balance
     *
     * @param int $user_id
     * @param int $currency_id
     * @param int $amount
     * @return void
     */
    public function decreaseBalance(int $user_id, int $currency_id, int $amount): void
    {
        Balance::whereUserId($user_id)->whereCurrencyId($currency_id)->update([
            'amount' => DB::raw("amount - {$amount}")
        ]);
    }
}
