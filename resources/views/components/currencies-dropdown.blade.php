<select name="currency" id="currency" class="selectpicker" data-live-search="true" data-width="100%"
        data-msg-required="{{ __('common.required') }}">
    @foreach($currencies as $currency)
        <option value="{{ $currency->id }}"
                data-content='<img src="{{ $currency->icon }}" class="img-32"> {{ $currency->name }} <span class="text-white-50">{{ $currency->chain }}</span>' {{ $currency->enabled == $currency::SUSPENDED ? 'disabled' : ''}}  {{ $currency->id == $usersMainWallet ? 'selected' : ''}}></option>
    @endforeach
</select>
