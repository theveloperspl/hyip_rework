@extends('layout.authentication.main')

@section('title', __('signin.sign'))

@section('vendors-css')
    <link href="{{ asset('investor/vendors/overhang/overhang.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="login-box animate__faster">
        <form action="{{ route('login.post') }}" method="POST" id="login-form">
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.login') }}</strong></label>
                <input type="text" class="form-control" name="login" id="login"
                       data-rule-minlength="3"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 3]) }}">
            </div>
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.password') }}</strong></label>
                <input type="password" class="form-control" name="password" id="password"
                       data-rule-minlength="8"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 8]) }}"
                       data-msg-pattern="{{ __('common.strong_password') }}"/>
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                @csrf
            </div>
            <div class="row d-flex justify-content-between mt-4 mb-2">
                <div class="mb-3">
                    <a href="{{ route("auth.forgot") }}">{{ __('signin.forgot') }}</a>
                </div>
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn btn-primary btn-block">{{ __('signin.sign') }}</button>
            </div>
        </form>
        <div class="new-account mt-3">
            <p>{{ __('signin.noacc') }}
                <a class="text-primary" href="{{ route("auth.register") }}">{{ __('signin.register') }}</a>
            </p>
        </div>
    </div>
    <div class="factor-box d-none">
        <form action="{{ route('2fa.post') }}" method="POST" id="sfa-form">
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('security.code') }}</label>
                <input type="text" class="form-control" name="code" id="code"
                       data-rule-minlength="6" data-rule-maxlength="8"
                       data-rule-digits=”true”
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 6]) }}"
                       data-msg-maxlength="{{ __('common.maxChr', ['max' => 8]) }}"
                       data-msg-digits="{{ __('common.digits') }}"/>
                @csrf
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn btn-primary btn-block">{{ __('security.verify') }}</button>
            </div>
        </form>
    </div>
    <div class="success-box text-center d-none">
        <img src="{{ asset('investor/images/check.png') }}" class="img-fluid animate__animated animate__pulse">
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
