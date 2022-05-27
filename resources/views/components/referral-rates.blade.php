@if(config('referrals.enabled'))
    <div id="referralRatesChart" data-rates="{{ implode(",", $rates->toArray()) }}"
         data-level="{{ __('referrals.level_number') }}"></div>
@else
    <h4>{{ __('referrals.disabled') }}</h4>
@endif
