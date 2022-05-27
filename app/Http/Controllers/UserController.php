<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\AuthenticateUserRequest;
use App\Http\Requests\Authentication\CreateUserRequest;
use App\Http\Requests\Authentication\ResendActivationEmailRequest;
use App\Http\Requests\Authentication\VerifyEmailRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Authenticate user
     *
     * @param AuthenticateUserRequest $request
     * @return JsonResponse
     */
    public function authenticate(AuthenticateUserRequest $request): JsonResponse
    {
        $username = $request->username;
        $password = $request->password;
        $captcha = $request->captcha;

        if (passedCaptcha($request, $captcha, 'login')) {
            return $this->userService->authenticateUser($request, $username, $password);
        }

        return response()->json(['type' => 'error', 'message' => __('common.captcha')]);
    }

    /**
     * Create new user
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function register(CreateUserRequest $request): JsonResponse
    {
        $captcha = $request->captcha;

        if (passedCaptcha($request, $captcha, 'register')) {
            return $this->userService->createAccount($request);
        }

        return response()->json(['type' => 'error', 'message' => __('common.captcha')]);
    }

    /**
     * Verify user email based on email and token received from URL
     *
     * @param VerifyEmailRequest $request
     * @return RedirectResponse
     */
    public function verify(VerifyEmailRequest $request): RedirectResponse
    {
        $token = $request->token;
        $email = $request->email;

        return $this->userService->verifyEmail($email, $token);
    }

    /**
     * Resend activation email
     *
     * @param ResendActivationEmailRequest $request
     * @return JsonResponse
     */
    public function resendActivationEmail(ResendActivationEmailRequest $request): JsonResponse
    {
        $email = $request->email;
        $captcha = $request->captcha;

        if (passedCaptcha($request, $captcha, 'resend')) {
            return $this->userService->resendActivationEmail($email);
        }

        return response()->json(['type' => 'error', 'message' => __('common.captcha')]);
    }

    /**
     * Get the path the user should be redirected to when they use logout route
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->logout();
        $request->session()->flush();
        return redirect()->route('auth.login');
    }
}
