@extends('layout.authentication.main')

@section('title', __('verify.resend'))

@section('vendors-css')
    <link href="{{ asset('investor/vendors/overhang/overhang.min.css') }}" rel="stylesheet">
@endsection

@section('content')
    <div class="login-box animate__faster">
        <form action="{{ route('resend.post') }}" method="POST" id="resend-form">
            <div class="mb-3">
                <label class="mb-1"><strong>{{ __('common.email') }}</strong></label>
                <input type="text" class="form-control" name="email" id="email"
                       data-rule-email="true" data-rule-minlength="3"
                       data-msg-required="{{ __('common.required') }}"
                       data-msg-email="{{ __('common.wrong_email') }}"
                       data-msg-minlength="{{ __('common.minChr', ['min' => 3]) }}">
                <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                @csrf
            </div>
            <div class="text-center">
                <button type="submit"
                        class="btn btn-primary btn-block">{{ __('verify.resend') }}</button>
            </div>
        </form>
        <div class="new-account mt-3">
            <p>{{ __('common.back') }} <a class="text-primary"
                                          href="{{ route("auth.login") }}">{{ __('signin.sign') }}</a>
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
