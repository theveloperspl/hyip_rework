@extends('layout.authentication.main')

@section('title', __('signin.sign'))

@section('content')
    <div class="login-box text-center">
        <img src="{{ asset('investor/images/spam.png') }}" class="img-fluid mt-3 mb-4">
        <h3>{{ __('reset.fail') }}</h3>
        <a href="{{ route("auth.forgot") }}" class="btn btn-primary btn-block mt-5">{{ __('signin.forgot') }}</a>
    </div>
@endsection
