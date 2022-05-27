<!-- Language switcher -->
<div class="row">
    <div class="col-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4 col-xl-4 offset-xl-4 mfp-hide" id="language-switcher">
        <div class="card">
            <div class="card-body text-center">
                <div class="row">
                    @foreach (array_keys(config('app.supported_locales')) as $lang)
                        <div class="col">
                            <a href="{{ route('language.update', ['lang' => $lang]) }}"><img
                                    src="{{ asset("images/language/$lang.png") }}" class="img-fluid"></a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
