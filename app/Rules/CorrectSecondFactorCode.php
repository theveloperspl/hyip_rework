<?php

namespace App\Rules;

use App\Services\SecurityService;
use Exception;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FAQRCode\Google2FA;

class CorrectSecondFactorCode implements Rule
{
    private const MAX_ATTEMPTS = 3;

    private Request $request;
    private RateLimiter $rateLimiter;
    private SecurityService $securityService;

    private string $errorMessage = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(RateLimiter $rateLimiter, Request $request)
    {
        $this->rateLimiter = $rateLimiter;
        $this->request = $request;
        $this->securityService = new SecurityService();
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
        $user = $this->request->user();
        $cacheKey = "{$user->id}:second_factor_attempt";

        //limit request to prevent brute force attacks
        if ($this->rateLimiter->tooManyAttempts($cacheKey, self::MAX_ATTEMPTS)) {
            $minutes = gmdate('i:s', $this->rateLimiter->availableIn($cacheKey));
            $this->errorMessage = __('errors.throttled', ['minutes' => $minutes]);
            return false;
        }

        $this->errorMessage = __('security.invalid_code');

        if (strlen($value) === 6) {
            $google2FA = new Google2FA();

            try {
                $valid = $google2FA->verifyKey($user->security->second_factor_secret, $value);
                if ($valid) {
                    $this->securityService->logSecondFactorAttempt($this->request, $this->rateLimiter, $cacheKey, 1);
                    return true;
                }
            } catch (Exception $exception) {
                $this->securityService->logSecondFactorAttempt($this->request, $this->rateLimiter, $cacheKey, 0);
                return false;
            }

            $this->securityService->logSecondFactorAttempt($this->request, $this->rateLimiter, $cacheKey, 0);
            return false;
        }

        if (strlen($value) === 8) {
            $valid = false;
            $recoveryCodes = $user->security->second_factor_recovery_codes;
            foreach ($recoveryCodes as $index => $recoveryCode) {
                if (Hash::check($value, $recoveryCode)) {
                    //remove used code
                    unset($recoveryCodes[$index]);
                    //reindex array to prevent addition of its keys into database
                    $recoveryCodes = array_values($recoveryCodes);
                    //update user available recovery codes (use QueryBuilder update method to prevent setSecondFactorRecoveryCodesAttribute() from running on SecuritySettings model and encrypting recovery codes again)
                    $user->security()->update([
                        'second_factor_recovery_codes' => $recoveryCodes
                    ]);
                    $valid = true;
                }
            }

            $this->securityService->logSecondFactorAttempt($this->request, $this->rateLimiter, $cacheKey, $valid);
            return $valid;
        }

        $this->securityService->logSecondFactorAttempt($this->request, $this->rateLimiter, $cacheKey, 0);
        return false;
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
