@if(isset($items) && $items->count())
<div class="container-fluid px-0 portal-section">
    <div id="portalCarousel" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-inner">
            @foreach($items->chunk(6) as $chunkIndex => $chunk)
                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                    <div class="d-flex flex-nowrap">

                        @foreach($chunk as $loopIndex => $item)
                            @php
                                $isFirst = ($chunkIndex === 0 && $loopIndex === 0);

                                $href = match($item->type) {
                                    'external' => $item->url,
                                    'file'     => asset('storage/' . ($item->file ?? '')),
                                    default    => $item->url ?? route('show.content', $item->slug),
                                };

                                $target   = ($item->opens_new_tab || $item->type === 'external') ? '_blank' : '_self';
                                $fileIcon = $item->file_icon ?? null;
                                $icon     = $item->icon ?? null;
                            @endphp

                            <div class="portal-cell border-end {{ $isFirst ? 'portal-cell--active' : '' }}">
                                <div class="p-3 text-center h-100">
                                    <a href="{{ $href }}"
                                       target="{{ $target }}"
                                       @if($target === '_blank') rel="noopener" @endif>

                                        <div class="portal-icon-wrap">
                                            @if($fileIcon)
                                                <img src="{{ asset('storage/' . $fileIcon) }}"
                                                     alt="{{ $item->name }}"
                                                     class="img-fluid"
                                                     style="max-height:60px;">
                                            @elseif($icon && !Str::startsWith($icon, 'fa'))
                                                <img src="{{ asset($icon) }}"
                                                     alt="{{ $item->name }}"
                                                     class="img-fluid"
                                                     style="max-height:60px;">
                                            @elseif($icon)
                                                <i class="{{ $icon }} fa-2x"></i>
                                            @else
                                                <i class="fa fa-link fa-2x"></i>
                                            @endif
                                        </div>

                                        <h6 class="portal-label mb-0">{{ $item->name }}</h6>

                                    </a>
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>

        @if($items->count() > 6)
        <div class="servicion_carsousel">
            <button class="carousel-control-prev" type="button"
                    data-bs-target="#portalCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button"
                    data-bs-target="#portalCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        @endif

    </div>
</div>

<style>
.portal-section { border-top: 1px solid #dee2e6; border-bottom: 1px solid #dee2e6; }
.portal-cell {
    flex: 0 0 16.6666%; max-width: 16.6666%; min-width: 0;
}
.portal-cell a { display: block; text-decoration: none; color: inherit; }
.portal-cell a:hover { opacity: 0.8; }
.portal-icon-wrap {
    height: 70px; display: flex;
    align-items: center; justify-content: center;
    margin-bottom: 10px;
}
.portal-label { font-size: 13px; line-height: 1.3; color: #222; }
.portal-cell--active { background-color: #1a2e4a; }
.portal-cell--active .portal-label,
.portal-cell--active a { color: #fff !important; }
.portal-cell--active img { filter: brightness(0) invert(1); }
.portal-cell--active i { color: #fff; }
.servicion_carsousel {
    position: absolute; bottom: 8px; right: 12px;
    display: flex; gap: 6px;
}
.servicion_carsousel .carousel-control-prev,
.servicion_carsousel .carousel-control-next {
    position: static; width: 28px; height: 28px;
    background: #1a2e4a; border-radius: 50%;
    opacity: 1;
}
</style>
@endif
