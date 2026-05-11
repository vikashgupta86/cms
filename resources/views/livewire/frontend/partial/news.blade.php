@if(isset($items) && $items->count())

@php
    $tabs = $items->whereNull('parent_id')->values();
    if ($tabs->isEmpty()) {
        $tabs = $items->filter(fn($i) => $i->children && $i->children->count())->values();
    }
@endphp

<div class="container py-5">
    <div class="row g-4 align-items-start">

        {{-- ── LEFT: News & Events ── --}}
        <div class="col-md-8">

            <h5 class="news-section-heading mb-2">
                <span class="heading-sq"></span> News &amp; Events
            </h5>

            {{-- TABS: data-bs-toggle="tab" NOT "pill" --}}
            <ul class="nav news-tabs mb-0" id="newsTabs-{{ rand(100,999) }}" role="tablist">
                @foreach($tabs as $tab)
                    <li class="nav-item flex-fill" role="presentation">
                        <button
                            class="nav-link w-100 {{ $loop->first ? 'active' : '' }}"
                            id="news-tab-{{ $tab->id }}"
                            data-bs-toggle="tab"
                            data-bs-target="#news-pane-{{ $tab->id }}"
                            type="button"
                            role="tab"
                            aria-controls="news-pane-{{ $tab->id }}"
                            aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            {{ $tab->name }}
                        </button>
                    </li>
                @endforeach
            </ul>

            {{-- TAB CONTENT --}}
            <div class="tab-content news-tab-content">
                @foreach($tabs as $tab)
                    <div
                        class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                        id="news-pane-{{ $tab->id }}"
                        role="tabpanel"
                        aria-labelledby="news-tab-{{ $tab->id }}">

                        <div class="tabpanel_links">
                            @forelse($tab->children as $child)
                                @php
                                    if ($child->type === 'external' && !empty($child->url)) {
                                        $href = $child->url;
                                    } elseif ($child->type === 'file' && !empty($child->file)) {
                                        $href = asset('storage/' . $child->file);
                                    } elseif (!empty($child->slug)) {
                                        $href = route('show.content', $child->slug);
                                    } else {
                                        $href = '#';
                                    }
                                    $isExternal = $child->opens_new_tab
                                        || in_array($child->type, ['external', 'file']);

                                    $fileSize = '';
                                    if ($child->type === 'file' && !empty($child->file)) {
                                        try {
                                            $bytes = \Illuminate\Support\Facades\Storage::disk('public')->size($child->file);
                                            $fileSize = $bytes >= 1048576
                                                ? number_format($bytes / 1048576, 2) . ' MB'
                                                : number_format($bytes / 1024, 0) . ' KB';
                                        } catch (\Exception $e) {}
                                    }
                                @endphp

                                <a href="{{ $href }}"
                                   @if($isExternal) target="_blank" rel="noopener" @endif>
                                    <span class="news-item-name">
                                        {{ $child->name }}
                                        @if($child->type === 'file')
                                            <i class="fa fa-file-pdf-o text-danger ms-1"></i>
                                            @if($fileSize)
                                                <small class="text-muted ms-1">({{ $fileSize }})</small>
                                            @endif
                                        @elseif($child->type === 'external')
                                            <i class="fa fa-external-link ms-1"></i>
                                        @endif
                                    </span>
                                    <span class="news-arrow">&#10132;</span>
                                </a>

                            @empty
                                <div class="p-3 text-muted" style="font-size:14px;">
                                    No records found.
                                </div>
                            @endforelse
                        </div>

                    </div>
                @endforeach
            </div>

            <div class="text-center mt-3">
                <a href="{{ url('news') }}" class="btn btn-view-more">
                    View more &#10132;
                </a>
            </div>

        </div>

        {{-- ── RIGHT: Portals ── --}}
        @if(isset($sidebarItems) && $sidebarItems->count())
        <div class="col-md-4">

            <h5 class="news-section-heading mb-2">
                <span class="heading-sq"></span> {{ $sidebarTitle ?? 'Portals' }}
            </h5>

            <div class="watsnew_linkspanel">
                @foreach($sidebarItems as $item)
                    @php
                        $href = match($item->type) {
                            'external' => $item->url,
                            'file'     => asset('storage/' . ($item->file ?? '')),
                            default    => route('show.content', $item->slug),
                        };
                        $isExternal = in_array($item->type, ['external', 'file'])
                                      || $item->opens_new_tab;
                    @endphp
                    <a href="{{ $href }}"
                       @if($isExternal) target="_blank" rel="noopener" @endif>
                        <span class="link-name">{{ $item->name }}</span>
                        <span class="link-arrow">&#10132;</span>
                    </a>
                @endforeach
            </div>

            <div class="text-center mt-3">
                <a href="{{ url('portals') }}" class="btn btn-view-more">
                    View more &#10132;
                </a>
            </div>

        </div>
        @endif

    </div>
</div>

<style>
.news-section-heading {
    font-size: 20px; font-weight: 700; color: #1a3a6b;
    display: flex; align-items: center; gap: 8px;
}
.heading-sq {
    display: inline-block; width: 14px; height: 14px;
    border: 2px solid #1a3a6b; flex-shrink: 0;
}

/* Tabs */
.news-tabs { display: flex; border: none; }
.news-tabs .nav-item { flex: 1; }
.news-tabs .nav-link {
    border-radius: 0;
    border: 1px solid #28599a;
    border-right: none;
    color: #333; background: #fff;
    font-size: 14px; font-weight: 500;
    padding: 10px 8px; text-align: center;
    white-space: nowrap; overflow: hidden;
    text-overflow: ellipsis;
    cursor: pointer;
    /* IMPORTANT: pointer-events must be auto */
    pointer-events: auto;
}
.news-tabs .nav-item:last-child .nav-link {
    border-right: 1px solid #28599a;
}
.news-tabs .nav-link.active {
    background: #28599a; color: #fff;
    border-color: #28599a;
}
.news-tabs .nav-link:hover:not(.active) {
    background: #eef3fb; color: #28599a;
}

/* Tab content */
.news-tab-content {
    border: 1px solid #28599a;
    border-top: none;
    background: #fff;
    max-height: 260px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #28599a #f0f4f8;
}
.news-tab-content::-webkit-scrollbar { width: 6px; }
.news-tab-content::-webkit-scrollbar-thumb { background: #28599a; border-radius: 3px; }
.news-tab-content::-webkit-scrollbar-track { background: #f0f4f8; }

/* News rows */
.tabpanel_links a {
    display: flex; justify-content: space-between;
    align-items: center; padding: 10px 14px;
    border-bottom: 1px solid rgba(40,89,154,0.18);
    color: #111; text-decoration: none;
    font-size: 14px; line-height: 1.4;
    transition: background 0.12s;
}
.tabpanel_links a:last-child { border-bottom: none; }
.tabpanel_links a:hover { background: #eef3fb; color: #1a3a6b; }
.news-item-name { flex: 1; padding-right: 10px; }
.news-arrow { flex-shrink: 0; color: #28599a; font-size: 16px; }

/* Blue portals panel */
.watsnew_linkspanel {
    background: #28599a;
    max-height: 260px; overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #1a3a6b #3a6db5;
}
.watsnew_linkspanel::-webkit-scrollbar { width: 5px; }
.watsnew_linkspanel::-webkit-scrollbar-thumb { background: #1a3a6b; }
.watsnew_linkspanel::-webkit-scrollbar-track { background: #3a6db5; }
.watsnew_linkspanel a {
    display: flex; justify-content: space-between;
    align-items: center; padding: 12px 16px;
    color: #fff; text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.22);
    font-size: 15px; font-weight: 500;
    transition: background 0.12s;
}
.watsnew_linkspanel a:last-child { border-bottom: none; }
.watsnew_linkspanel a:hover { background: #1d4478; }
.link-name { flex: 1; padding-right: 10px; line-height: 1.3; }
.link-arrow { flex-shrink: 0; font-size: 18px; color: rgba(255,255,255,0.85); }

/* View more button */
.btn-view-more {
    background: #28599a; color: #fff !important;
    border-radius: 30px; padding: 9px 30px;
    font-size: 14px; font-weight: 500;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    border: none;
}
.btn-view-more:hover { background: #1a3a6b; color: #fff !important; }
</style>

@endif