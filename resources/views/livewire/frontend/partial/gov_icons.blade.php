@if(isset($items) && $items->count())
<div class="container-fluid px-0 gov-icons-section border-bottom">
    <div id="govIconsCarousel" class="carousel slide" data-bs-ride="carousel">

        <div class="carousel-inner">
            @foreach($items->chunk(8) as $chunkIndex => $chunk)
                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                    <div class="d-flex flex-nowrap">

                        @foreach($chunk as $item)
                            @php
                                $href = match($item->type) {
                                    'external' => $item->url,
                                    'file'     => asset('storage/' . ($item->file ?? '')),
                                    default    => $item->url ?? route('show.content', $item->slug),
                                };
                                $target   = ($item->opens_new_tab || $item->type === 'external') ? '_blank' : '_self';
                                $fileIcon = $item->file_icon ?? null;
                                $icon     = $item->icon ?? null;
                            @endphp

                            <div class="gov-icon-cell border-end">
                                <div class="p-2 text-center">
                                    <a href="{{ $href }}" target="{{ $target }}"
                                       @if($target === '_blank') rel="noopener" @endif>

                                        <div class="gov-icon-wrap">
                                            @if($fileIcon)
                                                <img src="{{ asset('storage/' . $fileIcon) }}"
                                                     alt="{{ $item->name }}"
                                                     class="img-fluid"
                                                     style="max-height:50px;">
                                            @elseif($icon && !Str::startsWith($icon, 'fa'))
                                                <img src="{{ asset($icon) }}"
                                                     alt="{{ $item->name }}"
                                                     class="img-fluid"
                                                     style="max-height:50px;">
                                            @elseif($icon)
                                                <i class="{{ $icon }} fa-2x" style="color:#1a3a6b;"></i>
                                            @else
                                                <i class="fa fa-globe fa-2x" style="color:#1a3a6b;"></i>
                                            @endif
                                        </div>

                                    </a>
                                </div>
                            </div>

                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>

        @if($items->count() > 8)
            <div class="servicion_carsousel">
                <button class="carousel-control-prev" type="button"
                        data-bs-target="#govIconsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button"
                        data-bs-target="#govIconsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        @endif

    </div>
</div>

<style>
.gov-icons-section { background: #f8f9fa; border-top: 1px solid #dee2e6; }
.gov-icon-cell {
    flex: 0 0 12.5%; max-width: 12.5%; min-width: 0;
}
.gov-icon-cell a { display: block; text-decoration: none; }
.gov-icon-cell a:hover { opacity: 0.75; }
.gov-icon-wrap {
    height: 60px; display: flex;
    align-items: center; justify-content: center;
}
</style>
@endif
