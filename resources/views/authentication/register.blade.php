@extends('layout.authentication.main')

@section('title', __('register.create'))

@section('vendors-css')
    <link href="{{ asset('investor/vendors/overhang/overhang.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="register-box animate__faster">
        <form action="{{ route('register.post') }}" method="POST" id="register-form">
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.login') }}</strong></label>
                <input type="text" class="form-control" name="username" id="username"
                       data-rule-minlength="3"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 3]) }}">
            </div>
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.email') }}</strong></label>
                <input type="text" class="form-control" name="email" id="email"
                       data-rule-email="true" data-rule-minlength="3"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-email="{{ __('common.wrong_email') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 3]) }}">
            </div>
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.password') }}</strong></label>
                <input type="password" class="form-control" name="password" id="password"
                       data-rule-minlength="8"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 8]) }}"
                       data-msg-pattern="{{ __('common.strong_password') }}"/>
            </div>
            <div class="mb-3">
                <label
                    class="mb-1"><strong>{{ __('common.confirm_password') }}</strong></label>
                <input type="password" class="form-control" name="password_confirmation"
                       id="password_confirmation" data-rule-minlength="8"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 8]) }}"
                       data-msg-pattern="{{ __('common.strong_password') }}"
                       data-msg-equalto="{{ __('common.passwords_not_equal') }}"/>
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                @csrf
            </div>
            @if(config('app.email_verification'))
                <div class="row d-flex justify-content-between mt-4 mb-2">
                    <div class="mb-3"><a
                            href="{{ route("verify.resend") }}">{{ __('verify.resend') }}</a>
                    </div>
                </div>
            @endif
            <div class="text-center">
                <button type="submit"
                        class="btn btn-primary btn-block">{{ __('register.create') }}</button>
            </div>
        </form>
        <div class="new-account mt-3 text-center">
            <p>{{ __('common.back') }} <a class="text-primary"
                                          href="{{ route("auth.login") }}">{{ __('signin.sign') }}</a>
            </p>
        </div>
    </div>
    <div class="success-box text-center d-none">
        <img
            src="{{ config('app.email_verification') ? asset('investor/images/mail.png') : asset('investor/images/check.png') }}"
            class="img-fluid mt-3 mb-4">
        <h3>{{ config('app.email_verification') ? __('register.confirm') : __('register.signin') }}</h3>
        @if(config('app.email_verification'))
            <a href="{{  route('verify.resend') }}"
               class="btn btn-primary btn-block mt-3">{{ __('verify.resend') }}</a>
        @else
            <a href="{{  route('auth.login') }}"
               class="btn btn-primary btn-block mt-3">{{ __('signin.sign') }}</a>
        @endif
    </div>
@endsection

@section('vendors-js')
    <script async src="https://www.google.com/recaptcha/api.js?render={{ config('recaptcha.site_key') }}"></script>
    <script src="{{ asset('investor/vendors/overhang/overhang.min.js') }}"></script>
    <script src="{{ asset('investor/vendors/jquery-validation/jquery.validate.min.js') }}"></script>
    <script src="{{ asset('investor/vendors/jquery-validation/additional-methods.min.js') }}"></script>
@endsection

@section('custom-js')
    <script src="{{ asset('investor/js/auth.js') }}" data-site-key="{{ config('recaptcha.site_key') }}"></script>
@endsection
