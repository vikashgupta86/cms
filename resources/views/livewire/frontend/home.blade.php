@extends('frontend.layouts.app')

@section('title', "Home")

@php
    use Illuminate\Support\Str;

    // Pre-resolve all cross-referenced sections once — avoids repeated collect() scans in the loop.
    $sectionMap     = collect($sectionsData)->keyBy('section_type');

    $sidebarSection = $sectionMap->get('sidebar_links');
    $eventsSection  = $sectionMap->get('events');
    $photoSection   = $sectionMap->get('photo_gallery');
    $contact_us = $sectionMap->get('contact_us');
    $map_section = $sectionMap->get('map');
    $socialSection  = $sectionMap->get('social');

    $sidebarItems   = $sidebarSection ? $sidebarSection['data'] : collect();
     $socialItems    = $socialSection  ? $socialSection['data']  : collect();
 
    $hasNewsSection = $sectionMap->has('news');
@endphp
 
@section('content')

<section class="home-page">

    @forelse ($sectionsData as $section)
        @php $type = $section['section_type']; $data = $section['data']; @endphp

        {{-- ── 1. ANNOUNCEMENT TICKER ── --}}
        @if ($type === 'announcement')
            @include('frontend.partial.announcement', ['items' => $data])

        {{-- ── 2. BANNER ── --}}
        @elseif ($type === 'banner')
            @include('frontend.partial.banner', ['items' => $data])

        {{-- ── 3. PORTAL / SERVICE ICONS ── --}}
        @elseif ($type === 'portal')
            @include('frontend.partial.portal', ['items' => $data])

        {{-- ── 4. GOV ICONS MARQUEE ── --}}
        @elseif ($type === 'gov_icons')
            @include('frontend.partial.gov_icons', ['items' => $data])

        {{-- ── 5. MINISTER / PM QUOTE ── --}}
        @elseif ($type === 'minister')
            @include('frontend.partial.pmquote', ['items' => $data])

        {{-- ── 6. ABOUT ── --}}
        @elseif ($type === 'about')
            @include('frontend.partial.about', ['items' => $data])

        {{-- ── 7. MINISTERS LIST ── --}}
        @elseif ($type === 'ministers')
            @include('frontend.partial.ministers', ['items' => $data])

        {{-- ── 8. NEWS + SIDEBAR (rendered together) ── --}}
        @elseif ($type === 'news')
            @include('frontend.partial.news', [
                'items'        => $data,
                'sidebarItems' => $sidebarItems,
                'sidebarTitle' => 'Portals',
            ])

        {{-- ── 9. SIDEBAR — standalone fallback (only when no news section) ── --}}
        @elseif ($type === 'sidebar_links' && !$hasNewsSection)
            <div class="container py-5">
                <div class="row justify-content-end">
                    <div class="col-md-4">
                        @include('frontend.partial.sidebar_links', [
                            'items' => $data,
                            'title' => 'Portals',
                        ])
                    </div>
                </div>
            </div>

        {{-- ── 10. DASHBOARD + EVENTS (rendered together) ── --}}
        @elseif ($type === 'dashboard')
            <div class="container py-5">
                <div class="row">
                    <div class="{{ $eventsSection ? 'col-md-6' : 'col-12' }}">
                        @include('frontend.partial.dashboard', ['items' => $data])
                    </div>

                    @if ($eventsSection)
                        <div class="col-md-6">
                            @include('frontend.partial.events', ['items' => $eventsSection['data']])
                        </div>
                    @endif
                </div>
            </div>

        {{-- ── 11. EVENTS — skip; rendered alongside dashboard above ── --}}
        @elseif ($type === 'events')
            {{-- intentionally blank --}}

        {{-- ── 12. VIDEO GALLERY + PHOTO GALLERY (rendered together) ── --}}
        @elseif ($type === 'video_gallery')
            <div class="container-fluid py-5" style="background:#1a3a6b;">
                <div class="container">
                    <div class="row">
                        <div class="{{ $photoSection ? 'col-md-6' : 'col-12' }}">
                            @include('frontend.partial.video_gallery', ['items' => $data])
                        </div>

                        @if ($photoSection)
                            <div class="col-md-6">
                                @include('frontend.partial.photo_gallery', ['items' => $photoSection['data']])
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        {{-- ── 13. PHOTO GALLERY — skip; rendered alongside video gallery above ── --}}
        @elseif ($type === 'photo_gallery')
            {{-- intentionally blank --}}
        @elseif ($type === 'contact_us')
            {{-- @include('frontend.partial.contact_map', ['items' => $data]) --}}
        @elseif ($type === 'map')
            {{-- @include('frontend.partial.contact_map', ['items' => $data]) --}}

        {{-- ── 14. SOCIAL — currently disabled ── --}}
        @elseif ($type === 'social')
            {{-- @include('frontend.partial.social', ['items' => $data]) --}}

        {{-- ── 15. CONTACT / MAP — currently disabled ── --}}
        @elseif ($type === 'contact_us')
            {{-- @include('frontend.partial.contact_map', ['items' => $data]) --}}

        {{-- ── 16. FOOTER LINKS ── --}}
        @elseif ($type === 'footer_links')
            @include('frontend.partial.footer_links', [
                'items'        => $data,
                 'map_section'  => $map_section,
                'contact_us'   => $contact_us,
                'socialItems'=>$socialItems,
            ])

        @endif

    @empty
        <div class="container py-4">
            <div class="alert alert-warning">No homepage section found.</div>
        </div>
    @endforelse

</section>

@endsection