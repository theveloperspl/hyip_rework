<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">
            @administrator
            <hr>
            <li>
                <a href="{{ route('administrator.dashboard') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/businessman.png') }}">
                    <span class="nav-text">{{ __('menu.administrator') }}</span>
                </a>
            </li>
            <hr>
            @endadministrator
            <li>
                <a href="{{ route('panel.dashboard') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/dashboard.png') }}">
                    <span class="nav-text">{{ __('menu.dashboard') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.invest') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/invest.png') }}">
                    <span class="nav-text">{{ __('menu.invest') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.reinvest') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/displacement.png') }}">
                    <span class="nav-text">{{ __('menu.reinvest') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.withdraw') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/fund.png') }}">
                    <span class="nav-text">{{ __('menu.withdraw') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.investments') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/profit.png') }}">
                    <span class="nav-text">{{ __('menu.deposits') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.withdrawals') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/money-loss.png') }}">
                    <span class="nav-text">{{ __('menu.withdrawals') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.profits') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/investment.png') }}">
                    <span class="nav-text">{{ __('menu.profits') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.referrals') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/influence.png') }}">
                    <span class="nav-text">{{ __('menu.referrals') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.banners') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/gallery.png') }}">
                    <span class="nav-text">{{ __('menu.banners') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.achievements') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/trophy.png') }}">
                    <span class="nav-text">{{ __('menu.achievements') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.bounty') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/diamond.png') }}">
                    <span class="nav-text">{{ __('menu.bounty') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.account') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/shirt.png') }}">
                    <span class="nav-text">{{ __('menu.account') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.wallets') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/wallet.png') }}">
                    <span class="nav-text">{{ __('menu.wallets') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.security') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/shield.png') }}">
                    <span class="nav-text">{{ __('menu.security') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('panel.help', ['category' => 'general']) }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/life-ring.png') }}">
                    <span class="nav-text">{{ __('menu.help') }}</span>
                </a>
            </li>
            <li>
                <a href="{{ route('auth.logout') }}" class="ai-icon" aria-expanded="false">
                    <img src="{{ asset('images/menu/logout.png') }}">
                    <span class="nav-text">{{ __('menu.logout') }}</span>
                </a>
            </li>
        </ul>
        <div class="plus-box">
            <p class="fs-16 font-w500 mb-4">{{ __('menu.cta') }}</p>
        </div>
    </div>
</div>
