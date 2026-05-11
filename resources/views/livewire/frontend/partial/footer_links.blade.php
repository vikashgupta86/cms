@if(isset($items) && $items->count())
<div class="container-fluid footer-links-bar py-3">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-center gap-3">

            @foreach($items as $item)
                @php
                    $href = match($item->type) {
                        'external' => $item->url,
                        'file'     => asset('storage/' . ($item->file ?? '')),
                        default    => route('show.content', $item->slug),
                    };
                    $isExternal = in_array($item->type, ['external','file']) || $item->opens_new_tab;
                @endphp

                <a href="{{ $href }}"
                   @if($isExternal) target="_blank" rel="noopener" @endif
                   class="footer-link-item">
                    {{ $item->name }}
                </a>

                @if(!$loop->last)
                    <span class="footer-sep">|</span>
                @endif

            @endforeach

        </div>
    </div>
</div>

<style>
.footer-links-bar { background: #1a3a6b; }
.footer-link-item {
    color: #cce0ff; text-decoration: none;
    font-size: 13px; white-space: nowrap;
}
.footer-link-item:hover { color: #fff; text-decoration: underline; }
.footer-sep { color: rgba(255,255,255,0.3); }
</style>
@endif
