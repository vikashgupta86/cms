@if(isset($items) && $items->count())

<div class="col-md-4">
 
    {{-- Section heading --}}
    <h5 class="news-section-heading mb-2">
        <span class="heading-icon">&#9633;</span> {{ $title ?? 'Portals' }}
    </h5>

    {{-- Blue links panel --}}
    <div class="watsnew_linkspanel">
        @foreach($items as $item)
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

                <span class="link-name">
                    @if($item->file_icon)
                        <img src="{{ asset('storage/' . $item->file_icon) }}"
                             alt="{{ $item->name }}"
                             style="height:18px; margin-right:6px; vertical-align:middle; filter:brightness(0) invert(1);">
                    @elseif($item->icon)
                        <i class="{{ $item->icon }} me-2"></i>
                    @endif
                    {{ $item->name }}
                </span>

                <span class="link-arrow">&#10132;</span>
            </a>
        @endforeach
    </div>

    {{-- View more button --}}
    <div class="text-center mt-3">
        <a href="{{ url('portals') }}" class="btn btn-news-more">
            View more &#10132;
        </a>
    </div>

</div>

<style>
/* ── Heading (shared with news) ── */
.news-section-heading {
    font-size: 20px;
    font-weight: 700;
    color: #1a3a6b;
    display: flex;
    align-items: center;
    gap: 8px;
}
.heading-icon { font-size: 18px; color: #1a3a6b; }

/* ── Blue links panel ── */
.watsnew_linkspanel {
    background: #28599a;
    max-height: 240px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #1a3a6b #3a6db5;
}
.watsnew_linkspanel::-webkit-scrollbar { width: 5px; }
.watsnew_linkspanel::-webkit-scrollbar-thumb { background: #1a3a6b; }
.watsnew_linkspanel::-webkit-scrollbar-track { background: #3a6db5; }

.watsnew_linkspanel a {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    color: #fff;
    text-decoration: none;
    border-bottom: 1px solid rgba(255,255,255,0.25);
    font-size: 15px;
    font-weight: 500;
    transition: background 0.15s;
}
.watsnew_linkspanel a:last-child { border-bottom: none; }
.watsnew_linkspanel a:hover { background: #1d4478; }

.link-name {
    flex: 1;
    padding-right: 10px;
    line-height: 1.3;
}
.link-arrow {
    flex-shrink: 0;
    font-size: 18px;
    color: rgba(255,255,255,0.85);
}

/* ── View more button (shared with news) ── */
.btn-news-more {
    background: #28599a;
    color: #fff;
    border-radius: 30px;
    padding: 8px 28px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.btn-news-more:hover {
    background: #1a3a6b;
    color: #fff;
}
</style>

@endif