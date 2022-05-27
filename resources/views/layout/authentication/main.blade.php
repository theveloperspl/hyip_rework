<!DOCTYPE html>
<html lang="{{ Localer::current() }}" dir="{{ Localer::dir() }}" class="h-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0"/>
    <meta name="format-detection" content="telephone=no">
    <title>{{ config('app.name') }} : @yield('title')</title>
    <link rel="shortcut icon" type="image/png" href={{ asset('investor/images/favicon.png') }} />
    <link href="{{ asset('investor/vendors/pace/pace.min.css') }}" rel="stylesheet">
    @yield('vendors-css')
    <link href="{{ asset('investor/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('investor/css/custom.css') }}" rel="stylesheet">
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
                                    <a href="#"><img src="{{ asset('investor/images/logo-full.png') }}" class="img-fluid mx-auto d-block" alt="{{ config('app.name') }}"></a>
                                </div>
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('investor/vendors/jquery-3.6.0/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('investor/vendors/jquery-ui-1.12.1/jquery-ui.min.js') }}"></script>
<script src="{{ asset('investor/vendors/bootstrap-5.0.1-dist/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('investor/vendors/pace/pace.min.js') }}"></script>
<script src="{{ asset('investor/vendors/perfect-scrollbar/js/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('investor/vendors/metismenu/js/metisMenu.min.js') }}"></script>
@yield('vendors-js')
<!-- Other files -->
<script src="{{ asset('investor/js/deznav-init.js') }}"></script>
<script src="{{ asset('investor/js/custom.min.js') }}"></script>
<script src="{{ asset('investor/js/app.js') }}"></script>
@yield('custom-js')

@include('layout.chat')
</body>
</html>
