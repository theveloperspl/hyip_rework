<div class="col-12 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3">
    <div class="card">
        <div class="card-header border-0 pb-0">
            <h5 class="card-title">{{ __('invest.error') }}</h5>
        </div>
        <div class="card-body">
            <div class="col-12 text-center">
                <img src="{{ asset('images/error.png') }}">
                <h3 class="mt-4 mb-3">{{ $message ?? __('invest.error') }}</h3>
            </div>
        </div>
    </div>
</div>
