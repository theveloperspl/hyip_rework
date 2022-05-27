<div class="nav-header">
    <a href="{{ config('app.url') }}" class="brand-logo">
        <img class="logo-abbr" src="{{ asset('images/logo.png') }}" alt="">
        <img class="brand-title" src="{{ asset('images/logo-text-white.png') }}" alt="">
    </a>
    <div class="nav-control">
        <div class="hamburger">
            <span class="line"></span><span class="line"></span><span class="line"></span>
        </div>
    </div>
</div>
<div class="header">
    <div class="header-content">
        <nav class="navbar navbar-expand">
            <div class="collapse navbar-collapse justify-content-between">
                <div class="header-left"></div>
                <ul class="navbar-nav header-right">
                    @if(config('app.env') === 'local')
                            <li class="nav-item dropdown header-profile">
                                <button type="button" class="btn btn-primary btn-lg d-block d-sm-none">XS</button>
                                <button type="button" class="btn btn-secondary btn-lg d-none d-sm-block d-md-none">SM</button>
                                <button type="button" class="btn btn-success btn-lg d-none d-md-block d-lg-none">MD</button>
                                <button type="button" class="btn btn-danger btn-lg d-none d-lg-block d-xl-none">LG</button>
                                <button type="button" class="btn btn-warning btn-lg d-none d-xl-block d-xxl-none">XL</button>
                                <button type="button" class="btn btn-info btn-lg d-none d-xxl-block">XXL</button>
                            </li>
                    @endif
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link" href="#" role="button" data-bs-toggle="dropdown">
                            <div class="header-info me-3">
                                <span class="fs-16 font-w600 ">{{ auth()->user()->username }}</span>
                                <small
                                    class="text-end fs-14 font-w400">{{ auth()->user()->leader ? __('statuses.leader') : __('statuses.standard') }}</small>
                            </div>
                            <img src="{{ asset('images/profile.png') }}" width="20" alt=""/>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end" data-bs-popper="none">
                            <a href="./app-profile.html" class="dropdown-item ai-icon">
                                <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18"
                                     height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                <span class="ms-2">Profile </span>
                            </a>
                            <a href="./email-inbox.html" class="dropdown-item ai-icon">
                                <svg id="icon-inbox" xmlns="http://www.w3.org/2000/svg" class="text-success" width="18"
                                     height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                    <polyline points="22,6 12,13 2,6"></polyline>
                                </svg>
                                <span class="ms-2">Inbox </span>
                            </a>
                            <a href="./login.html" class="dropdown-item ai-icon">
                                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18"
                                     height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                     stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                    <polyline points="16 17 21 12 16 7"></polyline>
                                    <line x1="21" y1="12" x2="9" y2="12"></line>
                                </svg>
                                <span class="ms-2">Logout </span>
                            </a>
                        </div>
                    </li>
                    @if(count(config('app.supported_locales')) > 1)
                    <li class="nav-item dropdown header-profile">
                        <a class="nav-link language-switcher" href="#language-switcher">
                            <img src="{{ asset('images/language/' . Localer::current() .'.png') }}" width="10" alt=""/>
                        </a>
                    </li>
                    @endif
                </ul>
            </div>
        </nav>
    </div>
</div>
