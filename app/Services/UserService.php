<?php

namespace App\Services;

use App\Events\CurrencyModified;
use App\Events\NewReferral;
use App\Events\UserCreated;
use App\Http\Requests\Authentication\AuthenticateUserRequest;
use App\Http\Requests\Authentication\CreateUserRequest;
use App\Models\EmailVerification;
use App\Models\MailingSettings;
use App\Models\PasswordReset;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class UserService
{
    private SecurityService $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Attempt to authenticate user based on credential
     * @param AuthenticateUserRequest $request
     * @param string $username
     * @param string $password
     * @return JsonResponse
     */
    public function authenticateUser(AuthenticateUserRequest $request, string $username, string $password): JsonResponse
    {
        $response = response()->json(['type' => 'error', 'message' => __('signin.wrong')]);
        if (Auth::attempt(array('username' => $username, 'password' => $password))) {
            $user = auth()->user();
            $status = $user->status;
            if ($status === User::SUSPENDED) {
                $response = response()->json(['type' => 'error', 'message' => __('signin.suspended')]);
            } else if ($status === User::UNVERIFIED) {
                $response = response()->json(['type' => 'info', 'message' => __('signin.activate')]);
            } else if ($status === User::ACTIVE) {
                if ($user->second_factor && $user->security->signin) {
                    $response = response()->json(['action' => '2fa']);
                } else {
                    //log successful attempt
                    $this->securityService->createSecurityLog($user->id, $request->ip(), $request->server('HTTP_USER_AGENT'), 'authentication', 1);

                    $response = response()->json(['action' => 'redirect', 'url' => route('panel.dashboard')]);
                }
            }
        } else {
            //log failed attempt
            $user = User::whereUsername($username)->first();
            if (!empty($user)) {
                $this->securityService->createSecurityLog($user->id, $request->ip(), $request->server('HTTP_USER_AGENT'), 'authentication', 0);
            }
        }

        return $response;
    }

    /**
     * Create new user account
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function createAccount(CreateUserRequest $request): JsonResponse
    {
        $upline = 0;
        //check if referral cookie exists
        if (Cookie::has('referral')) {
            //check if user from cookie exists
            $uplineCheck = User::whereUsername(Cookie::get('referral'))->first();
            if ($uplineCheck) {
                $upline = $uplineCheck->id;
            }
        }
        //create user
        config('app.email_verification') ? $status = '0' : $status = '1';
        $user = new User();
        $user->username = $request->username;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->upline = $upline;
        $user->status = $status;
        $user->save();
        //add user to other tables
        $id = $user->id;
        MailingSettings::create(['user_id' => $id]);
        //deploy user created event
        event(new UserCreated($user));
        //deploy new referral event
        if ($upline !== 0) {
            //fetch upline user object for event processing
            $upline = User::find($upline);
            event(new NewReferral($user, $upline));
        }

        return response()->json(['type' => 'success']);
    }

    /**
     * Verify user email and account
     * @param string $email
     * @param string $token
     * @return RedirectResponse
     */
    public function verifyEmail(string $email, string $token): RedirectResponse
    {
        $emailVerification = EmailVerification::whereEmail($email)->whereToken($token);
        $fetchedEmailVerification = $emailVerification->first();
        $now = now();

        if ($fetchedEmailVerification && $fetchedEmailVerification->expires_at >= $now) {
            //update user status
            $user = User::whereEmail($email)->first();
            $user->update(['status' => '1']);
            //delete verification data
            $emailVerification->delete();
            //set user session
            Auth::login($user);
            //redirect to dashboard
            return redirect()->route('panel.dashboard');
        }

        return redirect()->route('verify.failed');
    }

    /**
     * Reset user password
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $passwordReset = PasswordReset::whereEmail($request->email)->whereToken($request->token);
        $fetchedPasswordReset = $passwordReset->first();
        $now = now();

        if ($fetchedPasswordReset && $fetchedPasswordReset->expires_at >= $now) {
            $user = User::whereEmail($request->email)->first();
            if ($user) {
                $this->updatePassword($user, $request->password);
            }
            $passwordReset->delete();
            //create successful log
            $this->securityService->createSecurityLog($user->id, $request->ip(), $request->server('HTTP_USER_AGENT'), 'password', 1);
            event(new \App\Events\PasswordReset($user));
            return response()->json(['type' => 'success', 'message' => __('reset.success')]);
        }

        return response()->json(['type' => 'error', 'message' => __('reset.fail')]);
    }

    /**
     * Update a user password
     * @param string $email
     * @return JsonResponse
     */
    public function resendActivationEmail(string $email): JsonResponse
    {
        $user = User::whereEmail($email)->first();

        if (config('app.email_verification') && $user && $user->status === '0') {
            event(new UserCreated($user));
        }

        return response()->json(['type' => 'success', 'message' => __('verify.sent')]);
    }

    /**
     * Update a user password
     * @param User $user
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = $password;
        $user->update();
    }

}
