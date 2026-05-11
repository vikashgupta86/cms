@extends('frontend.layouts.app')

@section('title', app_name())

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

<section class="home-page">

    @forelse($sectionsData as $section)

        {{-- ─────────────────────────────────────────────
             1. ANNOUNCEMENT TICKER
        ───────────────────────────────────────────── --}}
        @if($section['section_type'] === 'announcement')

            @include('frontend.partial.announcement', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             2. PORTAL / SERVICE ICONS (carousel row)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'portal')

            @include('frontend.partial.portal', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             3. GOV ICONS MARQUEE
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'gov_icons')

            @include('frontend.partial.gov_icons', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             4. MINISTER / PM QUOTE
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'minister')

            @include('frontend.partial.pmquote', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             5. ABOUT SECTION
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'about')

            @include('frontend.partial.about', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             6. MINISTERS LIST
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'ministers')

            @include('frontend.partial.ministers', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             7. NEWS & EVENTS  +  SIDEBAR LINKS  (side by side)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'news')

            @php
                // Find sidebar_links section to show beside news
                $sidebarSection = collect($sectionsData)
                    ->firstWhere('section_type', 'sidebar_links');
            @endphp

            <div class="container py-5">
                <div class="row">

                    {{-- News col (left 8) --}}
                    @include('frontend.partial.news', [
                        'items' => $section['data']
                    ])

                    {{-- Portals / sidebar col (right 4) --}}
                    @if($sidebarSection)
                        <div class="col-md-4">
                            @include('frontend.partial.sidebar_links', [
                                'items' => $sidebarSection['data'],
                                'title' => 'Portals',
                            ])
                        </div>
                    @endif

                </div>
            </div>

        {{-- ─────────────────────────────────────────────
             8. SIDEBAR LINKS — skip standalone render
             (already rendered alongside news above)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'sidebar_links')

            {{-- Rendered inline with news section above — skip standalone --}}

        {{-- ─────────────────────────────────────────────
             9. DASHBOARD STATS  +  EVENTS CALENDAR (side by side)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'dashboard')

            @php
                $eventsSection = collect($sectionsData)
                    ->firstWhere('section_type', 'events');
            @endphp

            <div class="container py-5">
                <div class="row">

                    <div class="col-md-6">
                        @include('frontend.partial.dashboard', [
                            'items' => $section['data']
                        ])
                    </div>

                    @if($eventsSection)
                        <div class="col-md-6">
                            @include('frontend.partial.events', [
                                'items' => $eventsSection['data']
                            ])
                        </div>
                    @endif

                </div>
            </div>

        {{-- ─────────────────────────────────────────────
             10. EVENTS — skip standalone render
             (already rendered beside dashboard above)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'events')

            {{-- Rendered inline with dashboard section above — skip standalone --}}

        {{-- ─────────────────────────────────────────────
             11. VIDEO GALLERY  +  PHOTO GALLERY (side by side)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'video_gallery')

            @php
                $photoSection = collect($sectionsData)
                    ->firstWhere('section_type', 'photo_gallery');
            @endphp

            <div class="container-fluid py-5" style="background: #1a3a6b;">
                <div class="container">
                    <div class="row">

                        <div class="col-md-6">
                            @include('frontend.partial.video_gallery', [
                                'items' => $section['data']
                            ])
                        </div>

                        @if($photoSection)
                            <div class="col-md-6">
                                @include('frontend.partial.photo_gallery', [
                                    'items' => $photoSection['data']
                                ])
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        {{-- ─────────────────────────────────────────────
             12. PHOTO GALLERY — skip standalone render
             (already rendered beside video gallery above)
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'photo_gallery')

            {{-- Rendered inline with video_gallery section above — skip standalone --}}

        {{-- ─────────────────────────────────────────────
             13. SOCIAL SECTION
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'social')

            @include('frontend.partial.social', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             14. CONTACT / MAP
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'contact_map')

            @include('frontend.partial.contact_map', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             15. FOOTER LINKS
        ───────────────────────────────────────────── --}}
        @elseif($section['section_type'] === 'footer_links')

            @include('frontend.partial.footer_links', [
                'items' => $section['data']
            ])

        {{-- ─────────────────────────────────────────────
             FALLBACK — unknown section type
        ───────────────────────────────────────────── --}}
        @else

            <div class="container py-4">
                <div class="mb-4">
                    <h3 class="mb-3">
                        {{ ucfirst(str_replace('_', ' ', $section['section_type'])) }}
                    </h3>

                    <div class="row">
                        @forelse($section['data'] as $item)

                            <div class="col-md-4 mb-3">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-body">

                                        <h5 class="card-title">{{ $item->name }}</h5>

                                        @if($item->type === 'content')
                                            <p>{{ Str::limit(strip_tags($item->content), 150) }}</p>
                                            <a href="{{ route('show.content', $item->slug) }}"
                                               class="btn btn-sm btn-primary">
                                                Read More
                                            </a>
                                        @endif

                                        @if($item->type === 'file' && $item->file)
                                            <a href="{{ asset('storage/' . $item->file) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-danger">
                                                View File
                                            </a>
                                        @endif

                                        @if($item->type === 'external' && $item->url)
                                            <a href="{{ $item->url }}"
                                               target="_blank"
                                               class="btn btn-sm btn-info">
                                                Open Link
                                            </a>
                                        @endif

                                    </div>
                                </div>
                            </div>

                        @empty
                            <div class="col-12">
                                <p>No data found.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        @endif

    @empty

        <div class="container py-4">
            <div class="alert alert-warning">
                No homepage section found.
            </div>
        </div>

    @endforelse

</section>

@endsection
