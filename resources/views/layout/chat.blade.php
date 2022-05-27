<script>
    document.tidioChatLang = '{! Localer::current() }}';
    @auth
        document.tidioIdentify = {
        distinct_id: '{{ auth()->user()->id }}',
        email: '{{ auth()->user()->email }}',
        name: '{{ auth()->user()->username }}'
    };
    @endauth
</script>

<script src="//code.tidio.co/oe4jhwfuauwfparcqnenvt5fmfg6wfiy.js" async></script>
