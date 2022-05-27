<div class="col-12">
    @forelse($currencies as $currency)
        <div class="media pb-3 border-bottom mb-3 align-items-center">
            <div class="media-image me-2">
                <img src="{{ $currency->icon }}" class="me-2"
                     alt="{{$currency->name}}">
            </div>
            <div class="media-body">
                <h6 class="fs-16 mb-0">{{$currency->name}}</h6>
            </div>
        </div>
    @empty
        <h2>{{ __('invest.no_methods') }}</h2>
    @endforelse
</div>
