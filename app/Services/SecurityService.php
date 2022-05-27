<?php

namespace App\Services;

use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use PDF;
use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FAQRCode\Exceptions\MissingQrCodeServiceException;
use PragmaRX\Google2FAQRCode\Google2FA;
use Stevebauman\Location\Facades\Location;

class SecurityService
{
    private const RECOVERY_CODES_AMOUNT = 5;
    private const RETRY_SECONDS = 60 * 60;

    /**
     * Create details needed to set up 2FA for user
     *
     * @param User $user
     * @return array
     * @throws MissingQrCodeServiceException
     * @throws IncompatibleWithGoogleAuthenticatorException
     * @throws InvalidCharactersException
     * @throws SecretKeyTooShortException
     */
    public function prepareTemporarySecondFactorSetup(User $user): array
    {
        $setup = [];
        $google2fa = new Google2FA();

        $secretKey = $google2fa->generateSecretKey();
        $qrCode = $google2fa->getQRCodeInline(
            config('app.name'),
            $user->username,
            $secretKey
        );
        $recoveryCodes = $this->generateRecoveryCodes();

        //add elements to setup array
        $setup['secret_key'] = $secretKey;
        $setup['qr_code'] = $qrCode;
        $setup['recovery_codes'] = $recoveryCodes;
        //flash setup array into session for future use
        session()->put('second-factor-setup', $setup);

        return $setup;
    }

    /**
     * Setup 2FA for user using temporary setup details
     *
     * @param User $user
     * @return void
     */
    public function setupSecondFactorAuthentication(User $user): void
    {
        $setup = session()->get('second-factor-setup');

        if (!$user->security) {
            //insert second factor details into table
            $user->security()->create([
                'second_factor_secret' => $setup['secret_key'],
                'second_factor_recovery_codes' => $setup['recovery_codes']
            ]);
        }

        //update user second factor status to enabled
        $user->update([
            'second_factor' => 1
        ]);

        //remove setup details from session
        session()->remove('second-factor-setup');

        //set middleware session to prevent user logout
        session()->put('second_factor_validated', true);
    }

    /**
     * Generate PDF document with user recovery codes
     *
     * @param User $user
     * @param $setup
     * @return Response
     */
    public function generateRecoveryCodesPDF(User $user, $setup): Response
    {
        $codes = $setup['recovery_codes'];
        //prepare file name
        $platformName = strtolower(config('app.name'));
        $userName = strtolower($user->username);
        $fileName = "{$platformName}-{$userName}-recovery-codes.pdf";
        //generate pdf
        $generatePDF = PDF::loadView('recovery_codes_pdf', compact('codes', 'fileName'));

        return $generatePDF->download($fileName);
    }

    /**
     * Update user 2FA status
     *
     * @param User $user
     * @param bool $enabled
     * @return void
     */
    public function updateSecondFactorAuthenticationStatus(User $user, bool $enabled): void
    {
        $user->second_factor = $enabled;
        $user->save();
    }

    /**
     * Insert authentication attempt into database
     *
     * @param Request $request
     * @param RateLimiter $rateLimiter
     * @param string $cacheKey
     * @param int $status
     * @return void
     */
    public function logSecondFactorAttempt(Request $request, RateLimiter $rateLimiter, string $cacheKey, int $status): void
    {
        $additionalInformation = json_encode([
            'action' => str_replace('App\\Http\\Controllers\\', '', $request->route()->action['controller']),
            'authentication_code' => $request->code
        ]);
        $this->createSecurityLog($request->user()->id, $request->ip(), $request->server('HTTP_USER_AGENT'), '2fa', $status, $additionalInformation);

        $status ? $this->resetSecondFactorLimit($rateLimiter, $cacheKey) : $this->incrementSecondFactorLimit($rateLimiter, $cacheKey);
    }

    /**
     * Create security log
     *
     * @param int $userId
     * @param string $ipAddress
     * @param string $userAgent
     * @param string $type
     * @param int $status
     * @param string|null $additionalInformation
     * @return void
     */

    public function createSecurityLog(int $userId, string $ipAddress, string $userAgent, string $type, int $status, string $additionalInformation = null): void
    {
        $country = $this->getCountryCode($ipAddress);
        //add log
        $securityLog = new SecurityLog();
        $securityLog->user_id = $userId;
        $securityLog->ip = $ipAddress;
        $securityLog->country = strtolower($country);
        $securityLog->user_agent = $userAgent;
        $securityLog->type = $type;
        $securityLog->status = $status;
        $securityLog->additional = $additionalInformation;
        $securityLog->save();
    }

    /**
     * Generate unique recovery code
     *
     * @return array
     */
    private function generateRecoveryCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < self::RECOVERY_CODES_AMOUNT; $i++) {
            $unique = false;
            while (!$unique) {
                $code = rand(10000000, 99999999);
                $checkCodesForCode = in_array($code, $codes);
                if (!$checkCodesForCode) {
                    $unique = true;
                    $codes[] = $code;
                }
            }
        }

        return $codes;
    }

    /**
     * Get country code from IP address
     *
     * @param string $ip
     * @return ?string
     */
    private function getCountryCode(string $ip): ?string
    {
        $location = Location::get($ip);

        return $location->countryCode ?? null;
    }

    /**
     * Add second factor check try to global limit
     *
     * @param RateLimiter $rateLimiter
     * @param string $cacheKey
     */
    private function incrementSecondFactorLimit(RateLimiter $rateLimiter, string $cacheKey): void
    {
        $rateLimiter->hit($cacheKey, self::RETRY_SECONDS);
    }

    /**
     * Remove second factor check tries limit after successful validation
     *
     * @param RateLimiter $rateLimiter
     * @param string $cacheKey
     */
    private function resetSecondFactorLimit(RateLimiter $rateLimiter, string $cacheKey): void
    {
        $rateLimiter->clear($cacheKey);
    }

    /**
     * Update user security settings
     *
     * @param User $user
     * @param bool $signin
     * @param bool $withdrawal
     * @param bool $password_change
     * @param bool $wallets
     */
    public function updateSecuritySettings(User $user, bool $signin, bool $withdrawal, bool $password_change, bool $wallets): void
    {
        $user->security()->update([
            'signin' => $signin,
            'withdrawal' => $withdrawal,
            'password_change' => $password_change,
            'wallets' => $wallets,
        ]);
    }
}
