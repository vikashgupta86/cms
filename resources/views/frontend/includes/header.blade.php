{{-- ═══════════════════════════════════════════════════════════
     AYUSH DEPARTMENT HEADER — Matches ayush.ap.gov.in exactly
     1. Top utility bar  (pale green bg, accessibility / social / lang)
     2. Green branding banner  (AP logo | title | right logos)
     3. Green navigation bar  (dynamic menu links)
═══════════════════════════════════════════════════════════ --}}

{{-- ── STYLES ──────────────────────────────────────────────── --}}
<style>
/* ─── TOP UTILITY BAR ─────────────────────────────────────── */
.ayush-topbar {
    background: #e8f5e9;
    border-bottom: 1px solid #c8e6c9;
    font-size: 12px;
    padding: 3px 0;
}
.ayush-topbar .topbar-inner {
    display: flex;
    align-items: center;
    justify-content: space-between;
    max-width: 100%;
    padding: 0 16px;
}
.ayush-topbar .skip-link {
    color: #005800;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}
.ayush-topbar .skip-link:hover { text-decoration: underline; }
.ayush-topbar .tb-right {
    display: flex;
    align-items: center;
    gap: 2px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.ayush-topbar .tb-right li a,
.ayush-topbar .tb-right li span {
    display: inline-flex;
    align-items: center;
    padding: 3px 7px;
    color: #333;
    text-decoration: none;
    font-size: 12px;
    border-radius: 3px;
    cursor: pointer;
}
.ayush-topbar .tb-right li a:hover { background: #c8e6c9; color: #005800; }
.ayush-topbar .tb-sep { color: #bbb; padding: 0 2px; font-size: 10px; }
.ayush-topbar .tb-right li img { width: 20px; height: 20px; object-fit: contain; }

/* Font-size dropdown (hover) */
.tb-dropdown { position: relative; }
.tb-dropdown .tb-panel {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0,0,0,.12);
    min-width: 110px;
    z-index: 99999;
    padding: 4px 0;
    list-style: none;
    margin: 0;
}
.tb-dropdown:hover .tb-panel { display: block; }
.tb-dropdown .tb-panel li a {
    padding: 5px 14px;
    display: block;
    white-space: nowrap;
    color: #333;
    font-size: 13px;
}
.tb-dropdown .tb-panel li a:hover { background: #f0f0f0; }

/* ─── GREEN BRANDING BANNER ───────────────────────────────── */
.ayush-banner {
    background: #006600;
    padding: 8px 0;
}
.ayush-banner .banner-inner {
    display: flex;
    align-items: center;
    max-width: 100%;
    padding: 0 16px;
    gap: 12px;
}
.ayush-banner .logo-left img {
    height: 80px;
    width: auto;
    object-fit: contain;
}
.ayush-banner .title-box {
    flex: 1;
    text-align: center;
}
.ayush-banner .title-box h4 {
    color: #F8933B;
    font-size: 1.45rem;
    font-weight: 700;
    margin: 0 0 2px;
    line-height: 1.2;
}
.ayush-banner .title-box h2 {
    color: #fff;
    font-size: 1.15rem;
    font-weight: 600;
    margin: 0 0 4px;
    line-height: 1.3;
}
.ayush-banner .title-box p {
    color: #fff;
    font-size: 0.875rem;
    margin: 0;
}
.ayush-banner .logos-right {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
}
.ayush-banner .logos-right img {
    height: 78px;
    width: auto;
    object-fit: contain;
    border-radius: 4px;
    background: #fff;
    padding: 2px;
}

/* ─── GREEN NAV BAR ───────────────────────────────────────── */
.ayush-navbar {
    background: #006600;
    border-top: 2px solid #004d00;
    position: sticky;
    top: 0;
    z-index: 9999;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}
.ayush-navbar .navbar-inner {
    max-width: 100%;
    padding: 0 16px;
    display: flex;
    align-items: stretch;
    flex-wrap: nowrap;
    overflow: visible;
}

/* Override the generic menu-item styles for the green nav */
.ayush-navbar .bee-nav-link {
    color: #fff !important;
    font-size: 0.82rem !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.02em !important;
    padding: 10px 10px !important;
    border-bottom: 2px solid transparent !important;
    white-space: nowrap;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ayush-navbar .bee-nav-link:hover,
.ayush-navbar .bee-nav-link.active {
    color: #ffd54f !important;
    border-bottom-color: #ffd54f !important;
    background: rgba(255,255,255,0.1) !important;
    text-decoration: none;
}

/* Dropdown panel inherits white bg */
.ayush-navbar .bee-dropdown-panel {
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 0 0 6px 6px;
    margin-top: 0;
    box-shadow: 0 6px 18px rgba(0,0,0,.2);
}
.ayush-navbar .bee-dropdown-item {
    color: #222 !important;
    font-size: 0.82rem !important;
    text-transform: none !important;
    letter-spacing: 0 !important;
    padding: 8px 16px !important;
    font-weight: 500 !important;
}
.ayush-navbar .bee-dropdown-item:hover {
    background: #e8f5e9 !important;
    color: #005800 !important;
}

.ayush-navbar #bee-main-nav-ul {
    flex-wrap: nowrap !important;
    overflow: visible !important;
}

/* Override generic nav-item link inside the green bar */
.ayush-navbar .bee-nav-link svg path { stroke: #fff; }
.ayush-navbar .bee-nav-link:hover svg path,
.ayush-navbar .bee-nav-link.active svg path { stroke: #ffd54f; }
</style>

{{-- ═══════════════════════════════════════════════════════
     1. TOP UTILITY BAR
═══════════════════════════════════════════════════════ --}}
<div class="ayush-topbar" role="banner">
    <div class="topbar-inner">
        {{-- Left: Skip link --}}
        <a class="skip-link" href="#main-content">Skip to main content</a>

        {{-- Right: utilities --}}
        <ul class="tb-right">

            {{-- Colour scheme --}}
            <li class="tb-dropdown">
                <a title="Colour Scheme" href="javascript:void(0);">
                    <img src="{{ asset('frontend/img/color_scheems.png') }}" alt="Colour scheme">
                </a>
                <ul class="tb-panel">
                    <li><a href="javascript:void(0);" title="Default Scheme">Default</a></li>
                    <li><a href="javascript:void(0);" title="High Contrast">High Contrast</a></li>
                </ul>
            </li>

            <li><span class="tb-sep">|</span></li>

            {{-- Accessibility / font resize --}}
            <li class="tb-dropdown">
                <a title="Accessibility Options" href="javascript:void(0);">A</a>
                <ul class="tb-panel">
                    <li><a href="javascript:void(0);" onclick="document.body.style.fontSize='smaller';" title="Decrease font">A<sup>-</sup></a></li>
                    <li><a href="javascript:void(0);" onclick="document.body.style.fontSize='';" title="Reset font">A</a></li>
                    <li><a href="javascript:void(0);" onclick="document.body.style.fontSize='larger';" title="Increase font">A<sup>+</sup></a></li>
                </ul>
            </li>

            <li><span class="tb-sep">|</span></li>

            {{-- Social links --}}
            <li class="tb-dropdown">
                <a title="Social Media" href="javascript:void(0);">
                    <img src="{{ asset('frontend/img/ico-social.png') }}" alt="Social" style="width:18px;height:18px;">
                </a>
                <ul class="tb-panel">
                    <li><a href="#" target="_blank"><img src="{{ asset('frontend/img/ico-facebook.png') }}" alt="Facebook" style="width:16px;height:16px;"> Facebook</a></li>
                    <li><a href="#" target="_blank"><img src="{{ asset('frontend/img/ico-twitter.png') }}" alt="Twitter" style="width:16px;height:16px;"> Twitter</a></li>
                    <li><a href="#" target="_blank"><img src="{{ asset('frontend/img/ico-youtube.png') }}" alt="YouTube" style="width:16px;height:16px;"> YouTube</a></li>
                </ul>
            </li>

            <li><span class="tb-sep">|</span></li>

            {{-- Search --}}
            <li>
                <a title="Site Search" href="javascript:void(0);">
                    <img src="{{ asset('frontend/img/ico-site-search.png') }}" alt="Search" style="width:18px;height:18px;">
                </a>
            </li>

            <li><span class="tb-sep">|</span></li>

            {{-- Sitemap --}}
            <li>
                <a title="Sitemap" href="/sitemap">
                    <img src="{{ asset('frontend/img/ico-sitemap.png') }}" alt="Sitemap" style="width:18px;height:18px;">
                </a>
            </li>

            <li><span class="tb-sep">|</span></li>

            {{-- Language --}}
            <li><a href="#" title="English">ENG</a></li>
            <li><span class="tb-sep">|</span></li>
            <li><a href="#" title="Telugu">TEL</a></li>

        </ul>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     2. GREEN BRANDING BANNER
═══════════════════════════════════════════════════════ --}}
<div class="ayush-banner">
    <div class="banner-inner">

        {{-- Left logo --}}
        <div class="logo-left">
            <img src="{{ asset('frontend/images/ap-logo.png') }}" alt="Government of Andhra Pradesh Logo">
        </div>

        {{-- Centre title --}}
        <div class="title-box">
            <h4>AYUSH Department</h4>
            <h2>(Ayurveda, Yoga and Naturopathy, Unani, Siddha and Homoeopathy)</h2>
            <p>Ministry Of Health And Family Welfare, Government Of Andhra Pradesh</p>
        </div>

        {{-- Right logos --}}
        <div class="logos-right">
            <img src="{{ asset('frontend/images/yoga_day.jpg') }}" alt="Yoga Day">
            <img src="{{ asset('frontend/images/ayush.png') }}" alt="AYUSH Logo">
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════
     3. GREEN NAVIGATION BAR  (dynamic menu)
═══════════════════════════════════════════════════════ --}}
<nav class="ayush-navbar" role="navigation" aria-label="Main navigation" id="main-navbar">
    <div class="navbar-inner">
        <x-frontend.dynamic-menu
            location="frontend-header"
            cssClass="flex flex-row items-stretch list-none m-0 p-0 flex-nowrap overflow-visible"
        />
    </div>
</nav>