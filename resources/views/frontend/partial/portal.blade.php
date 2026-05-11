@if(isset($items) && $items->count())

<div class="container-fluid px-0 portal-section">
    <div id="portalCarousel" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-inner">

            @foreach($items->chunk(6) as $chunkIndex => $chunk)

                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">

                    <div class="row g-0">

                        @foreach($chunk as $loopIndex => $item)
                            @if($item->parent_id!=NULL)
                            @php
                                $isFirst = ($chunkIndex === 0 && $loopIndex === 0);

                                if ($item->type === 'external' && !empty($item->url)) {
                                    $href = $item->url;
                                } elseif ($item->type === 'file' && !empty($item->file)) {
                                    $href = asset('storage/' . $item->file);
                                } elseif (!empty($item->slug)) {
                                    $href = route('show.content', $item->slug);
                                } else {
                                    $href = '#';
                                }

                                $target = ($item->opens_new_tab || in_array($item->type, ['external', 'file']))
                                    ? '_blank'
                                    : '_self';

                                $fileIcon = $item->file_icon ?? null;
                                $icon = $item->icon ?? null;
                            @endphp

                            <div class="col-6 col-sm-4 col-md-2 portal-cell border-end {{ $isFirst ? 'portal-cell--active' : '' }}">

                                <div class="p-3 text-center h-100">

                                    <a href="{{ $href }}"
                                       target="{{ $target }}"
                                       @if($target === '_blank') rel="noopener" @endif>

                                        <div class="portal-icon-wrap">

                                            @if($fileIcon)
                                                <img
                                                    src="{{ asset('storage/' . $fileIcon) }}"
                                                    alt="{{ $item->name }}"
                                                    class="img-fluid"
                                                >
                                            @elseif($icon && \Illuminate\Support\Str::startsWith($icon, 'fa'))
                                                <i class="{{ $icon }} fa-2x"></i>
                                            @else
                                                <i class="fa fa-link fa-2x"></i>
                                            @endif

                                        </div>

                                        <h6 class="portal-label mb-0">
                                            {{ $item->name }}
                                        </h6>

                                    </a>

                                </div>

                            </div>
@endif
                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

        @if($items->count() > 6)
            <div class="servicion_carsousel">

                <button class="carousel-control-prev"
                        type="button"
                        data-bs-target="#portalCarousel"
                        data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>

                <button class="carousel-control-next"
                        type="button"
                        data-bs-target="#portalCarousel"
                        data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>

            </div>
        @endif

    </div>
</div>

<style>
.portal-section {
    position: relative;
    border-top: 1px solid #dee2e6;
    border-bottom: 1px solid #dee2e6;
    background: #fff;
}

.portal-cell {
    min-height: 135px;
    background: #fff;
}

.portal-cell a {
    display: block;
    text-decoration: none;
    color: inherit;
    height: 100%;
}

.portal-cell a:hover {
    opacity: 0.85;
}

.portal-icon-wrap {
    height: 70px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
}

.portal-icon-wrap img {
    max-height: 60px;
    max-width: 80px;
    object-fit: contain;
}

.portal-label {
    font-size: 13px;
    line-height: 1.35;
    color: #222;
    font-weight: 600;
}

.portal-cell--active {
    background-color: #1a2e4a;
}

.portal-cell--active .portal-label,
.portal-cell--active a,
.portal-cell--active i {
    color: #fff !important;
}

.portal-cell--active img {
    filter: brightness(0) invert(1);
}

.servicion_carsousel {
    position: absolute;
    right: 12px;
    bottom: 8px;
    z-index: 5;
    display: flex;
    gap: 6px;
}

.servicion_carsousel .carousel-control-prev,
.servicion_carsousel .carousel-control-next {
    position: static;
    width: 30px;
    height: 30px;
    background: #1a2e4a;
    border-radius: 50%;
    opacity: 1;
}

.servicion_carsousel .carousel-control-prev-icon,
.servicion_carsousel .carousel-control-next-icon {
    width: 14px;
    height: 14px;
}

@media (max-width: 767px) {
    .portal-cell {
        min-height: 120px;
    }

    .portal-label {
        font-size: 12px;
    }
}
</style>

@endif