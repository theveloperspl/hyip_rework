<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\PasswordResetRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\PasswordReset;
use App\Services\EmailService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    private EmailService $emailService;
    private UserService $userService;

    public function __construct(EmailService $emailService, UserService $userService)
    {
        $this->emailService = $emailService;
        $this->userService = $userService;
    }

    /**
     * Verify user email based on email and token received from URL
     *
     * @param ForgotPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ForgotPasswordRequest $request): JsonResponse
    {
        $email = $request->email;
        $captcha = $request->captcha;

        if (passedCaptcha($request, $captcha, 'forgot')) {
            $this->emailService->sendPasswordResetEmail($email);
            return response()->json(['type' => 'info', 'message' => __('forgot.generated')]);
        }

        return response()->json(['type' => 'error', 'message' => __('common.captcha')]);
    }

    /**
     * Verify password reset request basing on email and token received from URL
     *
     * @param PasswordResetRequest $request
     * @return View
     */
    public function validatePasswordResetRequest(PasswordResetRequest $request): View
    {
        $email = $request->email;
        $token = $request->token;
        $now = now();

        $passwordReset = PasswordReset::whereEmail($email)->whereToken($token)->first();
        if ($passwordReset && $passwordReset->expires_at >= $now) {
            return view('reset-password', compact('token', 'email'));
        } else {
            return view('reset-failed');
        }
    }

    /**
     * Reset user password
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $captcha = $request->captcha;

        if (passedCaptcha($request, $captcha, 'reset')) {
            return $this->userService->resetPassword($request);
        }

        return response()->json(['type' => 'error', 'message' => __('common.captcha')]);
    }
}
