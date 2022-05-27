@extends('layout.authentication.main')

@section('title', __('reset.reset'))

@section('vendors-css')
    <link href="{{ asset('investor/vendors/overhang/overhang.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="login-box animate__faster">
        <form action="{{ route('reset.post') }}" method="POST" id="reset-form">
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
                <input type="hidden" name="token" id="token" value="{{ $token }}">
                <input type="hidden" name="email" id="email" value="{{ $email }}">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                @csrf
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn btn-primary btn-block">{{ __('reset.reset') }}</button>
            </div>
        </form>
        <div class="new-account mt-3 text-center">
            <p>{{ __('common.back') }} <a class="text-primary" href="{{ route("auth.login") }}">{{ __('signin.sign') }}</a>
            </p>
        </div>
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
