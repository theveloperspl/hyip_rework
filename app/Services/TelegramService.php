<?php

namespace App\Services;

use App\Classes\Converter;
use App\Models\Investment;
use App\Models\ReferralCommission;
use App\Models\TelegramSettings;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Telegram\Bot\Exceptions\TelegramSDKException;
use Telegram\Bot\Helpers\Emojify;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramService
{
    private const COMMISSION_IMG = 'AgACAgQAAx0EWs53hQADC2EAASD7cpW1jNYGxC0CqtzIsI7kuwACP7YxGyuWAVDi3ByuvlRRcAEAAwIAA3kAAyAE';
    private const INVESTMENT_IMG = 'AgACAgQAAx0EWs53hQADDGEAASEony53kg207z1cVhVRXPE6NwACQLYxGyuWAVBm3FisYvZXIQEAAwIAA3kAAyAE';
    private const PROCESSED_IMG = 'AgACAgQAAx0EWs53hQADDWEAASFBQuUAAZC4YWwNUMbCwsXVM1cAAkG2MRsrlgFQBDb9DYpVd8kBAAMCAAN5AAMgBA';
    private const RESET_IMG = 'AgACAgQAAx0EWs53hQADDmEAASFhWlfSXeiOdCQdb2cbdI91bgACQrYxGyuWAVBuM7BiFOncYwEAAwIAA3kAAyAE';
    private const TEAM_IMG = 'AgACAgQAAx0EWs53hQADGGEAAaNq8ZWbGs8iMi4cz3Pir587dgACKbYxGyuWCVAAARH10rbRo2kBAAMCAAN5AAMgBA';
    private const SECURITY_IMG = 'AgACAgQAAx0EWs53hQADGWEAAaObJfGDAdhaVFAAAXNdZyfx3igAAiq2MRsrlglQkxEN831kxbUBAAMCAAN5AAMgBA';

    /**
     * Get user Telegram connection token
     *
     * @param User $user
     * @return string
     */
    public function getTelegramConnectionToken(User $user): string
    {
        $userTelegramSettings = $user->telegram;
        if (!$userTelegramSettings || !$userTelegramSettings->connection_token) {
            $token = $this->generateConnectionToken();
            $this->setUserConnectionToken($user, $token);
        } else {
            $token = $userTelegramSettings->connection_token;
        }

        return $token;
    }

    /**
     * Set user connection token
     *
     * @param User $user
     * @param string $token
     * @return void
     */
    public function setUserConnectionToken(User $user, string $token): void
    {
        TelegramSettings::updateOrCreate(
            ['user_id' => $user->id],
            ['connection_token' => $token]
        );
    }

    /**
     * Set user telegram account
     *
     * @param User $user
     * @param string|null $account
     * @return void
     */
    public function setUserTelegramAccount(User $user, string $account = null): void
    {
        $user->telegram()->update([
            'account' => $account
        ]);
    }

    /**
     * Send notification about new referral
     *
     * @param User $user
     * @param User $upline
     * @return void
     */
    public function sendNewReferralNotification(User $user, User $upline): void
    {
        //prepare caption
        $caption = 'We are glad to inform you that your direct referral structure has grown by another member!' . PHP_EOL . PHP_EOL;
        $caption .= "Username: {$user->username}" . PHP_EOL;
        $caption .= "Joined on: {$user->created_at}" . PHP_EOL;
        //deploy message
        try {
            Telegram::sendPhoto([
                'chat_id' => $upline->telegram->account,
                'photo' => self::TEAM_IMG,
                'caption' => $caption,
                'parse_mode' => 'html'
            ]);
        } catch (TelegramSDKException $e) {
            $this->administratorErrorNotification($e->getMessage(), $e->getFile(), $e->getLine(), ['event' => 'NewReferral', 'user' => $user->id, 'upline' => $upline->id, 'chat_id' => $upline->telegram->account]);
        }
    }

    /**
     * Send notification about password reset
     *
     * @param User $user
     * @return void
     */
    public function sendPasswordResetNotification(User $user)
    {
        //prepare caption
        $caption = 'This message serves as a confirmation that your account password was recently changed using *Forgot password* option.' . PHP_EOL . PHP_EOL;
        $caption .= '*If this was not you who made this change please contact support immediately as your account may have been compromised!*' . PHP_EOL;
        //deploy message
        try {
            Telegram::sendPhoto([
                'chat_id' => $user->telegram->account,
                'photo' => self::RESET_IMG,
                'caption' => $caption,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            $this->administratorErrorNotification($e->getMessage(), $e->getFile(), $e->getLine(), ['event' => 'PasswordReset', 'user' => $user->id, 'chat_id' => $user->telegram->account]);
        }
    }

    /**
     * Send error notification to errors channel
     *
     * @param Investment $investment
     * @return void
     */
    public function sendDepositConfirmationNotification(Investment $investment)
    {
        $user = $investment->user;
        $currency = ucfirst($investment->currency->long);
        $plan = ucfirst($investment->plan);
        //prepare photo caption
        $caption = 'We are glad to inform you that your deposit was confirmed and is now active!' . PHP_EOL . PHP_EOL;
        $caption .= "*ID:* {$investment->uuid}" . PHP_EOL;
        $caption .= "*Amount:* {$investment->converted_amount} {$investment->currency->currency_code}" . PHP_EOL;
        $caption .= "*Currency:* {$currency}" . PHP_EOL;
        $caption .= "*Plan:* {$plan}" . PHP_EOL;
        $caption .= "*Created at:* {$investment->created_at}" . PHP_EOL;
        $caption .= "*Activated at:* {$investment->updated_at}" . PHP_EOL;
        //deploy message
        try {
            Telegram::sendPhoto([
                'chat_id' => $user->telegram->account,
                'photo' => self::INVESTMENT_IMG,
                'caption' => $caption,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            $this->administratorErrorNotification($e->getMessage(), $e->getFile(), $e->getLine(), ['event' => 'NewDeposit', 'user' => $user->id, 'chat_id' => $user->telegram->account, 'deposit' => $investment->uuid]);
        }
    }

    /**
     * Send new commission notification to upline
     *
     * @param User $upline
     * @param ReferralCommission $commission
     * @return void
     */
    public function sendNewCommissionNotification(User $upline, ReferralCommission $commission)
    {
        $currency = ucfirst($commission->currency->long);
        //prepare photo caption
        $caption = 'We are glad to inform you that you received referral commission!' . PHP_EOL . PHP_EOL;
        $caption .= "*Amount:* {$commission->converted_commission} {$commission->currency->currency_code}" . PHP_EOL;
        $caption .= "*Currency:* {$currency}" . PHP_EOL;
        $caption .= "*Referral:* {$commission->referral->username}" . PHP_EOL;
        $caption .= "*Level:* {$commission->level}" . PHP_EOL;
        $caption .= "*Added at:* {$commission->created_at}" . PHP_EOL;
        //deploy message
        try {
            Telegram::sendPhoto([
                'chat_id' => $upline->telegram->account,
                'photo' => self::COMMISSION_IMG,
                'caption' => $caption,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            $this->administratorErrorNotification($e->getMessage(), $e->getFile(), $e->getLine(), ['event' => 'NewCommission', 'user' => $upline->id, 'chat_id' => $upline->telegram->account, 'commission' => $commission->id]);
        }
    }

    /**
     * Send withdrawal address change notification to user
     *
     * @param User $user
     * @param Wallet $wallet
     * @return void
     */
    public function sendWithdrawalAddressChangedNotification(User $user, Wallet $wallet)
    {
        //prepare photo caption
        $caption = 'One of your withdrawal wallets was recently changed. You will find details below.' . PHP_EOL . PHP_EOL;
        $caption .= "*Currency:* {$wallet->currency->name}" . PHP_EOL;
        $caption .= "*New address:* {$wallet->address}" . PHP_EOL;
        if ($wallet->destination_tag) {
            $caption .= "*New destination tag:* {$wallet->destination_tag}" . PHP_EOL;
        }
        $caption .= "*Updated at:* {$wallet->updated_at}" . PHP_EOL;
        $caption .= '*If this was not you who made this change please contact support immediately as your account may have been compromised!*' . PHP_EOL;
        //deploy message
        try {
            Telegram::sendPhoto([
                'chat_id' => $user->telegram->account,
                'photo' => self::SECURITY_IMG,
                'caption' => $caption,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            $this->administratorErrorNotification($e->getMessage(), $e->getFile(), $e->getLine(), ['event' => 'WithdrawalAddressChanged', 'user' => $user->id, 'chat_id' => $user->telegram->account]);
        }
    }

    /**
     * Send error notification to errors channel
     *
     * @param $error
     * @param $file
     * @param $line
     * @param array $additionalData
     * @return void
     */
    public function administratorErrorNotification($error, $file, $line, array $additionalData = [])
    {
        //prepare photo caption
        $message = '*SYSTEM ERROR:*' . PHP_EOL;
        $message .= "ERROR: {$error}" . PHP_EOL;
        $message .= "FILE: {$file}" . PHP_EOL;
        $message .= "LINE: {$line}" . PHP_EOL;
        if (!empty($additionalData)) {
            $additionalData = json_encode($additionalData);
            $message .= "ADDITIONAL DATA: {$additionalData}" . PHP_EOL;
        }
        //deploy message
        try {
            Telegram::sendMessage([
                'chat_id' => config('telegram.errors_channel'),
                'text' => $message,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            Log::emergency("TELEGRAM EXCEPTION: {$e->getMessage()} File: {$e->getFile()} on line: {$e->getLine()}");
        }
    }

    /**
     * Send error notification to errors channel
     *
     * @param Investment $investment
     * @return void
     */
    public function administratorDepositNotification(Investment $investment)
    {
        $user = $investment->user;
        $currency = ucfirst($investment->currency->long);
        $plan = ucfirst($investment->plan);
        //prepare message
        $message = ':moneybag: *New deposit:*' . PHP_EOL;
        $message .= "*UUID:* {$investment->uuid}" . PHP_EOL;
        $message .= "*Amount:* {$investment->converted_amount} {$investment->currency->currency_code}" . PHP_EOL;
        if ($currency !== '$') {
            $usd = Converter::toDollars($investment->usd);
            $message .= "*USD value:* {$usd}$" . PHP_EOL;
        }
        $message .= "*Currency:* {$currency}" . PHP_EOL;
        $message .= "*User:* {$user->username} ({$user->id})" . PHP_EOL;
        $message .= "*Plan:* {$plan}" . PHP_EOL;
        $message .= "*Deposited at:* {$investment->updated_at}" . PHP_EOL;
        $message = Emojify::text($message);
        //deploy message
        try {
            Telegram::sendMessage([
                'chat_id' => config('telegram.transactions_channel'),
                'text' => $message,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            Log::emergency("TELEGRAM EXCEPTION: {$e->getMessage()} File: {$e->getFile()} on line: {$e->getLine()}");
        }
    }

    /**
     * Send notification to admin channel when queue process is restarted or launched again
     *
     * @return void
     */
    public function administratorQueueNotification($type = 'launched')
    {
        $message = ':rocket: *QUEUE WAS LAUNCHED* :rocket:' . PHP_EOL;
        if ($type === 'restarted') {
            $message = ':rotating_light: *QUEUE WAS RESTARTED* :rotating_light:' . PHP_EOL;
        }

        $message = Emojify::text($message);
        //deploy message
        try {
            Telegram::sendMessage([
                'chat_id' => config('telegram.errors_channel'),
                'text' => $message,
                'parse_mode' => 'markdown'
            ]);
        } catch (TelegramSDKException $e) {
            Log::emergency("TELEGRAM EXCEPTION: {$e->getMessage()} File: {$e->getFile()} on line: {$e->getLine()}");
        }
    }

    /**
     * Generate new, unique connection token
     *
     * @return string
     */
    private function generateConnectionToken(): string
    {
        $token = __('common.wrong');
        $unique = false;

        while (!$unique) {
            $token = Str::random(32);
            $checkTelegramSettingsForConnectionToken = TelegramSettings::whereConnectionToken($token)->first();
            if (!$checkTelegramSettingsForConnectionToken) {
                $unique = true;
            }
        }

        return $token;
    }
}
