<?php

namespace App\Services;

use App\Interfaces\MailingInterface;
use App\Models\EmailVerification;
use App\Models\Investment;
use App\Models\PasswordReset;
use App\Models\ReferralCommission;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmailService
{
    private MailingInterface $mailer;

    public function __construct(MailingInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send registration confirmation email
     * @param User $user
     * @return void
     */
    public function sendRegistrationConfirmationEmail(User $user)
    {
        $title = "Account created";
        $message = view('emails.registered', ['title' => $title, 'username' => $user->username])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($user->email, $user->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send account activation email
     * @param User $user
     * @return void
     */
    public function sendAccountActivationEmail(User $user)
    {
        //generate random verification token todo refactor into service
        $token = Str::random(32);
        //insert into database
        $emailVerification = new EmailVerification();
        $emailVerification->email = $user->email;
        $emailVerification->token = $token;
        $emailVerification->expires_at = Carbon::now()->addHours(24);
        $emailVerification->save();
        //prepare message with user data
        $title = "Email confirmation";
        $message = view('emails.confirm', ['title' => $title, 'username' => $user->username, 'token' => $token, 'email' => $user->email])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($user->email, $user->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send password reset email
     * @param string $email
     * @return void
     */
    public function sendPasswordResetEmail(string $email)
    {
        $user = User::whereEmail($email)->first();
        if ($user) {
            //generate random verification token todo refactor into service
            $token = Str::random(32);
            //insert into database
            $passwordReset = new PasswordReset();
            $passwordReset->email = $user->email;
            $passwordReset->token = $token;
            $passwordReset->expires_at = Carbon::now()->addHour();
            $passwordReset->save();
            //prepare message with user data
            $title = "Password reset";
            $message = view('emails.reset', ['title' => $title, 'username' => $user->username, 'token' => $token, 'email' => $user->email])->render();
            //deploy verification email
            $this->mailer->from(config('app.email'), config('app.name'));
            $this->mailer->to($user->email, $user->username);
            $this->mailer->subject($title);
            $this->mailer->message($message);
            $this->mailer->send();
        }
    }

    /**
     * Send password reset confirmation email
     * @param User $user
     * @return void
     */
    public function sendResetConfirmationEmail(User $user)
    {
        //prepare message with received data
        $title = "Password changed";
        $message = view('emails.reset_done', ['title' => $title, 'username' => $user->username])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($user->email, $user->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send new direct referral email
     * @param User $referral
     * @param User $upline
     * @return void
     */
    public function sendNewReferralEmail(User $referral, User $upline)
    {
        //prepare message with received data
        $title = "New referral";
        $message = view('emails.referral', ['title' => $title, 'username' => $upline->username, 'referral' => $referral->username, 'joined' => $referral->created_at])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($upline->email, $upline->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send new referral commission email
     * @param User $upline
     * @param ReferralCommission $commission
     * @return void
     */
    public function sendNewCommissionEmail(User $upline, ReferralCommission $commission)
    {
        //prepare message with received data
        $title = "New referral commission";
        $message = view('emails.commission', ['title' => $title, 'username' => $upline->username, 'commission' => $commission, 'currencyCode' => $commission->currency->currency_code])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($upline->email, $upline->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send new referral commission email
     * @param Investment $investment
     * @return void
     */
    public function sendNewDepositEmail(Investment $investment)
    {
        //prepare message with received data
        $title = "Deposit confirmed";
        $message = view('emails.deposit', ['title' => $title, 'username' => $investment->user->username, 'investment' => $investment, 'currencyCode' => $investment->currency->currency_code])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($investment->user->email, $investment->user->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }

    /**
     * Send new referral commission email
     * @param User $user
     * @param Wallet $wallet
     * @return void
     */
    public function sendWithdrawalAddressChangedEmail(User $user, Wallet $wallet)
    {
        //prepare message with received data
        $title = "Withdrawal wallet changed";
        $message = view('emails.address_changed', ['title' => $title, 'username' => $user->username, 'wallet' => $wallet])->render();
        //deploy verification email
        $this->mailer->from(config('app.email'), config('app.name'));
        $this->mailer->to($user->email, $user->username);
        $this->mailer->subject($title);
        $this->mailer->message($message);
        $this->mailer->send();
    }
}
