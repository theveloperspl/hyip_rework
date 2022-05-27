<!DOCTYPE html>
<html lang="{{ Localer::current() }}" dir="{{ Localer::dir() }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="format-detection" content="telephone=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ config('app.name') }} : {{ __('errors.404') }}</title>
    <link rel="shortcut icon" type="image/png" href={{ asset('images/favicon.png') }} />
    <link href="{{ asset('vendors/pace/pace.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
</head>
<body class="vh-100 full-bg">
<div class="authentication-page h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100 align-items-center">
            <div class="col-md-6">
                <div class="authentication-page-content">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form">
                                <div class="text-center mb-4">
                                    <a href="#"><img src="{{ asset('images/logo-full.png') }}"
                                                     class="img-fluid mx-auto d-block"
                                                     alt="{{ config('app.name') }}"></a>
                                </div>
                                <div class="success-box text-center">
                                    <img src="{{ asset('images/reviews.png') }}" class="img-fluid">
                                    <h3 class="mt-4 mb-5">{{ __('errors.404') }}</h3>
                                    <button class="btn btn-primary btn-block"
                                            onclick="window.history.back();">{{ __('buttons.back') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('vendors/jquery-3.6.0/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('vendors/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script src="{{ asset('vendors/bootstrap-5.0.1-dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('vendors/pace/pace.min.js') }}"></script>
<script src="{{ asset('vendors/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('vendors/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('js/custom.min.js') }}"></script>
<script src="{{ asset('js/deznav-init.js') }}"></script>
@include('layout.chat')
</body>
</html>
