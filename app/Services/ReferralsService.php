<?php

namespace App\Services;

use App\Events\NewCommission;
use App\Models\Investment;
use App\Models\ReferralCommission;
use App\Models\User;

class ReferralsService
{
    private CurrencyService $currencyService;
    private BalanceService $balanceService;

    public function __construct(CurrencyService $currencyService, BalanceService $balanceService)
    {
        $this->currencyService = $currencyService;
        $this->balanceService = $balanceService;
    }

    /**
     * Get user referrals breakdown by levels
     * @param int $user
     * @return array
     */
    public function getReferralsByLevel(int $user): array
    {
        $downlines = [];
        //add inviting user to array for proper checking
        $downlines[0] = [$user];
        for ($i = 1; $i <= config('referrals.levels'); $i++) {
            $downlines[$i] = $this->getDownline($downlines[$i - 1]);
        }
        //remove inviting user from array
        unset($downlines[0]);

        return $downlines;
    }

    /**
     * Get user referrals breakdown (optional: by levels) assigned to their parents
     * @param int $user
     * @param bool $displayLevels
     * @return array
     */
    public function getReferralsWithParent(int $user, bool $displayLevels = true): array
    {
        $downlines = [[]];
        $downlines[0][0] = [$user];
        for ($i = 1; $i <= config('referrals.levels'); $i++) {
            $downlines[$i] = $this->getDownlineWithParent($downlines[$i - 1]);
        }
        unset($downlines[0]);

        if ($displayLevels) {
            return $downlines;
        }

        return array_values($downlines);
    }

    /**
     * Fetch uplines of given user
     * @param int $user
     * @return array
     */
    public function getReferralUplines(int $user): array
    {
        $parents = [];
        $userDetails = User::find($user);
        for ($i = 1; $i <= config('referrals.levels'); $i++) {
            $upline = $userDetails->upline;
            if ($upline > 0) {
                $parents[$i] = $upline;
                $userDetails = User::find($upline);
            } else {
                break;
            }
        }

        return $parents;
    }

    /**
     * Count user referrals on supplied level
     * @param array $level
     * @return int
     */
    public function countReferralsByLevel(array $level): int
    {
        return count($level);
    }

    /**
     * Process referrals commissions
     * @param int $amount
     * @param int $scale
     * @param Investment $investment
     * @return void
     */
    public function processReferralCommission(int $amount, int $scale, Investment $investment): void
    {
        $uplines = $this->getReferralUplines($investment->user_id);
        if (!empty($uplines)) {
            foreach ($uplines as $level => $upline) {
                $upline = User::find($upline);
                $percentage = $this->determineReferralPercentage($upline, $level);
                //check if coin is already active for upline and update balance
                $activeCurrencies = $this->currencyService->userActiveCurrencies($upline->id);
                if (!$activeCurrencies->contains($investment->currency_id)) {
                    $this->currencyService->addCurrency($upline->id, $investment->currency_id);
                }
                $commission = $this->calculateCommission($amount, $percentage, $scale);
                $this->balanceService->increaseBalance($upline->id, $investment->currency_id, $commission);
                //insert ito commissions
                $referralCommission = new ReferralCommission();
                $referralCommission->user_id = $investment->user_id;
                $referralCommission->upline_id = $upline->id;
                $referralCommission->currency_id = $investment->currency_id;
                $referralCommission->investment_id = $investment->uuid;
                $referralCommission->amount = $commission;
                $referralCommission->level = $level;
                $referralCommission->save();
                //deploy new commission event
                event(new NewCommission($upline, $referralCommission));
            }
        }
    }

    /**
     * Determine referral percentage for given user and level
     * @param User $upline
     * @param int $level
     * @return string
     */
    public function determineReferralPercentage(User $upline, int $level): string
    {
        $referralPercentage = config('referrals.standard');
        $existingReferralPercentagesLevelsCount = count($referralPercentage);

        if ($upline->leader) {
            $leaderPercentage = config('referrals.leader');
            $existingReferralPercentagesLevelsCount = count($leaderPercentage);
            return $leaderPercentage[$level - 1] ?? $leaderPercentage[$existingReferralPercentagesLevelsCount - 1];
        }

        return $referralPercentage[$level - 1] ?? $referralPercentage[$existingReferralPercentagesLevelsCount - 1];
    }

    /**
     * Calculate commission
     * @param int $amount
     * @param float $percentage
     * @param int $scale
     * @return string
     */
    private function calculateCommission(int $amount, float $percentage, int $scale): string
    {
        return bcmul(bcdiv($amount, 100, $scale), $percentage);
    }

    /**
     * Fetch referral id's on given level
     * @param array|int $parentIds
     * @return array
     */
    private function getDownline(array $parentIds): array
    {
        $results = [];
        if (!empty($parentIds)) {
            $referrals = User::whereIn('upline', $parentIds)->get();
            foreach ($referrals as $referral) {
                $results[] = $referral->id;
            }
        }

        return $results;
    }

    /**
     * Fetch referral id's on given level with their parents
     * @param array $parentIds
     * @return array
     */
    private function getDownlineWithParent(array $parentIds): array
    {
        $childrenIds = array_values($parentIds);
        $results = [];
        if (!empty($childrenIds)) {
            $mergingArray = [];
            foreach ($childrenIds as $id) {
                $mergingArray = array_merge($mergingArray, $id);
            }
            $childrenIds = $mergingArray;

            $referrals = User::whereIn('upline', $childrenIds)->get();
            foreach ($referrals as $referral) {
                $results[$referral->upline][] = $referral->id;
            }
        }

        return $results;
    }
}
