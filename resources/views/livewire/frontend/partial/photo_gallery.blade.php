@if(isset($items) && $items->count())
<div class="pg-section">

    <h5 class="gallery-heading mb-3">
        <i class="fa fa-picture-o me-1"></i> Photo Gallery
    </h5>

    <div class="pg-carousel-wrapper">
        <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($items->chunk(1) as $ci => $chunk)
                    <div class="carousel-item {{ $ci === 0 ? 'active' : '' }}">
                        @foreach($chunk as $photo)
                            @php
                                $src = $photo->file
                                    ? asset('storage/' . $photo->file)
                                    : ($photo->file_icon
                                        ? asset('storage/' . $photo->file_icon)
                                        : asset('images/placeholder.jpg'));
                            @endphp
                            <img src="{{ $src }}"
                                 alt="{{ $photo->name }}"
                                 class="d-block w-100 pg-main-img">
                            <div class="pg-caption">{{ $photo->name }}</div>
                        @endforeach
                    </div>
                @endforeach
            </div>

            <div class="pg-nav">
                <button class="carousel-control-prev pg-btn" type="button"
                        data-bs-target="#photoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                    <span class="visually-hidden">Prev</span>
                </button>
                <button class="carousel-control-next pg-btn" type="button"
                        data-bs-target="#photoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </div>

</div>

<style>
.gallery-heading { color: #fff; font-size: 18px; font-weight: 700; }
.pg-carousel-wrapper { position: relative; border-radius: 6px; overflow: hidden; }
.pg-main-img { height: 260px; object-fit: cover; border-radius: 6px; }
.pg-caption {
    position: absolute; bottom: 0; left: 0; right: 0;
    background: rgba(0,0,0,0.55); color: #fff;
    font-size: 13px; padding: 8px 12px;
}
.pg-nav { position: absolute; bottom: 8px; right: 8px; display: flex; gap: 4px; }
.pg-btn {
    position: static; width: 28px; height: 28px;
    background: rgba(255,255,255,0.2); border-radius: 50%;
    border: 1px solid rgba(255,255,255,0.4); opacity: 1;
}
.pg-btn:hover { background: rgba(255,255,255,0.4); }
</style>
@endif
