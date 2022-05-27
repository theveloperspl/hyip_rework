<?php

use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TestFieldController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

//TESTS ROUTE
Route::get('test', [TestFieldController::class, 'index']);
////

//handle language change
Route::get('language/{lang}', [LanguageController::class, 'update'])->name('language.update');
//datatables translations url
Route::get('datatables-translations', [LanguageController::class, 'datatablesTranslations'])->name('language.datatables');

//Authentication
Route::middleware('guest')->group(function () {
    //login
    Route::view('login', 'authentication.login')->name('auth.login');
    Route::post('login', [UserController::class, 'authenticate'])->name('login.post');//->middleware('throttle:5,1,login');

    //register
    Route::view('register', 'authentication.register')->name('auth.register');
    Route::post('register', [UserController::class, 'register'])->name('register.post');

    //email verification
    Route::get('verify-account/{token}', [UserController::class, 'verify'])->name('auth.verify');

    //resend verification email
    Route::view('resend-verification-email', 'authentication.resend-verification-email')->name('verify.resend');
    Route::post('resend-verification-email', [UserController::class, 'resendActivationEmail'])->name('resend.post');//->middleware('throttle:1,15,resend-verification');

    //email verification failed
    Route::view('email-verification-failed', 'authentication.email-verification-failed')->name('verify.failed');

    //forgot password
    Route::view('forgot-password', 'authentication.forgot')->name('auth.forgot');
    Route::post('forgot-password', [PasswordResetController::class, 'forgotPassword'])->name('forgot.post');//->middleware('throttle:1,15,forgot-password');

    //reset password
    Route::view('reset-password/{token}', 'authentication.reset-password')->name('auth.reset')->middleware('throttle:5,10,reset-password');
    Route::post('reset-password/{token}', [PasswordResetController::class, 'resetPassword'])->name('reset.post')->middleware('throttle:5,10,reset-password');

    //password reset failed
    Route::view('password-reset-failed', 'authentication.reset-failed')->name('reset.failed');
});

//user panel
Route::middleware(['auth', 'verified', 'suspended', '2fa'])->group(function () {
    Route::post('2fa', [SecurityController::class, 'verifyAuthenticationCode'])->name('2fa.post')->withoutMiddleware('2fa');
});

//logout
Route::get('logout', [UserController::class, 'logout'])->name('auth.logout');
