<nav class="bg-white shadow-md dark:bg-gray-900"
     role="navigation"
     aria-label="Main navigation"
     style="
        width:100%;
        position:sticky;
        top:0;
        z-index:9999;
        box-shadow:0 4px 12px rgba(0,0,0,0.08);
     ">

   

<!-- Skip to main content link for accessibility -->
    <a href="#main-content"
        class="sr-only z-50 rounded-md bg-blue-600 px-4 py-2 text-white focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:ring-2 focus:ring-blue-500 focus:outline-none">
        Skip to main content
    </a>


    {{-- ═══════════════════════════════════════════════════════
    ROW 1 — Top Header Bar
    Left : BEE main logo
    Right : 4 partner logos (all on ONE line, no wrapping)
    ═══════════════════════════════════════════════════════ --}}
    <div style="width:100%; border-bottom:1px solid #e5e7eb; background:#fff; box-sizing:border-box;">
        <div style="
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: nowrap;
            padding: 8px 40px;
            width: 100%;
            box-sizing: border-box;
        ">
            {{-- LEFT: BEE Main Logo --}}
            <a href="/" wire:navigate title="{{ app_name() }}" aria-label="Go to homepage"
                style="display:inline-flex; align-items:center; flex-shrink:0; text-decoration:none;">
                <img src="{{ asset('frontend/img/new_logo_main.png') }}" alt="{{ app_name() }}"
                    style="max-height:90px; width:auto; max-width:260px;">
            </a>

            {{-- RIGHT: 4 Partner Logos — forced single row --}}
            <div style="
                display: flex;
                align-items: center;
                justify-content: flex-end;
                flex-wrap: nowrap;
                gap: 20px;
                flex-shrink: 0;
            ">
                <a href="https://amritkaal.nic.in/" target="_blank" rel="noopener noreferrer"
                    aria-label="BEE at 25 (opens in new tab)"
                    style="display:inline-flex; align-items:center; flex-shrink:0;">
                    <img src="{{ asset('frontend/img/Bee_25.jpeg') }}" alt="BEE@25"
                        style="height:65px; width:auto; object-fit:contain; display:block;">
                </a>

                <a href="https://amritkaal.nic.in/" target="_blank" rel="noopener noreferrer"
                    aria-label="Azadi ka Amrit Mahotsav (opens in new tab)"
                    style="display:inline-flex; align-items:center; flex-shrink:0;">
                    <img src="{{ asset('frontend/img/azadi.jpg') }}" alt="Azadi ka Amrit Mahotsav"
                        style="height:65px; width:auto; object-fit:contain; display:block;">
                </a>

                <a href="https://swachhbharatmission.ddws.gov.in/" target="_blank" rel="noopener noreferrer"
                    aria-label="Swachh Bharat Mission (opens in new tab)"
                    style="display:inline-flex; align-items:center; flex-shrink:0;">
                    <img src="{{ asset('frontend/img/226.jpg') }}" alt="Swachh Bharat Mission"
                        style="height:65px; width:auto; object-fit:contain; display:block;">
                </a>

                <a href="https://india.gov.in" target="_blank" rel="noopener noreferrer"
                    aria-label="Ministry of Power India (opens in new tab)"
                    style="display:inline-flex; align-items:center; flex-shrink:0;">
                    <img src="{{ asset('frontend/img/Ministry_of_Power_India.svg') }}" alt="Ministry of Power"
                        style="height:80px; width:auto; object-fit:contain; display:block;">
                </a>
            </div>
        </div>
    </div>
    {{-- END ROW 1 --}}

    {{-- ═══════════════════════════════════════════════════════
    ROW 2 — Main Navbar
    Left : Dynamic menu links
    Right : Theme / Language / Auth / Hamburger
    All on ONE line with space-between
    ═══════════════════════════════════════════════════════ --}}
    <div style="width:100%; border-top:2px solid #e5e7eb; background:inherit; box-sizing:border-box;">
        <div style="display:flex; align-items:center; justify-content:space-between; flex-wrap:nowrap;
                    padding:0 40px; width:100%; box-sizing:border-box; min-height:52px;">

            {{-- LEFT: Dynamic navigation menu --}}
            <div id="navbar-language" style="flex:1; min-width:0; display:flex; align-items:stretch; overflow:visible;">
                <x-frontend.dynamic-menu location="frontend-header" />
            </div>

            {{-- RIGHT: Utility controls (theme / language / auth / hamburger) --}}
            {{--

            <div style="display:flex; align-items:center; gap:4px; flex-shrink:0; margin-left:8px; padding:6px 0;">

                @if (setting('show_theme_dropdown'))
                <button
                    class="rounded-lg p-2 text-sm text-gray-500 hover:bg-gray-100 focus:outline-hidden dark:text-white dark:hover:bg-gray-700 dark:hover:text-white"
                    id="theme-toggle" type="button" aria-label="Toggle between light and dark theme"
                    aria-pressed="false">

                    <svg class="hidden h-5 w-5" id="theme-toggle-dark-icon" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>

                    <svg class="hidden h-5 w-5" id="theme-toggle-light-icon" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">

                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0z"
                            fill-rule="evenodd" clip-rule="evenodd">
                        </path>

                    </svg>

                </button>
                @endif

                @if (setting('show_language_dropdown'))

                <button
                    class="inline-flex cursor-pointer items-center justify-center rounded-sm p-2 text-sm font-medium text-gray-900 hover:bg-gray-100 sm:px-3 sm:py-2 dark:text-white dark:hover:bg-gray-700 dark:hover:text-white"
                    data-dropdown-toggle="language-dropdown-menu" type="button">

                    <svg class="icon icon-tabler icons-tabler-outline icon-tabler-language"
                        xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor">

                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />

                    </svg>

                    <span class="ms-2 hidden sm:block">
                        {{ strtoupper(app()->currentLocale()) }}
                    </span>

                </button>

                @endif

                @guest

                @if (user_registration())

                <a href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>

                @endif

                <a href="{{ route('login') }}">
                    {{ __('Login') }}
                </a>

                @endguest

                @auth

                <button data-dropdown-toggle="user-dropdown-menu" type="button">

                    <img src="{{ asset(Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}">

                    <span class="ms-2 hidden sm:block">
                        {{ Auth::user()->last_name }}
                    </span>

                </button>

                @endauth

                <button data-collapse-toggle="navbar-language" type="button" aria-controls="navbar-language"
                    aria-expanded="false">

                    <span class="sr-only">Open main menu</span>

                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 17 14">

                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />

                    </svg>

                </button>

            </div>

            --}}
            {{-- end right controls --}}
        </div>
    </div>
    {{-- END ROW 2 --}}

</nav>