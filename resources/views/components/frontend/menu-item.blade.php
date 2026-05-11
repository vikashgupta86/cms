@props(['item', 'optimized' => false, 'nested' => false])

@php
    $url = $item->getFullUrl();
    $isActive = $item->isCurrentlyActive();
    $hasChildren = isset($item->children) && $item->children instanceof \Illuminate\Support\Collection && $item->children->isNotEmpty();
    $target = $item->opens_new_tab ? '_blank' : null;
    $htmlAttributes = $item->html_attributes ?? [];
    $baseCssClasses = '';
    $attributes = array_merge($htmlAttributes, ['class' => $baseCssClasses]);
    if ($target) {
        $attributes['target'] = $target;
    }
@endphp

@once
<style>
    /* ── Parent <ul> rendered by dynamic-menu: force horizontal flex row ── */
    #bee-main-nav-ul {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
        flex-wrap: nowrap !important;
        gap: 0 !important;
    }

    /* ── Remove Bootstrap's auto ::after caret everywhere we use custom SVG ── */
    .no-bs-caret::after {
        display: none !important;
        content: none !important;
    }

    /* ── TOP-LEVEL DROPDOWN: pure CSS hover, no JS/Alpine needed ── */
    .bee-nav-dropdown {
        position: relative;
        list-style: none;
    }
    .bee-nav-dropdown > .bee-dropdown-panel {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        margin-top: 2px;
        min-width: 200px;
        width: max-content;
        max-width: 420px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 9999;
        padding: 6px 0;
        list-style: none;
        border: 1px solid rgba(0,0,0,0.08);
        overflow: visible !important; /* CRITICAL: do NOT clip nested sub-menus */
    }
    .bee-nav-dropdown:hover > .bee-dropdown-panel {
        display: block !important;
    }

    /* ── NESTED SUBMENU: opens to the RIGHT on hover ── */
    .bee-submenu {
        position: relative;
        list-style: none;
    }
    .bee-submenu > .bee-submenu-panel {
        display: none;
        position: absolute;
        top: 0;
        left: 100%;
        margin-left: 4px;
        min-width: 200px;
        width: max-content;
        max-width: 420px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        z-index: 10000;
        padding: 6px 0;
        list-style: none;
        border: 1px solid rgba(0,0,0,0.08);
    }
    .bee-submenu:hover > .bee-submenu-panel {
        display: block !important;
    }

    /* ── Dropdown items base style ── */
    .bee-dropdown-item {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        padding: 8px 16px;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
        gap: 8px;
    }
    .bee-dropdown-item:hover {
        background-color: #f3f4f6;
        color: #111827;
        text-decoration: none;
    }
    .bee-dropdown-item.active {
        color: #1d4ed8;
    }

    /* ── Divider ── */
    .bee-divider {
        border-top: 1px solid #e5e7eb;
        margin: 4px 8px;
    }

    /* ── Heading inside dropdown ── */
    .bee-dropdown-heading {
        display: block;
        padding: 6px 16px 2px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.05em;
    }

    /* ── Scroll for top-level dropdown only ── */
    .bee-dropdown-panel {
        max-height: 80vh;
        overflow-y: auto;
        overflow-x: visible; /* must NOT clip sub-submenus */
    }

    /* ── Sub-menus must NEVER clip their children ── */
    .bee-submenu-panel {
        overflow: visible !important; /* allows sub-sub-menus to escape */
        max-height: none !important;
    }

    /* ── Each .bee-submenu inside a panel must be overflow:visible too ── */
    .bee-dropdown-panel .bee-submenu,
    .bee-submenu-panel .bee-submenu {
        overflow: visible !important;
    }

    /* ── Top-level nav link style ── */
    .bee-nav-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1f2937;
        text-decoration: none;
        border-bottom: 2px solid transparent;
        transition: border-color 0.15s, opacity 0.15s;
        white-space: nowrap;
    }
    .bee-nav-link:hover {
        border-bottom-color: #374151;
        opacity: 0.8;
        text-decoration: none;
        color: #1f2937;
    }
    .bee-nav-link.active {
        border-bottom-color: #1d4ed8;
        color: #1d4ed8;
    }
</style>
@endonce

@if($nested)
    {{-- ══════════════════════════════════════════════════
         NESTED ITEM  (inside a dropdown or sub-menu)
    ══════════════════════════════════════════════════ --}}

    @if($item->type === 'divider')
        <li role="separator">
            <div class="bee-divider"></div>
        </li>

    @elseif($item->type === 'heading')
        <li role="presentation">
            <span class="bee-dropdown-heading">{{ $item->getDisplayTitle() }}</span>
        </li>

    @elseif($hasChildren)
        {{-- Has children → sub-menu opens to the RIGHT on hover (pure CSS) --}}
        <li class="bee-submenu">
            <a href="{{ route('show.content', $item->slug) }}"
               class="bee-dropdown-item no-bs-caret"
               aria-haspopup="true"
               aria-expanded="false"
            >
                <span>{{ $item->getDisplayTitle() }}</span>
                {{-- Right-facing chevron ─ rotated 90° --}}
                <svg style="width:9px;height:9px;flex-shrink:0;transform:rotate(-90deg);opacity:0.6;"
                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round"
                          stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                </svg>
            </a>

            <ul class="bee-submenu-panel">
                @foreach($item->children as $childItem)
                    <x-frontend.menu-item :item="$childItem" :nested="true" />
                @endforeach
            </ul>
        </li>

    @elseif($item->type === 'file')
        <li>
            <a href="{{ asset($item->file) }}"
               target="{{ $item->opens_new_tab ? '_blank' : '_self' }}"
               class="bee-dropdown-item no-bs-caret {{ $item->isCurrentlyActive() ? 'active' : '' }}"
               @if($item->opens_new_tab) aria-label="{{ $item->getDisplayTitle() }} (opens in new tab)" @endif>
                <span>{{ $item->getDisplayTitle() }}</span>
                @if($item->opens_new_tab)
                    <svg style="width:12px;height:12px;opacity:0.5;flex-shrink:0;" fill="none"
                         viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span class="sr-only">(opens in new tab)</span>
                @endif
            </a>
        </li>

    @elseif($item->type === 'content')
        <li>
            <a href="{{ route('show.content', $item->slug) }}"
               class="bee-dropdown-item no-bs-caret {{ $item->isCurrentlyActive() ? 'active' : '' }}">
                <span>{{ $item->getDisplayTitle() }}</span>
            </a>
        </li>

    @else
        <li>
            <a href="{{ $item->getFullUrl() }}"
               @if($item->opens_new_tab) target="_blank" @else wire:navigate @endif
               class="bee-dropdown-item no-bs-caret {{ $item->isCurrentlyActive() ? 'active' : '' }}"
               @if($item->opens_new_tab) aria-label="{{ $item->getDisplayTitle() }} (opens in new tab)" @endif>
                <span>{{ $item->getDisplayTitle() }}</span>
                @if($item->opens_new_tab)<span class="sr-only">(opens in new tab)</span>@endif
            </a>
        </li>
    @endif

@else
    {{-- ══════════════════════════════════════════════════
         TOP-LEVEL NAVBAR ITEM  (opens on HOVER — pure CSS)
    ══════════════════════════════════════════════════ --}}

    @switch($item->type)

        @case('divider')
            <li style="border-left:1px solid #e5e7eb; height:24px; margin:0 4px; align-self:center;"></li>
            @break

        @case('heading')
            <li>
                <span class="bee-nav-link" style="font-size:0.75rem; color:#9ca3af; cursor:default;">
                    {{ $item->getDisplayTitle() }}
                </span>
            </li>
            @break

        @case('file')
        @case('dropdown')
            @if($hasChildren)
                {{-- Pure CSS hover — no Alpine, no JS, no Bootstrap conflicts --}}
                <li class="bee-nav-dropdown">
                    <a href="{{ route('show.content', $item->slug) }}"
                       class="bee-nav-link no-bs-caret {{ $isActive ? 'active' : '' }}"
                       aria-haspopup="true"
                       aria-label="{{ $item->getDisplayTitle() }}"
                    >
                        <span>{{ $item->getDisplayTitle() }}</span>
                        {{-- Down-pointing chevron --}}
                        <svg style="width:9px;height:9px;flex-shrink:0;opacity:0.6;"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round"
                                  stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                        </svg>
                    </a>

                    <ul class="bee-dropdown-panel"
                        role="menu"
                        aria-label="{{ $item->getDisplayTitle() }} submenu">
                        @foreach(($item->children ?? collect()) as $childItem)
                            @if($optimized || $childItem->userCanSee())
                                <x-frontend.menu-item :item="$childItem" :optimized="$optimized" :nested="true" />
                            @endif
                        @endforeach
                    </ul>
                </li>
            @else
                <li>
                    <a href="{{ $url }}"
                       @if($target) target="{{ $target }}" @else wire:navigate @endif
                       class="bee-nav-link {{ $isActive ? 'active' : '' }}">
                        {{ $item->getDisplayTitle() }}
                    </a>
                </li>
            @endif
            @break

        @case('external')
        @case('link')
        @default
            <x-frontend.nav-item :href="$url" :active="$isActive" :target="$target">
                {{ $item->getDisplayTitle() }}
            </x-frontend.nav-item>
            @break

    @endswitch

@endif